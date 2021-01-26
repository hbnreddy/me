<?php

namespace App\Traits;

trait HasExternalIds
{
    public function setExternalId($key, $value)
    {
        return $this->externalIds[$key] = $value;
    }

    public function getExternalId($key)
    {
        return $this->externalIds[$key];
    }
}
