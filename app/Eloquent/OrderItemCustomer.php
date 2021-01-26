<?php

namespace App\Eloquent;

class OrderItemCustomer extends Model
{
    protected $fillable = [
        'customer_id',
        'order_item_id',
    ];
}
