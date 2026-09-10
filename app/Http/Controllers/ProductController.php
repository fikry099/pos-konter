<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Exports\ProductStockExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Helper privat untuk menentukan store_id cabang yang sedang aktif
     */
    private function getActiveStoreId()
    {
        return session('selected_store_id') ?? auth()->user()->store_id ?? 1;
    }

    /**
     * Menampilkan Daftar Seluruh Produk (Beserta Stok & Harga Cabang Aktif)
     */
    public function index(Request $request)
    {
        $storeId = $this->getActiveStoreId();

        // Load relasi category dan stok khusus cabang aktif
        $query = Product::with([
            'category.parent',
            'stocks' => function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            }
        ]);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(products.name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(products.code) LIKE ?', ["%{$search}%"]);
            });
        }

        $products = $query->latest()->get();

        // Overwrite harga modal & harga jual objek $product dengan harga khusus cabang aktif
        $products->transform(function ($product) {
            $storeStock = $product->stocks->first();
            if ($storeStock) {
                if ($storeStock->selling_price !== null) {
                    $product->selling_price = $storeStock->selling_price;
                }
                if ($storeStock->cost_price !== null) {
                    $product->cost_price = $storeStock->cost_price;
                }
            }
            return $product;
        });

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
     * Menyimpan Produk Baru ke Database (Beserta Inisialisasi Stok & Harga Cabang Aktif)
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
            'stock'         => 'required_if:type,physical|nullable|numeric|min:0',
            'min_stock'     => 'required_if:type,physical|nullable|numeric|min:0',
        ]);

        // 1. Buat Master Katalog Produk
        $product = Product::create([
            'category_id'   => $request->category_id,
            'name'          => $request->name,
            'code'          => $request->code ?? 'PRD-' . strtoupper(Str::random(6)),
            'type'          => $request->type,
            'cost_price'    => $request->cost_price,
            'selling_price' => $request->selling_price,
            'stock'         => 0,
            'min_stock'     => 0,
            'is_active'     => $request->has('is_active'),
        ]);

        // 2. Daftarkan Stok & Harga ke Cabang Aktif Saat Ini (Baik Fisik Maupun Digital)
        $storeId = $this->getActiveStoreId();

        StoreProductStock::create([
            'store_id'      => $storeId,
            'product_id'    => $product->id,
            'stock'         => $request->type === 'physical' ? ($request->stock ?? 0) : 0,
            'min_stock'     => $request->type === 'physical' ? ($request->min_stock ?? 5) : 0,
            'cost_price'    => $request->cost_price,
            'selling_price' => $request->selling_price,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan dan stok/harga cabang telah terdaftar!');
    }

    /**
     * Menampilkan Form Edit Produk (MEMUAT STOK & HARGA CABANG AKTIF)
     */
    public function edit($id)
    {
        $product    = Product::findOrFail($id);
        $categories = Category::whereNull('parent_id')->with('allChildren')->get();
        $storeId    = $this->getActiveStoreId();

        $storeStock = StoreProductStock::where('store_id', $storeId)
            ->where('product_id', $product->id)
            ->first();

        // Tempelkan nilai stok & harga spesifik cabang ke objek $product
        $product->current_store_stock = $storeStock ? $storeStock->stock : 0;
        $product->current_min_stock   = $storeStock ? $storeStock->min_stock : 5;
        
        if ($storeStock) {
            if ($storeStock->selling_price !== null) $product->selling_price = $storeStock->selling_price;
            if ($storeStock->cost_price !== null) $product->cost_price = $storeStock->cost_price;
        }

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Mengubah Data Produk & Stok/Harga Cabang di Database
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
            'stock'         => 'required_if:type,physical|nullable|numeric|min:0',
            'min_stock'     => 'required_if:type,physical|nullable|numeric|min:0',
        ]);

        // 1. Update Master Informasi Produk (Tanpa Merubah Harga Global Cabang Lain)
        $product->update([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'code'        => $request->code,
            'type'        => $request->type,
            'is_active'   => $request->has('is_active'),
        ]);

        // 2. Update Stok & Harga Khusus Cabang Aktif Saat Ini (Semua Jenis Barang)
        $storeId = $this->getActiveStoreId();

        StoreProductStock::updateOrCreate(
            [
                'store_id'   => $storeId,
                'product_id' => $product->id,
            ],
            [
                'stock'         => $request->type === 'physical' ? ($request->stock ?? 0) : 0,
                'min_stock'     => $request->type === 'physical' ? ($request->min_stock ?? 5) : 0,
                'cost_price'    => $request->cost_price,    // Spesifik Cabang Aktif
                'selling_price' => $request->selling_price, // Spesifik Cabang Aktif
            ]
        );

        return redirect()->route('products.index')->with('success', 'Data produk dan harga khusus cabang berhasil diperbarui!');
    }

    /**
     * Menghapus Produk
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        StoreProductStock::where('product_id', $product->id)->delete();
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }

    /**
     * Menampilkan Rekomendasi Restok / Order Voucher & Barang Fisik
     */
    public function reorderOrder()
    {
        $storeId = $this->getActiveStoreId();

        $lowStockProducts = StoreProductStock::with([
                'product' => function ($qp) {
                    $qp->select('id', 'category_id', 'name', 'code', 'type', 'selling_price', 'cost_price')
                       ->with('category:id,parent_id,name,slug');
                }
            ])
            ->where('store_id', $storeId)
            ->whereHas('product', function ($query) {
                $query->where('type', 'physical')
                    ->where('is_active', true)
                    ->whereHas('category', function ($catQuery) {
                        $catQuery->where(function ($q) {
                            $q->whereIn('slug', ['voucher-internet', 'kartu-perdana'])
                              ->orWhere('name', 'LIKE', '%Voucher%')
                              ->orWhere('name', 'LIKE', '%Perdana%')
                              ->orWhereHas('parent', function ($parentQuery) {
                                  $parentQuery->whereIn('slug', ['voucher-internet', 'kartu-perdana'])
                                              ->orWhere('name', 'LIKE', '%Voucher%')
                                              ->orWhere('name', 'LIKE', '%Perdana%');
                              });
                        });
                    });
            })
            ->whereColumn('stock', '<', 'min_stock')
            ->get();

        if ($lowStockProducts->isEmpty()) {
            session()->flash('info', 'Semua stok voucher & kartu perdana di cabang ini masih mencukupi.');
        }

        return view('products.reorder', compact('lowStockProducts'));
    }

    /**
     * Menampilkan Halaman Restok Khusus Karyawan & Owner
     */
    public function restockView()
    {
        $storeId = $this->getActiveStoreId();

        $stocks = StoreProductStock::with([
                'product' => function ($q) {
                    $q->select('id', 'category_id', 'name', 'code', 'type', 'selling_price', 'is_active')
                       ->with('category:id,name,parent_id');
                }
            ])
            ->where('store_id', $storeId)
            ->whereHas('product', function ($q) {
                $q->where('type', 'physical')->where('is_active', true);
            })
            ->get();

        $products = $stocks;

        $categories = Category::whereNull('parent_id')
            ->select('id', 'name', 'slug')
            ->with('allChildren:id,parent_id,name,slug')
            ->get();

        return view('products.restock', compact('stocks', 'products', 'categories'));
    }

    /**
     * Memproses Penambahan Kuantitas Stok
     */
    public function processRestock(Request $request)
    {
        $qtyAdd = $request->input('qty_add') ?? $request->input('quantity');
        $request->merge(['qty_add' => $qtyAdd]);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty_add'    => 'required|numeric|min:1',
            'notes'      => 'nullable|string|max:255',
        ]);

        $storeId = $this->getActiveStoreId();

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

        $storeStock->increment('stock', $qtyAdd);
        $storeStock->refresh();

        $product = Product::find($request->product_id);
        $costPrice = $product ? (float) $product->cost_price : 0;
        $totalCost = $costPrice * (int) $qtyAdd;

        \App\Models\Restock::create([
            'store_id'   => $storeId,
            'product_id' => $request->product_id,
            'user_id'    => auth()->id(),
            'qty_add'    => (int) $qtyAdd,
            'cost_price' => $costPrice,
            'total_cost' => $totalCost,
            'notes'      => $request->notes,
        ]);

        $message = "Berhasil menambahkan {$qtyAdd} pcs stok untuk {$product->name}. Stok cabang sekarang: {$storeStock->stock} pcs.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'message'   => $message,
                'new_stock' => $storeStock->stock,
                'product'   => [
                    'id'   => $product->id,
                    'name' => $product->name,
                ]
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Ekspor Laporan Stok ke Excel (.xlsx)
     */
    public function exportExcel(Request $request)
    {
        $storeId = $this->getActiveStoreId();

        $query = StoreProductStock::with([
                'product' => function ($q) {
                    $q->select('id', 'category_id', 'name', 'code', 'type', 'selling_price', 'is_active')
                       ->with('category.parent');
                }
            ])
            ->where('store_id', $storeId)
            ->whereHas('product', function ($q) {
                $q->where('type', 'physical')->where('is_active', true);
            });

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $catKey = strtolower($request->category);
            $query->whereHas('product.category', function ($q) use ($catKey) {
                $q->where('slug', 'like', "%{$catKey}%")
                  ->orWhere('name', 'like', "%{$catKey}%")
                  ->orWhereHas('parent', function ($qp) use ($catKey) {
                      $qp->where('slug', 'like', "%{$catKey}%")
                        ->orWhere('name', 'like', "%{$catKey}%");
                  });
            });
        }

        if ($request->filled('sub_filter')) {
            $subKey = strtolower($request->sub_filter);
            $query->whereHas('product', function ($q) use ($subKey) {
                $q->where('name', 'like', "%{$subKey}%")
                  ->orWhereHas('category', function ($qc) use ($subKey) {
                      $qc->where('slug', 'like', "%{$subKey}%")
                        ->orWhere('name', 'like', "%{$subKey}%");
                  });
            });
        }

        $stocks = $query->latest()->get();

        $products = $stocks->map(function ($stock) {
            $prod = $stock->product;
            if ($prod) {
                $prod->stock = $stock->stock;
                if ($stock->selling_price !== null) {
                    $prod->selling_price = $stock->selling_price;
                }
            }
            return $prod;
        })->filter();

        $fileName = 'Laporan_Stok_' . ucfirst($request->category ?? 'Semua') . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new ProductStockExport($products), $fileName);
    }
}