<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class historynota extends Model
{
    /** @use HasFactory<\Database\Factories\HistorynotaFactory> */
    use HasFactory;


    protected $table = 'historynotas';
    protected $guarded=['id'];

}
