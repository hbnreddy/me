<?php

namespace App\Http\Controllers;

use App\Eloquent\Theme;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * @return Factory|View
     * @throws FileNotFoundException
     */
    public function index()
    {
        $success = Storage::disk('local')->exists('settings.json');

        $textBoxes = [];
        if ($success) {
            $settings = json_decode(Storage::disk('local')->get('settings.json'), true);

            if (isset($settings['text_boxes'])) {
                $textBoxes = $settings['text_boxes'];
            }
        }

        $data = [
            'user' => Auth::user(),
            'query' => Cache::get('query') || false,
            'themes' => Theme::all(),
            'text_boxes' => $textBoxes,
        ];

        return view('welcome', compact('data'));
    }
}
