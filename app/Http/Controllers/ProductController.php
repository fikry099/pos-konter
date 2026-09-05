<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Helper privat untuk menentukan store_id cabang yang sedang aktif
     */
    private function getActiveStoreId()
    {
        return auth()->user()->store_id ?? session('selected_store_id') ?? 1;
    }

    /**
     * Menampilkan Daftar Seluruh Produk (Tanpa Pagination untuk Filter JS)
     */
    public function index(Request $request)
    {
        // Load relasi category beserta parent-nya agar breadcrumb hirarki dapat tampil
        $query = Product::with('category.parent');

        // Filter backend opsional jika ada parameter category_id
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // Pencarian Nama / Kode Produk di backend jika di-submit
        if ($request->has('search') && $request->search != '') {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(code) LIKE ?', ["%{$search}%"]);
            });
        }

        // AMBIL SEMUA DATA DENGAN ->get() AGAR JS BISA MEMFILTER SEMUA ITEM
        $products   = $query->latest()->get();
        
        // Ambil hanya parent utama (parent_id null) dengan semua anak-anaknya secara rekursif
        $categories = Category::whereNull('parent_id')->with('allChildren')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Menampilkan Form Tambah Produk
     */
    public function create()
    {
        $categories = Category::whereNull('parent_id')->with('allChildren')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Menyimpan Produk Baru ke Database (Beserta Inisialisasi Stok Cabang Aktif)
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'name'          => 'required|string|max:255',
            'code'          => 'nullable|string|max:50|unique:products,code',
            'type'          => 'required|in:physical,digital',
            'cost_price'    => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock'         => 'required_if:type,physical|numeric|min:0',
            'min_stock'     => 'required_if:type,physical|numeric|min:0',
        ]);

        // 1. Buat Master Produk
        $product = Product::create([
            'category_id'   => $request->category_id,
            'name'          => $request->name,
            'code'          => $request->code ?? 'PRD-' . strtoupper(Str::random(6)),
            'type'          => $request->type,
            'cost_price'    => $request->cost_price,
            'selling_price' => $request->selling_price,
            'stock'         => 0, // Master katalog tidak menyimpan angka stok
            'min_stock'     => 0,
            'is_active'     => $request->has('is_active'),
        ]);

        // 2. Jika Barang Fisik, Daftarkan Stoknya di Cabang Aktif saat ini
        if ($request->type === 'physical') {
            $storeId = $this->getActiveStoreId();

            StoreProductStock::create([
                'store_id'   => $storeId,
                'product_id' => $product->id,
                'stock'      => $request->stock,
                'min_stock'  => $request->min_stock,
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan dan stok cabang telah terdaftar!');
    }

    /**
     * Menampilkan Form Edit Produk
     */
    public function edit($id)
    {
        $product    = Product::findOrFail($id);
        $categories = Category::whereNull('parent_id')->with('allChildren')->get();
        
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Mengubah Data Produk di Database
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'name'          => 'required|string|max:255',
            'code'          => 'nullable|string|max:50|unique:products,code,' . $id,
            'type'          => 'required|in:physical,digital',
            'cost_price'    => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
        ]);

        $product->update([
            'category_id'   => $request->category_id,
            'name'          => $request->name,
            'code'          => $request->code,
            'type'          => $request->type,
            'cost_price'    => $request->cost_price,
            'selling_price' => $request->selling_price,
            'is_active'     => $request->has('is_active'),
        ]);

        return redirect()->route('products.index')->with('success', 'Data produk berhasil diperbarui!');
    }

    /**
     * Menghapus Produk
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Hapus relasi stok fisik cabang terkait
        StoreProductStock::where('product_id', $product->id)->delete();
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }

    /**
     * Menampilkan Rekomendasi Restok / Order Voucher & Barang Fisik (Grosir Owner Per Cabang)
     * KONDISI: Tampil HANYA JIKA sisa stok < stok target (min_stock)
     */
    public function reorderOrder()
    {
        $storeId = $this->getActiveStoreId();

        // Ambil stok produk fisik cabang ini yang sisa stoknya LEBIH KECIL dari target (min_stock)
        $lowStockProducts = StoreProductStock::with(['product.category.parent'])
            ->where('store_id', $storeId)
            ->whereHas('product', function ($query) {
                $query->where('type', 'physical')
                    ->where('is_active', true);
            })
            ->whereColumn('stock', '<', 'min_stock') // HANYA TAMPIL JIKA SISA STOK < MIN_STOCK / TARGET STOK
            ->get();

        return view('products.reorder', compact('lowStockProducts'));
    }

    /**
     * Menampilkan Halaman Restok Khusus Karyawan & Owner (Hanya Tambah Qty Stok Cabang Aktif)
     */
    public function restockView()
    {
        $storeId = $this->getActiveStoreId();

        // 1. Mengambil seluruh stok produk fisik cabang ini (beserta relasi produk & kategori)
        $stocks = StoreProductStock::with(['product.category.parent.parent'])
            ->where('store_id', $storeId)
            ->whereHas('product', function ($q) {
                $q->where('type', 'physical')->where('is_active', true);
            })
            ->get();

        // Alias agar tidak error jika ada view partial yang memanggil $products
        $products = $stocks;

        // 2. Ambil kategori berjenjang
        $categories = Category::whereNull('parent_id')
            ->with('allChildren')
            ->get();

        return view('products.restock', compact('stocks', 'products', 'categories'));
    }

    /**
     * Memproses Penambahan Kuantitas Stok dari Form Restok Karyawan ke Cabang Aktif
     */
    public function processRestock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty_add'    => 'required|numeric|min:1',
            'notes'      => 'nullable|string|max:255',
        ]);

        $storeId = $this->getActiveStoreId();

        // Cari record stok fisik cabang ini, jika belum ada otomatis dibuatkan
        $storeStock = StoreProductStock::firstOrCreate(
            [
                'store_id'   => $storeId,
                'product_id' => $request->product_id,
            ],
            [
                'stock'     => 0,
                'min_stock' => 5,
            ]
        );

        // Increment stok fisik cabang aktif
        $storeStock->increment('stock', $request->qty_add);

        return redirect()->back()->with('success', "Berhasil menambahkan {$request->qty_add} pcs stok untuk {$storeStock->product->name}. Stok cabang sekarang: {$storeStock->stock} pcs.");
    }
}