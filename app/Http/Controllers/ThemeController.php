<?php

namespace App\Http\Controllers;

use App\Eloquent\Theme;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index()
    {
        $entities = Theme::all();

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function store(Request $request, $id = false)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'string|nullable',
        ]);

        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ];

        $entity = Theme::query()
            ->updateOrCreate(
                [
                    'id' => $id,
                ],
                $data);

        $response = [
            'success' => !!$entity,
            'entity' => $entity,
        ];

        return response()->json($response);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $success = Theme::query()
            ->find($request->input('id'))
            ->delete();

        $response = [
            'success' => $success,
        ];

        return response()->json($response);
    }
}
