<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesProduct extends Model
{
    use HasFactory;

    protected $table = 'sales_product';

    protected $fillable = [
        'id_conta',
        'customer_id',
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount',
        'total_price',
        'billingType',
        'dueDate',

    ];

    // Relacionamentos
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
