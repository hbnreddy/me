<?php

namespace App\Http\Controllers;

use App\AppConfig;
use App\AppDateFormat;
use App\Cart\Cart;
use App\Cart\PlanItemType;
use App\Cart\RideItem;
use App\Eloquent\City;
use App\Http\Api\Kiwi\KiwiApi;
use App\RideService;
use App\Traits\Fails;
use App\Traits\QueryProcessor;
use App\TripType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RideController extends Controller
{
    use Fails, QueryProcessor;

    public function index(Request $request)
    {
        $request->validate([
            'travellers' => 'required|string',
            'from_city_id' => 'required|integer',
            'to_city_id' => 'required|integer',
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'airlines' => 'string|nullable',
            'departure' => 'string',
            'arrival' => 'string',
            'bags' => 'integer',
            'max_price' => 'integer',
            'max_stops' => 'integer',
            'limit' => 'integer',
            'offset' => 'integer',
            'type' => 'required|string|in:aircraft,train',
        ]);

        $cityFrom = City::query()
            ->find($request->input('from_city_id'));
        $cityTo = City::query()
            ->find($request->input('to_city_id'));

        $travellers = explode('-', $request->input('travellers'));

        $parameters = [
            'limit' => $request->input('limit'),
            'offset' => $request->input('offset'),
            'vehicle_type' => $request->input('type'),
            'adults' => $travellers[0],
            'children' => $travellers[1],
            'infants' => $travellers[2],
//            'date_from' => date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($request->input('start_date'))),
//            'date_to' => date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($request->input('end_date'))),
            'fly_from' => $cityFrom->code,
            'fly_to' => $cityTo->code,
            'curr' => strtoupper(AppConfig::getCurrency()),
        ];

//        if ($request->has('airlines')) {
//            $parameters['select_airlines'] = $request->input('airlines'); // separated by comma
//        }

        if ($request->has('max_price')) {
            $parameters['price_from'] = 50;
            $parameters['price_to'] = $request->input('max_price');
        }

        if ($request->has('max_stops')) {
            $parameters['max_stopovers'] = $request->input('max_stops');
        }

        if ($request->has('bags')) {
            $parameters['bags'] = $request->input('bags');
        }

        if ($request->has('departure')) {
            $departure = explode('-', $request->input('departure'));

            $parameters['dtime_from'] = $departure[0];
            $parameters['dtime_to'] = $departure[1];
        }

        if ($request->has('arrival')) {
            $arrival = explode('-', $request->input('arrival'));

            $parameters['atime_from'] = $arrival[0];
            $parameters['atime_to'] = $arrival[1];
        }

        $kiwiApi = resolve(KiwiApi::class);
        $data = \GuzzleHttp\json_decode($kiwiApi->search($parameters), true);

        $rides = [];
        if ($data && isset($data['data'])) {
            $rides = $data['data'];
        }

        $response = [
            'success' => !!count($rides),
            'entities' => $rides,
        ];

        return response()->json($response);
    }

    public function cheapestRoute(Request $request, RideService $rideService)
    {
        $request->validate([
            'plan_id' => 'string|nullable',
            'price_only' => 'boolean',
        ]);

        $planId = $request->input('plan_id');

        if (! $planId) {
            $request->validate([
                'travellers' => 'required|string',
                'from_city_code' => 'required|string',
                'to_city_code' => 'required|string',
                'start_date' => 'required|string',
                'end_date' => 'required|string',
            ]);

            $fromCityCode = $request->input('from_city_code');
            $toCityCode = $request->input('to_city_code');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $parameters = array_merge(
                $this->processTravellers($request->input('travellers')),
                [
                    'date_from' => date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($startDate)),
                    'date_to' => date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($endDate)),
                    'fly_from' => $fromCityCode,
                    'fly_to' => $toCityCode,
                    'activities' => [],
                ]
            );
        } else {
            $request->validate([
                'to_city_code' => 'required|string',
            ]);

            $parameters = [];

            $toCityCode = $request->input('to_city_code');
            $parameters['fly_to'] = $toCityCode;

            // Get plan.
            $plan = Cart::getPlan($planId);

            // Get origin city code.

            $originCityCode = City::query()->find($plan->getOriginCityId())->code;
            $parameters['fly_from'] = $originCityCode;

            // Get travellers from plan.

            $parameters = array_merge($parameters, $plan->countTravellers());

            // Get start date and end date from plan.
            $planTimeslot = $plan->getTimeslot()->toArray();

            $parameters = array_merge($parameters, [
                'date_from' => date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($planTimeslot['start_date'])),
                'date_to' => date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($planTimeslot['end_date'])),
            ]);

            $activities = [];
            $itineraryCities = $plan->getItinerary()->toShort();
            foreach ($itineraryCities as $itineraryCity) {
                $cityCode = City::query()->find($itineraryCity['city_id'])->code;

                $date = date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($itineraryCity['date']));
                if ($toCityCode !== $cityCode) {
                    if (! isset($activities[$cityCode]) || $date > $activities[$cityCode]['date']) {
                        $activities[$cityCode] = [
                            'date' => $date,
                            'city_code' => $cityCode,
                        ];
                    }
                }
            }

            $parameters['activities'] = [];
            foreach ($activities as $activity) {
                $parameters['activities'][] = $activity;
            }
        }

        $cheapestRoute = $rideService->cheapestRoute($parameters);

        if ($cheapestRoute && $request->has('date') && $date = $request->input('date')) {
            foreach ($cheapestRoute['routes'] as $route) {
                if ($date === $route['date']) {
                    $cheapestRoute = $route;
                    $cheapestRoute['type'] = PlanItemType::RIDE_ITEM;

                    if ($request->input('price_only')) {
                        $cheapestRoute = [
                            'price' => $cheapestRoute['price'],
                            'extra' => $cheapestRoute['extra'],
                            'type' => PlanItemType::RIDE_ITEM,
                        ];
                    }

                    break;
                }
            }
        }

        if (! $cheapestRoute) {
            return $this->jsonFails();
        }

        $cheapestRoute['extra'] = false;
        if (isset($cheapestRoute['routes']) && count($cheapestRoute['routes']) > 2) {
            $totalPrice = $cheapestRoute['price'];
            $cheapestRoute['extra'] = true;

            foreach ($cheapestRoute['routes'] as $route) {
                if ($route['to_city_code'] !== $toCityCode) {
                    $totalPrice -= $route['price'];
                }
            }

            $cheapestRoute['price'] = $totalPrice;
        }

        if ($request->input('price_only')) {
            $cheapestRoute = [
                'price' => $cheapestRoute['price'],
                'extra' => $cheapestRoute['extra'],
                'type' => PlanItemType::RIDE_ITEM,
            ];
        }

        return response()->json([
            'success' => !!$cheapestRoute,
            'entity' => $cheapestRoute,
        ]);
    }

    public function airports()
    {
        $kiwiApi = resolve(KiwiApi::class);
        $data = \GuzzleHttp\json_decode($kiwiApi->airports(), true);

        if (! $data || ! isset($data['locations'])) {
            return $this->jsonFails();
        }

        $entities = [];
        foreach ($data['locations'] as $airport) {
            $entities[] = [
                'id' => $airport['id'],
                'code' => $airport['code'],
                'icao' => $airport['icao'],
                'name' => $airport['name'],
                'rank' => $airport['rank'],
            ];
        }

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }
}
