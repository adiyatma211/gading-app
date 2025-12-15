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

    protected $casts = [
        'harga_per_meter' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
