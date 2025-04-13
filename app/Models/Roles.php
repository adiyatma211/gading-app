<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Roles extends Model
{
    /** @use HasFactory<\Database\Factories\RolesFactory> */
    use HasFactory;

    protected $guarded=['id'];
    public function users()
    {
        return $this->hasMany(User::class, 'role_id'); // Tentukan foreign key
    }

}
