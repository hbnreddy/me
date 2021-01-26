<?php

namespace App\Http\Controllers;

use App\Eloquent\Place;
use App\Exports\VenuesExport;
use App\Http\Api\Musement\MusementApi;
use App\Http\Api\Sygic\SygicApi;
use App\Imports\VenuesImport;
use App\Eloquent\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VenueController extends Controller
{
    /**
     * @param Request $request
     * @param SygicApi $sygicApi
     * @param MusementApi $musementApi
     * @return JsonResponse
     */
    public function index(Request $request, SygicApi $sygicApi, MusementApi $musementApi)
    {
        $request->validate([
            'limit' => 'integer',
            'offset' => 'integer',
            'state' => 'integer',
        ]);

        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $state = intval($request->input('state'));

        $query = Venue::query()
            ->with([
                'place',
            ]);

        if (isset($state)) {
            if ($state) {
                $query = $query->where('place_id', '!=', 0);
            } else {
                $query = $query->where('place_id', 0);
            }
        }

        if (isset($offset)) {
            $query = $query->offset($offset);
        }

        if (isset($limit)) {
            $query = $query->limit($limit);
        }

        $entities = $query->get()->map(function ($el) {
            return $el->setAppends([]);
        });

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function setPlace(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'place_id' => 'required|integer',
        ]);

        $entity = Venue::query()->find($request->input('id'));

        Place::query()
            ->find($request->input('place_id'))
            ->update([
                'venue_id' => $request->input('id'),
            ]);

        $success = $entity->update([
            'place_id' => $request->input('place_id'),
        ]);

        $entity->load(['place']);

        $response = [
            'success' => $success,
            'entity' => $entity,
        ];

        return response()->json($response);
    }

    public function information(Request $request, $id)
    {
        $entity = Venue::query()->find($id);

        $entity->setAppends(['information']);

        $entity = $entity->information;

        $result = false;
        if ($entity) {
            $result = [
                'id' => $entity['id'],
                'cover_image_url' => $entity['cover_image_url'],
                'name' => $entity['name'],
                'description' => $entity['description'],
                'reviews_number' => $entity['reviews_number'],
                'reviews_avg' => $entity['reviews_avg'],
                'headline' => $entity['headline'],
                'address' => $entity['address'],
                'events_count' => $entity['events_count'],
            ];
        }

        $response = [
            'success' => !!$result,
            'entity' => $result,
        ];

        return response()->json($response);
    }

    public function activitiesCount(Request $request, $id)
    {
        $entity = Venue::query()->find($id);

        $musementApi = resolve(MusementApi::class);

        $information = json_decode($musementApi->getVenue($entity->foreign_id), true);

        $count = $information && $information['events_count']
            ? $information['events_count']
            : 0;

        $response = [
            'success' => true,
            'count' => $count,
        ];

        return response()->json($response);
    }

    public function shortInfo($id)
    {
        $venue = [];

        $response = [
            'success' => !!$entity,
            'entity' => $entity,
        ];

        return response()->json($response);
    }

    public function activities(Request $request, $id, MusementApi $musementApi)
    {
        $request->validate([
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'offset' => 'integer',
            'limit' => 'integer',
            'first_only' => 'integer',
        ]);

        $venue = Venue::query()->find($id);

        if (! $venue) {
            $venue = Venue::query()
                ->where('foreign_id', $id)
                ->first();
        }

        $firstOnly = $request->input('first_only');

        $params = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];
        if ($request->has('offset')) {
            $params['offset'] = $request->input('offset');
        }

        $limit = $request->has('limit') ? $request->input('limit') : 40;

        $activities = [];

        if (isset($venue->foreign_id)) {
            $activities = json_decode($musementApi->getVenueActivities($venue->foreign_id, $params), true);
        }

        if (! $activities) {
            $activities = [];
        }

        $entities = [];
        foreach ($activities as &$activity) {
            $dates = json_decode($musementApi->getActivityDates($activity['uuid'], [
                'start_date' => $params['start_date'],
                'end_date' => $params['end_date'],
            ]), true);

            if (! $dates || ! count($dates)) {
                continue;
            }

            $media = json_decode($musementApi->getActivityMedia($activity['uuid']));

            $entities[] = [
                'uuid' => $activity['uuid'],
                'title' => $activity['title'],
                'about' => $activity['about'],
                'reviews_avg' => $activity['reviews_avg'],
                'reviews_number' => $activity['reviews_number'],
                'retail_price' => $activity['retail_price'],
                'media' => $media,
            ];

            if ($firstOnly) {
                break;
            }
        }

        if (! $firstOnly) {
            $entities = array_slice($entities, $params['offset'], $limit);
        }

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function bestActivityPrice(Request $request, $id)
    {
        $entity = Venue::query()->find($id);

        $musementApi = resolve(MusementApi::class);

        $activities = json_decode($musementApi->getVenueActivities($entity->foreign_id, []), true);

        if (! $activities) {
            $activities = [];
        }

        $bestPrice = false;
        foreach ($activities as $activity) {
            if (! $bestPrice || $bestPrice > $activity['retail_price']['value']) {
                $bestPrice = $activity['retail_price']['value'];
            }
        }

        $response = [
            'success' => !!$entity,
            'entity' => $bestPrice,
        ];

        return response()->json($response);
    }

    public function totalCount()
    {
        $totalAssigned = Venue::query()
            ->where('place_id', '!=', 0)
            ->count();

        $totalUnassigned = Venue::query()
            ->where('place_id', 0)
            ->count();

        $response = [
            'total_assigned' => $totalAssigned,
            'total_unassigned' => $totalUnassigned,
        ];

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'foreign_id' => 'required|integer',
            'name' => 'string',
            'poi_id' => 'required|string',
        ]);

        $data = [
            'poi_id' => $request->input('poi_id'),
        ];

        $entity = Venue::query()
            ->where('foreign_id', $request->input('foreign_id'))
            ->update($data);

        $response = [
            'entity' => $entity,
            'success' => !!$entity
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

        Excel::import(new VenuesImport(), $request->file('import_file'));

        return redirect()->back();
    }

    /**
     * @return BinaryFileResponse
     */
    public function export()
    {
        return Excel::download(new VenuesExport, 'venues.xlsx');
    }

    public function getByIds(Request $request, MusementApi $musementApi)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->input('ids'));

        $venues = [];
        foreach ($ids as $id) {
            $temp = json_decode($musementApi->getVenue($id), true);

            if ($temp) {
                $venues[] = $temp;
            }
        }

        $response = [
            'success' => !!count($venues),
            'entities' => $venues,
        ];

        return response()->json($response);
    }
}
