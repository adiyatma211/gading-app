<?php

namespace App\Models;

use App\Models\customers;
use App\Models\ProdukBahan;
use App\Models\transactionitems;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
