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
}