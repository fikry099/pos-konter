<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_id',
        'served_by_user_id', // BARU
        'target_phone',
        'digital_provider',
        'qty',
        'cost_price',
        'selling_price',
        'subtotal',
        'profit',
    ];

    protected $casts = [
        'cost_price'    => 'decimal:2',
        'selling_price' => 'decimal:2',
        'subtotal'      => 'decimal:2',
        'profit'        => 'decimal:2',
    ];

    // Relasi: Detail milik 1 Transaksi
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relasi: Detail mengacu pada 1 Produk
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // RELASI BARU: Karyawan penanggung jawab aksesoris
    public function servedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by_user_id');
    }
}