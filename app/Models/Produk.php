<?php

namespace App\Models;

use App\Models\ProdukBahan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    /** @use HasFactory<\Database\Factories\ProdukFactory> */
    use HasFactory;

    protected $guarded=['id'];
    public function bahan()
    {
        return $this->hasMany(ProdukBahan::class, 'produk_id');
    }
    public function hargas()
    {
        return $this->hasMany(HargaProdukNew::class, 'produk_id', 'id');
    }
}
