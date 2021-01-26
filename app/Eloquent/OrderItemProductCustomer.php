<?php

namespace App\Eloquent;

class OrderItemProductCustomer extends Model
{
    protected $fillable = [
        'customer_id',
        'order_item_product_id',
    ];
}
