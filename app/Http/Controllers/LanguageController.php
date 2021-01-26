<?php

namespace App\Http\Controllers;

use App\AppCache;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = AppCache::languages();

        $response = [
            'success' => !!count($languages),
            'entities' => $languages,
        ];

        return response()->json($response);
    }
}
