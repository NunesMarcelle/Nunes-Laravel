<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_conta',
        'name',
        'email',
        'phone',
        'position',
        'salary',
        'access_level',
        'status',
    ];
}

