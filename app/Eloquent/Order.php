<?php

namespace App\Eloquent;

class Order extends Model
{
    protected $fillable = [
        'name',
        'customer_id',
        'identifier',
        'musement_identifier',
        'musement_uuid',
        'currency',
        'total_price',
        'date',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
