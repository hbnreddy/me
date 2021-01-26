<?php

namespace App\Traits;

use App\Cart\Traveller;

trait ValidateEntity
{
    protected function travellerValidForCart($data)
    {
        foreach (Traveller::REQUIRED_FIELDS as $requiredField) {
            if (! isset($data[$requiredField])) {
                return false;
            }
        }

        return true;
    }
}
