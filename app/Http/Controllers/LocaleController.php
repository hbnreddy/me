<?php

namespace App\Http\Controllers;

use App\AppCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class LocaleController extends Controller
{
    public function index()
    {
        $locales = AppCache::languages()
            ->pluck('code')
            ->toArray();

        $keys = [];
        $entities = [];
        foreach ($locales as $locale) {
            $path = resource_path('lang\\'.$locale.'.json');

            if (! file_exists($path)) {
                File::put($path, json_encode([]));
            }

            $fileContents = [];
            if (is_string($path) && is_readable($path)) {
                $fileContents = json_decode(file_get_contents($path), true);
            }

            $keys = array_merge($keys, array_keys($fileContents));
            $entities[$locale] = $fileContents;
        }

        foreach ($entities as &$entity) {
            foreach ($keys as $key) {
                if (! isset($entity[$key])) {
                    $entity[$key] = '';
                }
            }

            ksort($entity);
        }

        $response = [
            'success' => true,
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $request->validate([
            'locales' => 'required|array',
        ]);

        $keys = [];
        $locales = $request->input('locales');
        foreach ($locales as $locale) {
            $keys = array_merge($keys, array_keys($locale));
        }

        foreach ($locales as &$entity) {
            foreach ($keys as $key) {
                if (! isset($entity[$key])) {
                    $entity[$key] = '';
                }
            }

            ksort($entity);
        }

        $entities = [];
        foreach ($locales as $locale => $data) {
            $path = resource_path('lang\\'.$locale.'.json');

            if (! file_exists($path)) {
                File::put($path, json_encode($data));
            }

            if (is_string($path) && is_readable($path)) {
                File::put($path, json_encode($data));

                $fileContents = json_decode(file_get_contents($path), true);
                $entities[$locale] = $fileContents;
            }
        }

        Cache::forget('translations');

        $response = [
            'success' => true,
            'entities' => $entities,
        ];

        return response()->json($response);
    }
}
