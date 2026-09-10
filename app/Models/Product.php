<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'code',
        'image',
        'type',
        'cost_price',
        'selling_price',
        'stock',
        'min_stock',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relasi: Produk milik 1 Kategori
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi: Produk bisa ada di banyak detail transaksi
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // Relasi Stok Cabang
    public function storeStocks(): HasMany
    {
        return $this->hasMany(StoreProductStock::class);
    }

    // ALIAS DIBUTUHKAN CONTROLLER (agar $query->with('stocks') tidak error)
    public function stocks(): HasMany
    {
        return $this->storeStocks();
    }

    // Helper mengambil stok spesifik cabang tertentu
    public function getStockForStore($storeId)
    {
        if ($this->type === 'digital') {
            return 0; // Digital bebas stok
        }

        $storeStock = $this->storeStocks()->where('store_id', $storeId)->first();
        return $storeStock ? $storeStock->stock : 0;
    }

    // Helper mengambil record stok & harga spesifik cabang
    public function getStoreStockRecord($storeId)
    {
        return $this->storeStocks()->where('store_id', $storeId)->first();
    }

    // Helper mengambil harga jual spesifik cabang (fallback ke katalog jika null)
    public function getSellingPriceForStore($storeId)
    {
        $storeStock = $this->getStoreStockRecord($storeId);
        return ($storeStock && $storeStock->selling_price !== null) 
            ? $storeStock->selling_price 
            : $this->selling_price;
    }

    // Helper mengambil harga modal spesifik cabang (fallback ke katalog jika null)
    public function getCostPriceForStore($storeId)
    {
        $storeStock = $this->getStoreStockRecord($storeId);
        return ($storeStock && $storeStock->cost_price !== null) 
            ? $storeStock->cost_price 
            : $this->cost_price;
    }
}