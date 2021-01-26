<?php

namespace App\Http\Controllers;

use App\Eloquent\CityWeather;
use App\Eloquent\EntityState;
use App\Eloquent\Marker;
use App\Eloquent\MediaResource;
use App\Eloquent\Place;
use App\EntityType;
use App\Http\Api\Musement\MusementApi;
use App\Http\Api\Sygic\SygicApi;
use App\Eloquent\Theme;
use App\Eloquent\PlaceTag;
use App\Eloquent\SygicTag;
use App\Eloquent\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'city_id' => 'required|integer',
            'theme_id' => 'integer',
            'marker_id' => 'integer',
            'limit' => 'integer',
        ]);

        $query = Place::query();

        $rating = $request->input('rating');

        if (isset($rating)) {
            $query = $query
                ->orderBy('rating_local', 'DESC');
        }

        $cityId = $request->input('city_id');
        if (isset($cityId)) {
            $query = $query->where('city_id', $cityId);
        }

        $themeId = $request->input('theme_id');
        if (isset($themeId) && $themeId) {
            $markers = Marker::query()
                ->where('theme_id', $themeId)
                ->get()
                ->pluck('id')
                ->toArray();

            $query = $query->whereIn('marker_id', $markers);
        }

        $markerId = $request->input('marker_id');
        if (isset($markerId)) {
            $query = $query->where('marker_id', $markerId);
        }

        $search = $request->input('search');
        if (isset($search) && $search) {
            $query = $query->where('name', 'LIKE', "%${search}%");
        }

        $limit = $request->input('limit');
        if (isset($limit) && $limit) {
            $query = $query->limit($limit);
        }

        $venueNotSet = $request->input('venue_not_set');

        if ($venueNotSet) {
            $query = $query->whereNull('venue_id');
        }

        $entities = $query
            ->get();

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function get($id)
    {
        $entity = Place::query()
            ->with([
                'location',
                'bounds',
                'city',
                'references',
                'venue',
                'media_resources',
            ])
            ->find($id);

        $entity->setAppends(['theme']);

        $response = [
            'success' => !!$entity,
            'entity' => $entity,
        ];

        return response()->json($response);
    }

    public function theme($id, Request $request)
    {
        $place = Place::query()
            ->find($id);

        $themeId = $place->theme->id;

        $response = [
            'entity' => $themeId,
        ];

        return response()->json($response);
    }

    /**
     * @param SygicApi $sygicApi
     * @return JsonResponse
     */
    public function continents(SygicApi $sygicApi)
    {
        $dataFromApi = json_decode($sygicApi->getContinents(), true);

        $continents = $dataFromApi['data']['places'];
        if (! $continents) {
            $continents = [];
        }

        $ids = collect($continents)->map(function ($el) {
            return $el['id'];
        })->toArray();

        $states = EntityState::query()
            ->whereIn('entity_id', $ids)
            ->get()
            ->keyBy('entity_id')
            ->toArray();

        foreach ($continents as &$continent) {
            if (!isset($states[$continent['id']])) {
                $continent['enabled'] = false;

                EntityState::query()
                    ->create([
                        'entity_id' => $continent['id'],
                        'enabled' => false,
                    ]);
            } else {
                $continent['enabled'] = $states[$continent['id']]['enabled'];
            }
        }

        $response = [
            'places' => $continents,
            'success' => !!count($continents)
        ];

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @param SygicApi $sygicApi
     * @return JsonResponse
     */
    public function countries(Request $request, SygicApi $sygicApi)
    {
        // Todo: Find a way to fetch countries by not hardcoding them.
        $parents = $request->input('parents');

        if (!$parents || ! count($parents)) {
            $entity = EntityState::query()
                ->where('entity_id', 'LIKE', 'continent%')
                ->where('enabled', true)
                ->first();

            $parents = [];
            if ($entity) {
                $parents = [$entity->entity_id];
            }
        }

        $countries = json_decode(
            $sygicApi->getCountries(['parents' => $parents]),
            true)['data']['places'];

        $extraCountries = [];
        if (in_array('continent:1', $parents)) {
            $extraCountriesIds = [
                'country:37',
                'country:36',
                'country:14',
                'country:13',
                'country:4',
                'country:16',
                'country:34',
            ];

            $extraCountries = json_decode(
                $sygicApi->getPlacesByIds($extraCountriesIds),
                true)['data']['places'];

            if (! $extraCountries) {
                $extraCountries = [];
            }

            foreach ($extraCountries as &$country) {
                $country['parent_ids'] = ['continent:1']; // Todo: Remove this hardcoded id.
            }
        }

        $countries = array_merge($countries, $extraCountries);

        if (!$countries) {
            $countries = [];
        }

        $ids = collect($countries)->map(function ($el) {
            return $el['id'];
        })->toArray();

        $states = EntityState::query()
            ->whereIn('entity_id', $ids)
            ->get()
            ->keyBy('entity_id')
            ->toArray();

        foreach ($countries as &$country) {
            if (!isset($states[$country['id']])) {
                $country['enabled'] = false;

                EntityState::query()
                    ->create([
                        'entity_id' => $country['id'],
                        'enabled' => false,
                    ]);
            } else {
                $country['enabled'] = $states[$country['id']]['enabled'];
            }
        }

        $response = [
            'places' => $countries,
            'success' => !!count($countries)
        ];

        return response()->json($response);
    }

    public function placesByIds(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'theme_id' => 'integer',
            'city_id' => 'integer',
        ]);

        $ids = array_map('intval', explode('-', $request->input('ids')));

        $query = Place::query()
            ->with([
                'venue'
            ])
            ->whereIn('id', $ids);

        $themeId = $request->input('theme_id');

        if (isset($themeId)) {
            $markers = Marker::query()
                ->where('theme_id', $request->input('theme_id'))
                ->get()
                ->pluck('id')
                ->toArray();

            $query = $query
                ->whereIn('marker_id', $markers);
        }

        $cityId = $request->input('city_id');
        if (isset($cityId)) {
            $query = $query
                ->where('city_id', $cityId);
        }

        $entities = $query->get();

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @param SygicApi $sygicApi
     * @return JsonResponse
     */
    public function cities(Request $request, SygicApi $sygicApi)
    {
        $parents = $request->input('parents');

        if (!$parents || ! count($parents)) {
            $entity = EntityState::query()
                ->where('entity_id', 'LIKE', 'country%')
                ->where('enabled', true)
                ->first();

            $parents = [];
            if ($entity) {
                $parents = [$entity->entity_id];
            }
        }

        $cities = json_decode(
            $sygicApi->getCities(['parents' => $parents]),
            true)['data']['places'];

        if (!$cities) {
            $cities = [];
        }

        $ids = collect($cities)->map(function ($el) {
            return $el['id'];
        })->toArray();

        $states = EntityState::query()
            ->whereIn('entity_id', $ids)
            ->get()
            ->keyBy('entity_id')
            ->toArray();

        foreach ($cities as &$city) {
            if (!isset($states[$city['id']])) {
                $city['enabled'] = false;

                EntityState::query()
                    ->create([
                        'entity_id' => $city['id'],
                        'enabled' => false,
                    ]);
            } else {
                $city['enabled'] = $states[$city['id']]['enabled'];
            }
        }

        $response = [
            'places' => $cities,
            'success' => !!count($cities)
        ];

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @param SygicApi $sygicApi
     * @return JsonResponse
     */
    public function placesOfInterest(Request $request, SygicApi $sygicApi)
    {
        $parents = $request->input('parents');
        $categoryId = $request->input('category_id');
        $requestFilters = $request->input('filters');

        if (isset($requestFilters)) {
            $requestFilters = json_decode($requestFilters, true);
        }

        $filters = [];
        if (isset($requestFilters['filter_by_rating'])
            && $requestFilters['filter_by_rating']
            && $requestFilters['rating'])
        {
            $filters['limit'] = $requestFilters['rating'];
            $filters['rating'] = '0.05:';
        }

        if (!$parents || ! count($parents)) {
            $entity = EntityState::query()
                ->where('entity_id', 'LIKE', 'city%')
                ->where('enabled', true)
                ->first();

            $parents = [];
            if ($entity) {
                $parents = [$entity->entity_id];
            }
        }

        $filters['parents'] = $parents;

        $tags = false;
        if ($categoryId && $categoryId !== 'false') {
            $tags = SygicTag::query()
                ->where('category_id', $categoryId)
                ->get()
                ->pluck('tag_key')
                ->toArray();
        }

        if ($tags) {
            $filters['tags'] = $tags;
        }

        if (isset($requestFilters['query'])) {
            $filters['query'] = $requestFilters['query'];
        }

        $places = json_decode(
            $sygicApi->getPlacesOfInterest($filters),
            true)['data']['places'];

        if (!$places) {
            $places = [];
        }

        $response = [
            'places' => $places,
            'success' => !!count($places)
        ];

        return response()->json($response);
    }

    /**
     * @param $id
     * @param SygicApi $sygicApi
     * @return JsonResponse
     */
    public function place($id, SygicApi $sygicApi)
    {
        $place = json_decode(
            $sygicApi->getPlace($id),
            true)['data']['place'];

        $state = EntityState::query()
            ->where('entity_id', $id)
            ->first();

        $place['enabled'] = $state['enabled'];

        $categoryIds = SygicTag::query()
            ->where('category_id', '!=', 0)
            ->whereIn('tag_key', collect($place['tags'])->pluck('key')->toArray())
            ->get()
            ->pluck('category_id')
            ->toArray();

        $place['rutugo_categories'] = Theme::query()
            ->whereIn('id', $categoryIds)
            ->get();

        $response = [
            'place' => $place,
            'success' => !!$place
        ];

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @param SygicApi $sygicApi
     * @return JsonResponse
     */
    public function searchPlaces(Request $request, SygicApi $sygicApi)
    {
        $request->validate([
            'query' => 'required|string',
            'level' => 'required|string|in:continent,country,city,poi',
        ]);

        $query = trim($request->input('query'));
        $level = trim($request->input('level'));

        $places = json_decode(
            $sygicApi->searchPlace($query, $level),
            true)['data']['places'];

        if (! $places) {
            $places = [];
        }

        $response = [
            'places' => $places,
            'success' => !!(count($places)),
        ];

        return response()->json($response);
    }

    /**
     * @param $city_id
     * @param Request $request
     * @param SygicApi $sygicApi
     * @return JsonResponse
     */
    public function placesByThemes($city_id, Request $request, SygicApi $sygicApi)
    {
        $request->validate([
            'themes_ids' => 'required|array',
        ]);

        $themesIds = $request->input('themes_ids');

        $themes = Theme::query()
            ->with([
                'tags'
            ])
            ->findMany($themesIds);

        foreach ($themes as &$theme) {
            $tags = $theme->tags
                ->pluck('tag_key')
                ->toArray();

            $placeIds = PlaceTag::query()
                ->where('city_id', $city_id)
                ->whereIn('tag_key', $tags)
                ->get()
                ->pluck('place_id')
                ->toArray();

            $experiencesByPlaceId = Venue::query()
                ->selectRaw('poi_id, COUNT(name) as count')
                ->whereIn('poi_id', $placeIds)
                ->groupBy('poi_id')
                ->get()
                ->keyBy('poi_id')
                ->toArray();

            $theme->places = json_decode(
                $sygicApi->getPlacesByIds($placeIds),
                true)['data']['places'];

            if (! $theme->places) {
                $theme->places = [];
            }

            $theme = $theme->toArray();
            foreach ($theme['places'] as $index => $place) {
                $theme['places'][$index]['experiences_count'] = isset($experiencesByPlaceId[$place['id']])
                    ? $experiencesByPlaceId[$place['id']]['count']
                    : 0;
            }
        }

        $response = [
            'entities' => $themes,
            'success' => !!(count($themes)),
        ];

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @param SygicApi $sygicApi
     * @return JsonResponse
     */
    public function explore(Request $request, SygicApi $sygicApi)
    {
        $request->validate([
            'themes_ids' => 'required|array',
        ]);

        $themesIds = $request->input('themes_ids');

        $tags = SygicTag::query()
            ->whereIn('category_id', $themesIds)
            ->get()
            ->pluck('tag_key')
            ->toArray();

        $cities = PlaceTag::query()
            ->selectRaw('city_id, COUNT(place_id) AS places_count')
            ->join('entity_states', 'place_tags.city_id', '=', 'entity_states.entity_id')
            ->where('entity_states.enabled', true)
            ->whereIn('tag_key', $tags)
            ->groupBy('city_id')
            ->orderByRaw('COUNT(place_id) DESC')
            ->get()
            ->keyBy('city_id')
            ->toArray();

        $entities = json_decode(
            $sygicApi->getPlacesByIds(array_keys($cities)),
            true)['data']['places'];

        foreach ($entities as &$entity) {
            $entity['places_count'] = $cities[$entity['id']]['places_count'];
            $entity['explored'] = false;
            $entity['experiences_count'] = Venue::query()
                ->join('place_tags', 'place_tags.place_id', '=', 'venues.poi_id')
                ->whereNotNull('venues.poi_id')
                ->where('place_tags.city_id', $entity['id'])
                ->count();

            $entity['plane_ticket'] = 265;
            $entity['accommodation'] = 120;
            $entity['visit_time'] = 'Feb, March and 2'; // Extra
            $entity['weather'] = CityWeather::query()
                ->where('city_id', $entity['id'])
                ->where('best_time', true)
                ->get()
                ->keyBy('month')
                ->toArray();
        }

        $response = [
            'entities' => $entities,
            'success' => !!(count($entities)),
        ];

        return response()->json($response);
    }

    public function placeInfo($place_id, Request $request, SygicApi $sygicApi, MusementApi $musementApi)
    {
        $place = json_decode(
            $sygicApi->getPlace($place_id),
            true)['data']['place'];

        $venue = Venue::query()
            ->where('poi_id', $place_id)
            ->first();

        if (! $venue) {
            return response()->json(['success' => false]);
        }

        $entity = \GuzzleHttp\json_decode($musementApi->getVenue($venue->foreign_id), true);

        $activitiesFilters = [
            'limit' => 5,
        ];

        $entity['activities'] = \GuzzleHttp\json_decode($musementApi->getVenueActivities($venue->foreign_id, $activitiesFilters), true);

        foreach ($entity['activities'] as &$activity) {
            $activityInfo =
                \GuzzleHttp\json_decode($musementApi->getActivity($activity['uuid']), true);

            $comments =
                \GuzzleHttp\json_decode($musementApi->getActivityComments($activity['uuid']), true);

            if (! $activityInfo) {
                $activityInfo = [];
            }

            if (! $comments) {
                $comments = [];
            }

            $activity = array_merge($activity, $activityInfo);
            $activity['comments'] = $comments;
        }

        $place['venue'] = $entity;

        $response = [
            'success' => !!$place,
            'place' => $place,
        ];

        return response()->json($response);
    }

    public function media($id)
    {
        $entities = MediaResource::query()
            ->where('entity_type', EntityType::PLACE)
            ->where('entity_id', $id)
            ->get();

        $response = [
            'success' => !!$entities,
            'entities' => $entities,
        ];

        return response()->json($response);
    }
}
