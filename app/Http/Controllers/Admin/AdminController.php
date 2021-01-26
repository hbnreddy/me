<?php

namespace App\Http\Controllers\Admin;

use App\AppCache;
use App\AppConfig;
use App\Eloquent\City;
use App\Eloquent\Continent;
use App\Eloquent\Country;
use App\Eloquent\Marker;
use App\Eloquent\Place;
use App\Eloquent\Theme;
use App\Http\Controllers\Controller;
use App\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $data = [
            'locale' => AppConfig::getLocale(),
        ];

        return view('admin.dashboard', compact('data'));
    }

    public function statistics()
    {
        $statistics = [
            'total_users' => User::query()->count(),
            'total_places' => Place::query()->count(),
            'total_themes' => Theme::query()->count(),
            'total_continents' => Continent::query()->count(),
            'active_continents' => Continent::query()
                ->where('enabled', 1)
                ->count(),
            'total_countries' => Country::query()->count(),
            'active_countries' => Country::query()
                ->where('enabled', 1)
                ->count(),
            'total_cities' => City::query()->count(),
            'active_cities' => City::query()
                ->where('enabled', 1)
                ->count(),
            'total_markers' => Marker::query()->count(),
            'assigned_markers' => Marker::query()
                ->where('theme_id', '!=', 0)
                ->count(),
        ];

        $response = [
            'entities' => $statistics,
        ];

        return response()->json($response);
    }
}
