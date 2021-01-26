<?php

namespace App\Http\Controllers;

use App\Imports\MarkersImport;
use App\Eloquent\Marker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MarkerController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $request->validate([
            'theme_id' => 'integer',
        ]);

        $query = Marker::query();
        $path = public_path('pictures/markers');

        $themeId = $request->input('theme_id');
        if (isset($themeId) && $themeId) {
            $query = $query->where('theme_id', $themeId);
        }

        $search = $request->input('search');
        if (isset($search) && $search) {
            $query = $query->where('name', 'LIKE', "%${search}%");
        }

        $markers = $query
            ->get()
            ->map(function ($el) use ($path) {
                $picture = str_replace(':', '-', $el->name.'.png');
                $el->picture = file_exists($path.'/'.$picture)
                    ? 'pictures/markers/'.$picture
                    : 'pictures/markers/discovering.png';

                return $el;
            });

        $response = [
            'entities' => $markers,
            'success' => !!count($markers)
        ];

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file',
        ]);

        Excel::import(new MarkersImport, $request->file('import_file'));

        return redirect()->back();
    }

    public function setTheme(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'theme_id' => 'required|integer',
        ]);

        $success = Marker::query()
            ->find($request->input('id'))
            ->update([
                'theme_id' => $request->input('theme_id'),
            ]);

        return response()->json(['success' => $success]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'markers' => 'required|array',
        ]);

        $markers = $request->input('markers');

        $success = true;
        foreach ($markers as $marker) {
            $done = Marker::query()
                ->find($marker['id'])
                ->update([
                    'category_id' => $marker['category_id'],
                ]);

            if (!$done) {
                $success = false;
            }
        }

        $response = [
            'success' => $success,
        ];

        return response()->json($response);
    }
}
