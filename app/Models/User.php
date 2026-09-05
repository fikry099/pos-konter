<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'store_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* =========================================================================
     * HELPER PENGECEKAN ROLE
     * ========================================================================= */

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isCabang(): bool
    {
        return $this->role === 'cabang';
    }

    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }

    /* =========================================================================
     * RELASI ELOQUENT
     * ========================================================================= */

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}