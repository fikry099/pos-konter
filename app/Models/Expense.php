<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'store_id',
        'shift_id',
        'user_id',
        'description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /* =========================================================================
     * RELASI ELOQUENT
     * ========================================================================= */

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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