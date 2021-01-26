<?php

namespace App\Http\Controllers;

use App\Eloquent\City;
use App\Eloquent\Continent;
use App\Eloquent\Country;
use Illuminate\Http\Request;

class ContinentController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'state' => 'integer',
        ]);

        $query = Continent::query()
            ->with([
                'location',
                'bounds',
            ]);

        $state = $request->input('state');
        if (isset($state)) {
            $query = $query->where('enabled', boolval($state));
        }

        $entities = $query->get();

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function get($id)
    {
        $entity = Continent::query()
            ->with([
                'location',
                'bounds',
            ])->find($id);

        $response = [
            'success' => !!$entity,
            'entity' => $entity,
        ];

        return response()->json($response);
    }

    public function setState(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'state' => 'required|boolean',
        ]);

        $entity = Continent::query()->find($request->input('id'));

        $state = $request->input('state');
        $success = $entity->update([
            'enabled' => $request->input('state'),
        ]);

        if (! $state) {
            $countries = Country::query()
                ->where('continent_id', $entity->id)
                ->get();

            foreach ($countries as $country) {
                $cities = City::query()
                    ->where('country_id', $country->id)
                    ->get();

                foreach ($cities as $city) {
                    $city->update([
                        'enabled' => false,
                    ]);
                }

                $country->update([
                    'enabled' => false,
                ]);
            }
        }

        $response = [
            'success' => $success,
            'entity' => $entity,
        ];

        return response()->json($response);
    }
}
