<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_conta',
        'name',
        'email',
        'phone',
        'birth_date',
        'status',
        'asaas_id',
        'cpf',
    ];

}
