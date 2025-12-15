<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function settings()
    {
        return $this->hasOne(AttendanceSetting::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}

