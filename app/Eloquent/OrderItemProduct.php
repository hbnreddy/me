<?php

namespace App\Eloquent;

class OrderItemProduct extends Model
{
    protected $fillable = [
        'order_item_id',
        'musement_product_id',
        'musement_order_item_id',
        'musement_transaction_code',
        'musement_type',
        'musement_activity_uuid',
        'title',
        'currency',
        'price',
        'status',
    ];
}
