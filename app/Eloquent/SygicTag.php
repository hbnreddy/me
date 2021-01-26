<?php

namespace App\Eloquent;

class SygicTag extends Model
{
    protected $fillable = [
        'tag_key',
        'tag_count',
        'category_key',
        'category_count',
        'category_id',
    ];
}
