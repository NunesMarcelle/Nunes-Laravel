<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_conta',
        'customer_id',
        'billing_type',
        'next_due_date',
        'value',
        'cycle',
        'description',
        'status',
    ];

   
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
