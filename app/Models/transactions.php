<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transactions extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionsFactory> */
    use HasFactory;
    protected $guarded=['id'];

    public function customer()
    {
        return $this->belongsTo(customers::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(transactionitems::class, 'transaction_id', 'id');
    }

    public function produkBahan()
    {
        return $this->belongsTo(ProdukBahan::class, 'tipe_produk_id', 'id');
    }

}
