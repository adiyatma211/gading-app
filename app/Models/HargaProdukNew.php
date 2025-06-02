<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HargaProdukNew extends Model
{
    use HasFactory;

    protected $table = 'harga_produk_new'; // Nama tabel yang benar

    // protected $fillable = [
    //     'produk_id',
    //     'min_qty',
    //     'max_qty',
    //     'sisi',
    //     'laminasi',
    //     'harga',
    //     'satuan',
    //     'diso',
    // ];

    protected $guarded =['id'];

    /**
     * Relasi ke model Produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'id');
    }
}
