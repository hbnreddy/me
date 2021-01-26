<?php

namespace App\Eloquent;

class Theme extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function markers()
    {
        return $this->hasMany(Marker::class, 'theme_id');
    }
}
