<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductReturn extends Model
{
    // Arahkan tabel secara eksplisit
    protected $table = 'returns';

    protected $fillable = [
        'store_id',
        'transaction_id',
        'user_id',
        'shift_id',
        'return_code',
        'returned_total',
        'replacement_total',
        'price_difference',
        'payment_method',
        'reason',
    ];

    protected $casts = [
        'returned_total'    => 'decimal:2',
        'replacement_total' => 'decimal:2',
        'price_difference'  => 'decimal:2',
    ];

    /* =========================================================================
     * RELASI ELOQUENT
     * ========================================================================= */

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
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
        return $this->hasMany(ReturnDetail::class, 'return_id');
    }

    /* =========================================================================
     * SCOPE FILTER CABANG
     * ========================================================================= */

    public function scopeForStore($query, $storeId)
    {
        if ($storeId) {
            return $query->where('store_id', $storeId);
        }
        return $query;
    }
}