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

    public static function getActiveShift($storeId = null)
    {
        if (!$storeId && auth()->check()) {
            $storeId = auth()->user()->store_id ?? session('selected_store_id');
        }

        $query = self::where('status', 'open');

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        return $query->first();
    }

    /**
     * Hitung Total Omset Penjualan Keseluruhan
     */
    public function getTotalSalesAttribute(): float
    {
        return (float) $this->transactions()->where('status', 'completed')->sum('total_price');
    }

    /**
     * Hitung Total Uang Tunai Murni yang MASUK ke Laci Kasir
     */
    public function getTotalCashSalesAttribute(): float
    {
        // 1. Transaksi Penjualan Tunai Biasa (Bukan Tarik Tunai)
        $regularCash = (float) $this->transactions()
            ->where('status', 'completed')
            ->whereIn('payment_method', ['cash', 'tunai'])
            ->where('invoice_code', 'NOT LIKE', 'WD-%')
            ->sum('total_price');

        // 2. Transaksi Tarik Tunai yang Admin-nya dibayar Tunai oleh Pelanggan
        $adminCash = (float) $this->transactions()
            ->where('status', 'completed')
            ->where('invoice_code', 'LIKE', 'WD-%')
            ->whereHas('details', function($qd) {
                $qd->whereRaw('LOWER(custom_name) LIKE ?', ['%admin tunai%'])
                   ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%admin cash%']);
            })
            ->sum('total_price');

        return $regularCash + $adminCash;
    }

    /**
     * Hitung Total Penjualan QRIS / Non-Tunai Murni
     */
    public function getTotalQrisSalesAttribute(): float
    {
        // 1. Transaksi QRIS / Transfer Biasa
        $regularQris = (float) $this->transactions()
            ->where('status', 'completed')
            ->whereIn('payment_method', ['qris', 'transfer'])
            ->where('invoice_code', 'NOT LIKE', 'WD-%')
            ->sum('total_price');

        // 2. Transaksi Tarik Tunai yang Admin-nya JUGAA dikirim via Transfer/QRIS
        $adminQris = (float) $this->transactions()
            ->where('status', 'completed')
            ->where('invoice_code', 'LIKE', 'WD-%')
            ->whereHas('details', function($qd) {
                $qd->whereRaw('LOWER(custom_name) LIKE ?', ['%admin transfer%'])
                   ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%admin qris%']);
            })
            ->sum('total_price');

        return $regularQris + $adminQris;
    }

    public function getTotalProfitAttribute(): float
    {
        return (float) $this->transactions()->where('status', 'completed')->sum('total_profit');
    }

    /**
     * Total Seluruh Uang Kas yang KELUAR dari Laci (Pengeluaran Operasional + Penyerahan Tarik Tunai)
     */
    public function getTotalExpensesAttribute(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    /**
     * Hitung Estimasi Uang Fisik Laci Kasir:
     * Modal Awal + Total Uang Tunai Masuk - Total Uang Tunai Keluar
     */
    public function calculateExpectedCash(): float
    {
        return ($this->cash_initial + $this->total_cash_sales) - $this->total_expenses;
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

    public function scopeForStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }
}