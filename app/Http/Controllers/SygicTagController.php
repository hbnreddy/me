<?php

namespace App\Http\Controllers;

use App\Imports\SygicTagsImport;
use App\Eloquent\SygicTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SygicTagController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $request->validate([
            'offset' => 'required|integer',
            'count' => 'required|integer',
            'search' => 'string|nullable',
            'assigned' => 'integer',
        ]);

        $assigned = $request->input('assigned');
        $search = $request->input('search');

        if (! isset($assigned)) {
            $assigned = true;
        }

        $query = SygicTag::query();

        if ($assigned) {
            $query = $query->where('category_id', '!=', 0);
        } else {
            $query = $query->where('category_id', '=', 0);
        }

        if (isset($search) && $search !== '') {
            $search = strtolower($search);
            $query = $query->where('tag_key', 'like', '%'.$search.'%');
        }

        $totalCount = $query->count();
        $tags = $query
            ->skip($request->input('offset'))
            ->take($request->input('count'))
            ->get();

        $response = [
            'total_count' => $totalCount,
            'entities' => $tags,
            'success' => !!count($tags)
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

        Excel::import(new SygicTagsImport(), $request->file('import_file'));

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'tags' => 'required|array',
        ]);

        $tags = $request->input('tags');

        $success = true;
        foreach ($tags as $tag) {
            $done = SygicTag::query()
                ->find($tag['id'])
                ->update([
                    'category_id' => $tag['category_id'],
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
