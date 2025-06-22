<?php

namespace App\Models;

use App\Models\ProdukBahan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class transactionitems extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionItemsFactory> */
    use HasFactory;
    protected $table = 'transaction_items';
    protected $guarded=['id'];


    public function produk()
    {
        return $this->belongsTo(Produk::class, 'tipe_produk_id', 'id');
    }
    // public function produk()
    // {
    //     return $this->belongsTo(Produk::class, 'tipe_produk_id');
    // }

}
