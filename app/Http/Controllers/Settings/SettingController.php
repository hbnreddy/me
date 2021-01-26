<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function get(Request $request)
    {
        $key = $request->input('key');

        $success = Storage::disk('local')->exists('settings.json');

        if (! $success) {
            Storage::disk('local')->put('settings.json', json_encode([]));
        }

        $settings = json_decode(Storage::disk('local')->get('settings.json'), true);

        if ($success && isset($key) && isset($settings[$key])) {
            $settings = $settings[$key];
        }

        $response = [
            'success' => $success,
            'settings' => $settings,
        ];

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function store(Request $request)
    {
        $request->validate([
            'text_boxes' => 'required|array',
        ]);

        $settingsFileExists = Storage::disk('local')->exists('settings.json');
        $settings = $settingsFileExists
            ? json_decode(Storage::disk('local')->get('settings.json'), true)
            : [];

        $settings['text_boxes'] = $request->input('text_boxes');

        $success = !!(Storage::disk('local')->put('settings.json', json_encode($settings)));

        $response = [
            'success' => $success,
        ];

        return response()->json($response);
    }
}
