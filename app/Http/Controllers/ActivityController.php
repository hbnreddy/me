<?php

namespace App\Http\Controllers;

use App\Eloquent\Venue;
use App\Http\Api\Musement\MusementApi;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function dateInfo($id, Request $request)
    {
        $request->validate([
            'date' => 'required|string',
        ]);

        $musementApi = resolve(MusementApi::class);

        $result =
            \GuzzleHttp\json_decode($musementApi->getActivityDateInfo($id, $request->input('date')), true);

        $holderKeyMap = [
            'adult' => 'adults',
            'child' => 'children',
            'infant' => 'infants',
            'adult-rate' => 'adults',
            'bambino' => 'adults',
            'child-rate' => 'children',
            'infant-rate' => 'infants',
        ];

        $entity = false;
        if ($result && isset($result[0])) {
            foreach ($result[0]['groups'] as $group) {
                if (strtolower($group['type']) === 'time-based') {
                    $entity = [
                        'timeslots' => [],
                    ];

                    foreach ($group['slots'] as $slot) {
                        $timeslot = [
                            'time' => $slot['time'],
                        ];

                        foreach ($slot['products'] as $product) {
                            if (! isset($holderKeyMap[$product['holder_code']])) {
                                continue;
                            }

                            $timeslot[$holderKeyMap[$product['holder_code']]] = [
                                'product_id' => $product['product_id'],
                                'product_type' => $product['type'],
                                'min_buy' => $product['min_buy'],
                                'max_buy' => $product['max_buy'],
                                'price' => $product['retail_price']['value'],
                                'service_fee' => $product['service_fee']['value'],
                                'availability' => isset($product['availability']) ? $product['availability'] : false,
                            ];
                        }

                        if (! isset($timeslot['children'])) {
                            $timeslot['children'] = [
                                'price' => isset($timeslot['adults']) ? $timeslot['adults']['price'] : 0,
                                'availability' => 0,
                            ];
                        }

                        if (! isset($timeslot['infants'])) {
                            $timeslot['infants'] = [
                                'price' => isset($timeslot['children']) ? $timeslot['children']['price'] : 0,
                                'availability' => 0,
                            ];
                        }

                        $entity['timeslots'][] = $timeslot;
                    }

                    break;
                }
            }
        }

        $response = [
            'success' => !!$entity,
            'entity' => $entity,
        ];

        return response()->json($response);
    }

    public function dates($id, Request $request)
    {
        $request->validate([
            'start_date' => 'required|string',
            'end_date' => 'required|string',
        ]);

        $musementApi = resolve(MusementApi::class);

        $params = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $results =
            \GuzzleHttp\json_decode($musementApi->getActivityDates($id, $request->input('date'), $params, true), true);

        if (! $results) {
            $results = [];
        }

        $entities = [];
        foreach ($results as $result) {
            if (! $result['sold_out']
                && $result['day'] >= $params['start_date']
                && $result['day'] <= $params['end_date']
            ) {
                $entities[] = $result['day'];
            }
        }

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function comments($id)
    {
        $musementApi = resolve(MusementApi::class);

        $entities =
            \GuzzleHttp\json_decode($musementApi->getActivityComments($id), true);

        if (! $entities) {
            $entities = [];
        }

        $response = [
            'success' => true,
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function taxonomies($id)
    {
        $musementApi = resolve(MusementApi::class);

        $entities = json_decode($musementApi->getActivityTaxonomies($id));

        $entities = $entities ? $entities : [];

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function refundPolicies($id)
    {
        $musementApi = resolve(MusementApi::class);

        $entities = json_decode($musementApi->getActivityRefundPolicies($id));

        $entities = $entities ? $entities : [];

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function media(Request $request, $id)
    {
        $request->validate([
            'limit' => 'integer',
        ]);

        $limit = $request->has('limit')
            ? $request->input('limit')
            : 8;

        $musementApi = resolve(MusementApi::class);

        $entities = json_decode($musementApi->getActivityMedia($id));

        $entities = $entities
            ? array_slice($entities, 0, $limit)
            : [];

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function venues(Request $request, $id)
    {
        $musementApi = resolve(MusementApi::class);

        $activity = json_decode($musementApi->getActivity($id), true);

        $entities = [];
        foreach ($activity['venues'] as $venue) {
            $info = Venue::query()
                ->where('foreign_id', $venue['id'])
                ->first()
                ->information;

            $entities[] = [
                'id' => $venue['id'],
                'name' => $venue['name'],
                'image' => isset($info) ? $info['cover_image_url'] : '',
            ];
        }

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function duration($id)
    {
        $musementApi = resolve(MusementApi::class);

        $activity = json_decode($musementApi->getActivity($id), true);

        $entity = isset($activity['duration_range'])
            ? $activity['duration_range']
            : false;

        $response = [
            'success' => !!$entity,
            'entity' => $entity,
        ];

        return response()->json($response);
    }
}
