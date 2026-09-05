<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
    ];

    /**
     * Relasi: Satu Kategori punya Banyak Produk
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relasi: Mengambil Kategori Parent (Induk di atasnya)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relasi: Mengambil Sub-Kategori langsung di bawahnya
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Relasi Rekursif: Mengambil semua Sub-Kategori hingga level terdalam (anak & cucu)
     */
    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }
}