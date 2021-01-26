<?php

namespace App\Traits;

trait Fails
{
    public function jsonFails($message = '')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ]);
    }
}
