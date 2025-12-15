<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'check_in_on_time_until' => 'datetime:H:i:s',
        'check_in_last_allowed' => 'datetime:H:i:s',
        'check_out_earliest' => 'datetime:H:i:s',
        'check_out_latest' => 'datetime:H:i:s',
        'enable_weekends' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}

