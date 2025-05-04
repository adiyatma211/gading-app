<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class histoypayment extends Model
{
    /** @use HasFactory<\Database\Factories\HistoyPaymentFactory> */
    use HasFactory;

    protected $table = 'history_payments';
    protected $guarded=['id'];
}
