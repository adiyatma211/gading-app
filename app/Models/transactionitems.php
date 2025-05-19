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


    public function produkBahan()
    {
        return $this->belongsTo(ProdukBahan::class, 'tipe_produk_id', 'id');
    }

}
