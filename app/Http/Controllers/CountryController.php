<?php

namespace App\Http\Controllers;

use App\Eloquent\City;
use App\Eloquent\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'state' => 'integer',
            'continent_id' => 'integer',
        ]);

        $query = Country::query()
            ->with([
                'location',
                'bounds',
            ]);

        $state = $request->input('state');
        if (isset($state)) {
            $query = $query->where('enabled', boolval($state));
        }

        $continentId = $request->input('continent_id');
        if (isset($continentId) && $continentId) {
            $query = $query->where('continent_id', $continentId);
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
        $entity = Country::query()
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

        $entity = Country::query()->find($request->input('id'));

        $state = $request->input('state');
        $success = $entity->update([
            'enabled' => $state,
        ]);

        if (! $state) {
            City::query()
                ->where('country_id', $entity->id)
                ->update([
                    'enabled' => false,
                ]);
        }

        $response = [
            'success' => $success,
            'entity' => $entity,
        ];

        return response()->json($response);
    }
}
