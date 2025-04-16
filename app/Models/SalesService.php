<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesService extends Model
{
    use HasFactory;

    protected $table = 'sales_service';

    protected $fillable = [
        'id_conta',
        'service_id',
        'customer_id',
        'price',
        'discount',
        'total_price',
    ];

public function customer()
{
    return $this->belongsTo(Customer::class, 'customer_id');
}

public function service()
{
    return $this->belongsTo(Service::class, 'service_id');
}
}
