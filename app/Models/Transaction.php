<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'store_id',
        'invoice_code',
        'user_id',
        'shift_id',
        'total_cost',
        'total_price',
        'total_profit',
        'pay_amount',
        'change_amount',
        'payment_method',
        'payment_proof',
        'status',           // 'completed' / 'cancelled'
        'cancel_reason',    // Alasan pembatalan
        'cancelled_by',     // User ID yang membatalkan
        'cancelled_at',     // Waktu pembatalan
    ];

    protected $casts = [
        'total_cost'    => 'decimal:2',
        'total_price'   => 'decimal:2',
        'total_profit'  => 'decimal:2',
        'pay_amount'    => 'decimal:2',
        'change_amount' => 'decimal:2',
        'cancelled_at'  => 'datetime',
    ];

    /* =========================================================================
     * RELASI ELOQUENT
     * ========================================================================= */

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /* =========================================================================
     * SCOPE QUERY FILTER
     * ========================================================================= */

    public function scopeForStore($query, $storeId)
    {
        if ($storeId) {
            return $query->where('store_id', $storeId);
        }
        return $query;
    }

    // Scope untuk menyaring hanya transaksi yang berhasil (bukan batal)
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}