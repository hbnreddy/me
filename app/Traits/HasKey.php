<?php

namespace App\Traits;

trait HasKey
{
    public function getKey()
    {
        return $this->id;
    }
}
