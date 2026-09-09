<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpobDeposit extends Model
{
    protected $fillable = ['ppob_server_id', 'amount', 'notes', 'user_id'];

    public function server()
    {
        return $this->belongsTo(PpobServer::class, 'ppob_server_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}