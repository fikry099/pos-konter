<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreProductStock extends Model
{
    protected $fillable = [
        'store_id', 
        'product_id', 
        'stock', 
        'min_stock',
        'cost_price',    // Kolom harga modal spesifik cabang
        'selling_price', // Kolom harga jual spesifik cabang
    ];

    protected $casts = [
        'stock'         => 'integer',
        'min_stock'     => 'integer',
        'cost_price'    => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}