<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessCode extends Model
{
    protected $fillable = [
        'code',
        'expire_date',
        'forever',
    ];
}
