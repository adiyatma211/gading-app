<?php

namespace App\Models;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProdukBahan extends Model
{
    /** @use HasFactory<\Database\Factories\ProdukBahanFactory> */
    use HasFactory;

    protected $guarded=['id'];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
