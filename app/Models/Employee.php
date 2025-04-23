<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_conta', 'first_name', 'last_name', 'email', 'phone_number', 'status', 'employee_position'
    ];


}
