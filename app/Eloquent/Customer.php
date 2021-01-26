<?php

namespace App\Eloquent;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'type',
        'birthdate',
        'passport_start_date',
        'passport_end_date',
        'gender',
        'nationality',
        'mobile_number',
        'house_number',
        'street_number',
        'area',
        'city',
        'country',
        'pincode',
    ];
}
