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
     * Hitung Total Omset Penjualan Keseluruhan (Omset Murni / Fee Admin yang Hak Toko)
     */
    public function getTotalSalesAttribute(): float
    {
        return (float) $this->transactions()->where('status', 'completed')->sum('total_price');
    }

    /**
     * Hitung Net Arus Uang Murni yang MASUK ke LACI KASIR (Tunai Fisik)
     * Keterangan:
     * - Mengambil transaksi bertipe tunai/cash
     * - KECUALI transaksi Tarik Tunai (invoice_code WD-%) yang uang fisiknya KELUAR dari laci
     */
    public function getTotalCashSalesAttribute(): float
    {
        // 1. Transaksi Penjualan Tunai Biasa (Uang Masuk ke Laci)
        $regularCash = (float) $this->transactions()
            ->where('status', 'completed')
            ->whereIn('payment_method', ['cash', 'tunai'])
            ->where('invoice_code', 'NOT LIKE', 'WD-%')
            ->sum('total_price');

        // 2. Transaksi Tarik Tunai dengan Admin Cash (Hanya Fee Admin Tunai yang Masuk ke Laci)
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
     * Hitung Penjualan QRIS / Non-Tunai
     */
    public function getTotalQrisSalesAttribute(): float
    {
        return (float) $this->transactions()
            ->where('status', 'completed')
            ->where('payment_method', 'qris')
            ->sum('total_price');
    }

    public function getTotalProfitAttribute(): float
    {
        return (float) $this->transactions()->where('status', 'completed')->sum('total_profit');
    }

    public function getTotalExpensesAttribute(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    /**
     * Hitung Estimasi Uang Fisik Laci Kasir (Modal Awal + Tunai Masuk Murni - Pengeluaran Kas)
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