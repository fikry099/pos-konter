<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $fillable = [
        'store_id',
        'user_id',
        'user_ids',
        'photo',
        'start_time',
        'end_time',
        'cash_initial',
        'cash_expected',
        'cash_actual',
        'difference',
        'status',
    ];

    protected $casts = [
        'user_ids'      => 'array',
        'start_time'    => 'datetime',
        'end_time'      => 'datetime',
        'cash_initial'  => 'decimal:2',
        'cash_expected' => 'decimal:2',
        'cash_actual'   => 'decimal:2',
        'difference'    => 'decimal:2',
    ];

    /* =========================================================================
     * RELASI ELOQUENT
     * ========================================================================= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /* =========================================================================
     * LOGIKA & HELPER METHODS
     * ========================================================================= */

    /**
     * Cek apakah ada Shift yang sedang aktif ('open') SPESIFIK per cabang.
     */
    public static function getActiveShift($storeId = null)
    {
        // Jika store_id tidak dikirim manual, otomatis ambil store_id milik user yang sedang login
        if (!$storeId && auth()->check()) {
            $storeId = auth()->user()->store_id ?? session('selected_store_id');
        }

        $query = self::where('status', 'open');

        // Filter wajib berdasarkan store_id cabang
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        return $query->first();
    }

    public function getTotalSalesAttribute(): float
    {
        return (float) $this->transactions()->sum('total_price');
    }

    public function getTotalProfitAttribute(): float
    {
        return (float) $this->transactions()->sum('total_profit');
    }

    public function getTotalExpensesAttribute(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function calculateExpectedCash(): float
    {
        return ($this->cash_initial + $this->total_sales) - $this->total_expenses;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        if (str_starts_with($this->photo, 'data:image')) {
            return $this->photo;
        }

        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
            return $this->photo;
        }

        $cleanPath = ltrim(str_replace('public/', '', $this->photo), '/');

        return asset('storage/' . $cleanPath);
    }

    public function getStaffNamesAttribute()
    {
        if ($this->user_ids && is_array($this->user_ids)) {
            return \App\Models\User::whereIn('id', $this->user_ids)->pluck('name')->implode(', ');
        }
        return $this->user->name ?? '-';
    }

    /**
     * Scope query untuk memfilter shift berdasarkan toko/cabang.
     */
    public function scopeForStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }
}