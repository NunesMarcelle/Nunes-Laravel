<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'id_conta',
        'name',
        'description',
        'price',
        'amount',
        'min_amount',
        'status',
        'category',
    ];
}
