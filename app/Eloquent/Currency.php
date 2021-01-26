<?php

namespace App\Eloquent;

use App\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'code',
        'symbol',
    ];
}
