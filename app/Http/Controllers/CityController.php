<?php

namespace App\Http\Controllers;

use App\Traits\Validation;
use App\AppCache;
use App\AppConfig;
use App\Cart\Cart;
use App\Cart\Plan;
use App\Cart\PlanItemType;
use App\Eloquent\City;
use App\Eloquent\Marker;
use App\Eloquent\Place;
use App\Eloquent\Theme;
use App\Cart\TravellerType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\NotificationManager;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'state' => 'integer',
            'country_id' => 'integer',
            'limit' => 'integer',
            'offset' => 'integer',
        ]);

        $query = City::query()
            ->with([
                'location',
                'bounds',
            ]);

        $state = $request->input('state');
        if (isset($state)) {
            $query = $query->where('enabled', boolval($state));
        }

        $limit = $request->input('limit');
        if (isset($limit)) {
            $query = $query->limit($limit);
        }

        $offset = $request->input('offset');
        if (isset($offset)) {
            $query = $query->offset($offset);
        }

        $countryId = $request->input('country_id');
        if (isset($countryId) && $countryId) {
            $query = $query->where('country_id', $countryId);
        }

        $entities = $query->get();

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function exploreMoreCities(Request $request)
    {
        $request->validate([
            'offset' => 'required|integer'
        ]);

        $query = City::query()
            ->where('enabled', true);

        if ($request->has('themes')) {
            $themes = explode('-', $request->input('themes'));

            $markers = Marker::query()
                ->distinct()
                ->whereIn('theme_id', $themes)
                ->pluck('id')
                ->toArray();

            $cityIds = Place::query()
                ->distinct()
                ->whereIn('marker_id', $markers)
                ->orderBy('rating_local', 'DESC')
                ->pluck('city_id')
                ->toArray();

            $query = $query
                ->whereIn('id', $cityIds);
        }

        $entities = $query->get([
            'id',
            'name',
            'name_suffix',
            'description',
            'image_url',
            'code',
        ]);

        foreach ($entities as &$entity) {
            $entity->places_count = Place::query()
                ->whereIn('marker_id', $markers)
                ->where('city_id', $entity->id)
                ->orderBy('rating_local', 'DESC')
//                ->limit(100)
                ->count();
        }

        $entities = $entities->sortByDesc('places_count');

        $offset = $request->input('offset');
        $entities = array_values($entities
            ->slice($offset, $offset + 4)
            ->toArray());

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function get($id)
    {
        $entity = City::query()
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

    public function name($id)
    {
        $entity = City::query()
            ->find($id);

        if ($entity) {
            $entity = $entity->name;
        }

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

        $entity = City::query()->find($request->input('id'));

        $state = $request->input('state');
        $success = $entity->update([
            'enabled' => $state,
        ]);

        $response = [
            'success' => $success,
            'entity' => $entity,
        ];

        return response()->json($response);
    }

    public function exploreAll(Request $request)
    {
        AppConfig::setCurrency($request->input('currency'));

        $themes = $request->input('themes');

        if ($themes) {
            $themes = array_map('intval', explode('-', $themes));
        } else {
            $themes = Theme::query()
                ->limit(2)
                ->pluck('id')
                ->toArray();
        }

        $temp = explode('-', $request->input('travellers'));
        $travellers = [
            TravellerType::ADULTS => intval($temp[0]),
            TravellerType::CHILDREN => intval($temp[1]),
            TravellerType::INFANTS => intval($temp[2]),
        ];

        $plan = Cart::currentPlan();

        $date['start_date'] = $plan ? $plan->startDate() : $request->input('start_date');
        $date['end_date'] = $plan ? $plan->lastDate() : $request->input('end_date');
        $date['date_type'] = $plan ? 'exact' : $request->input('date_type');

        $originCity = City::query()
            ->find($request->input('origin_city_id'));

        $query = [
            'currency' => AppCache::currencies()
                ->where('code', strtoupper(AppConfig::getCurrency()))
                ->first(),
            'locale' => AppConfig::getLocale(),
            'origin_city' => [
                'id' => $originCity->id,
                'code' => $originCity->code,
                'full_name' => $originCity->name.(
                    isset($originCity->name_suffix)
                        ? ', '.$originCity->name_suffix
                        : ''
                ),
            ],
            'travellers' => $travellers,
            'themes' => $themes,
            'date' => $date,
        ];

        if (! $themes || ! count($themes)) {
            $response = [
                'success' => false,
                'error' => 'Please select at least 1 theme.',
            ];

            return redirect()->back()->with('error', $response);
        }

        $markers = Marker::query()
            ->whereIn('theme_id', $themes)
            ->get()
            ->pluck('id')
            ->toArray();

        $cityIds = Place::query()
            ->distinct()
            ->whereIn('marker_id', $markers)
            ->orderBy('rating_local', 'DESC')
            ->whereNotIn('city_id', [$originCity->id])
            ->pluck('city_id')
            ->toArray();

        $cities = City::query()
            ->where('enabled', true)
            ->whereIn('id', $cityIds)
            ->get([
                'id',
                'name',
                'name_suffix',
                'description',
                'image_url',
                'code',
            ]);

        $cart = Cart::get();
        $planCities = [];

        if ($cart) {
            foreach ($cart['plans'] as $plan) {
                $planCities = array_merge($planCities, $plan->getItineraryCities());
            }
        }

        foreach ($cities as &$city) {
            $city->places_count = Place::query()
                ->whereIn('marker_id', $markers)
                ->where('city_id', $city->id)
                ->orderBy('rating_local', 'DESC')
                ->count();

            $city->added = in_array($city->id, $planCities);
        }

        $cities = $cities
            ->where('places_count', '>', 0)
            ->sortByDesc('places_count');
        $cities = array_values($cities->slice(0, 8)->toArray());

        $currentPlan = false;
        if (isset($cart['current_plan_id'])) {
            $currentPlan = $cart['current_plan_id'];
        }

        $data = [
            'message' => [
                'text' => '',
                'type' => '',
            ],
            'locale' => AppConfig::getLocale(),
            'user' => Auth::user(),
            'query' => $query,
            'themes' => AppCache::themes(),
            'cities' => $cities,
            'current_plan_id' => $currentPlan,
            'notifications' => NotificationManager::getNotifications(),
            'cart' => Cart::get(),
        ];
        return response()->json($data);
        // return view('explore', compact('data'));
    }
    public function explore(Request $request)
    {
        $request->validate([
            'themes' => 'required|string',
            'limit' => 'required|integer',
            'offset' => 'required|integer',
        ]);

        $response = [
            'success' => true,
            'error' => false,
        ];

        $themes = array_map('intval', explode('-', $request->input('themes')));

        if (! $themes || ! count($themes)) {
            $response = [
                'success' => false,
                'error' => true,
            ];

            return response()->json($response);
        }

        $markers = Marker::query()
            ->whereIn('theme_id', $themes)
            ->get()
            ->pluck('id')
            ->toArray();

        $cityIds = array_keys(Place::query()
            ->whereIn('marker_id', $markers)
            ->get()
            ->pluck('city_id', 'city_id')
            ->toArray());

        $cities = City::query()
            ->with([
                'places' => function ($query) use ($markers) {
                    return $query
                        ->with([
                            'venue',
                        ])
                        ->whereIn('marker_id', $markers);
                },
                'media_resources',
            ])
            ->where('enabled', true)
            ->whereIn('id', $cityIds)
            ->limit($request->input('limit'))
            ->offset($request->input('offset'))
            ->get();

        $response = [
            'success' => true,
            'entities' => $cities,
        ];

        return response()->json($response);
    }

    public function places($id, Request $request)
    {
        $request->validate([
            'theme_id' => 'required|integer',
            'limit' => 'integer',
            'offset' => 'integer',
        ]);

        $markers = Marker::query()
            ->where('theme_id', $request->input('theme_id'))
            ->get()
            ->pluck('id')
            ->toArray();

        $query = Place::query()
            ->with([
                'venue'
            ])
            ->where('city_id', $id)
            ->whereIn('marker_id', $markers);

        $limit = $request->input('limit');
        $offset = $request->input('offset');

        if ($limit) {
            $query = $query->limit($limit);
        }

        if ($offset) {
            $query = $query->offset($offset);
        }

        /**
         * @var Plan $plan
         */
        $plan = Cart::currentPlan();

        $cartPlacesIds = [];

        if ($plan) {
            $items = $plan->items();
            foreach ($items as $item) {
                if (in_array($item->getType(), [PlanItemType::STANDARD_ITEM, PlanItemType::ACTIVITY_ITEM])) {
                    $cartPlacesIds = array_merge($cartPlacesIds, $item->placesIds());
                }
            }
        }

        $entities = $query
            ->whereNotIn('id', $cartPlacesIds)
            ->get()
            ->toArray();

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function cheapestExperience($id, Request $request)
    {
        $price = AppCache::cityCheapestExperience($id);

        $response = [
            'success' => $price !== 99999999,
            'value' => $price,
        ];

        return response()->json($response);
    }

    public function counts($id, Request $request)
    {
        $query = Place::query()
            ->distinct()
            ->with(['venue'])
            ->orderBy('rating_local', 'DESC')
//            ->limit(100)
            ->where('city_id', $id);

        if ($request->has('themes')) {
            $themes = explode('-', $request->input('themes'));

            $markers = Marker::query()
                ->distinct()
                ->whereIn('theme_id', $themes)
                ->pluck('id')
                ->toArray();

            $query = $query
                ->whereIn('marker_id', $markers);
        }

        $places = $query->get();

        $experiencesCount = 0;
        foreach ($places as $place) {
            if ($place->venue && $place->venue->information) {
                $experiencesCount += $place->venue->information['events_count'];
            }
        }

        $response = [
            'success' => true,
            'places_count' => count($places),
            'experiences_count' => $experiencesCount,
        ];

        return response()->json($response);
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
        ]);

        $query = $request->input('query');

        $entities = City::query()
            ->where('name', 'LIKE', "%${query}%")
            ->get([
                'id',
                'name',
                'name_suffix'
            ])
            ->map(function ($el) {
                return [
                    'id' => $el->id,
                    'full_name' => $el->name.' '.$el->name_suffix,
                ];
            });

        $response = [
            'entities' => $entities,
            'success' => !!(count($entities))
        ];

        return response()->json($response);
    }

    public function city(Request $request)
    {
        $entity = City::query()->find(1);

        $response = [
            'entity' => $entity,
            'success' => !!$entity,
        ];

        return response()->json($response);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'image' => 'required|string',
            'type' => 'required|string',
        ]);

        $type = $request->input('type');

        $extension = '.'.explode('/', $type)[1];

        $baseImage = $request->input('image');

        $contents = explode(',', $baseImage)[1];
        $contents = str_replace(' ', '+', $contents);

        $city = City::query()->find($request->input('id'));

        $deleteImage = $city->image_url;

        $path = 'cities/'.$city->id.'_'.now()->getTimestamp().$extension;
        $success = Storage::disk('public')->put($path, base64_decode($contents));

        $updated = $city->update([
            'image_url' => $path,
        ]);

        $success = $success && !!$updated;

        if ($success) {
            Storage::disk('public')->delete($deleteImage);
        }

        $response = [
            'url' => $path,
            'success' => $success,
        ];

        return response()->json($response);
    }
}
