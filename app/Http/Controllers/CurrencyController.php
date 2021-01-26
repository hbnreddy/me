<?php

namespace App\Http\Controllers;

use App\AppCache;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = AppCache::currencies();

        $response = [
            'success' => !!count($currencies),
            'entities' => $currencies,
        ];

        return response()->json($response);
    }
}
