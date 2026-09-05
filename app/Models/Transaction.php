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
    ];

    protected $casts = [
        'total_cost'    => 'decimal:2',
        'total_price'   => 'decimal:2',
        'total_profit'  => 'decimal:2',
        'pay_amount'    => 'decimal:2',
        'change_amount' => 'decimal:2',
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

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /* =========================================================================
     * SCOPE QUERY FILTER CABANG
     * ========================================================================= */

    public function scopeForStore($query, $storeId)
    {
        if ($storeId) {
            return $query->where('store_id', $storeId);
        }
        return $query;
    }
}