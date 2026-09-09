<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpobServer extends Model
{
    protected $fillable = ['name', 'balance', 'is_active'];

    public function deposits()
    {
        return $this->hasMany(PpobDeposit::class);
    }
}