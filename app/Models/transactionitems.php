<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transactionitems extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionItemsFactory> */
    use HasFactory;
    protected $table = 'transaction_items';
    protected $guarded=['id'];


    public function produkBahan()
    {
        return $this->belongsTo(produkBahan::class, 'tipe_produk_id', 'id');
    }

}
