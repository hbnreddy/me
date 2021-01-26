<?php

namespace App\Http\Controllers;

use App\Eloquent\CityWeather;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityWeatherController extends Controller
{
    /**
     * @param $id
     * @return JsonResponse
     */
    public function weathers($id)
    {
        $entities = CityWeather::query()
            ->where('city_id', $id)
            ->get()
            ->keyBy('month');

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function store($id, Request $request)
    {
        $request->validate([
            'weathers' => 'required|array',
        ]);

        $weathers = $request->input('weathers');

        $success = true;
        foreach ($weathers as $weather) {
            $data = [
                'city_id' => $id,
                'month' => $weather['month'],
                'high' => $weather['high'],
                'low' => $weather['low'],
                'average' => $weather['average'],
                'precipitation' => $weather['precipitation'],
                'best_time' => $weather['best_time'],
            ];

            $weather = CityWeather::query()
                ->updateOrCreate([
                    'city_id' => $id,
                    'month' => $weather['month'],
                ], $data);

            if (! $weather) {
                $success = false;
            }
        }

        $response = [
            'success' => $success,
        ];

        return response()->json($response);
    }
}
