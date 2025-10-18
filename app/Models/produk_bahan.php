<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class produk_bahan extends Model
{
    /** @use HasFactory<\Database\Factories\ProdukBahanFactory> */
    use HasFactory;
    protected $guarded=['id'];
}
