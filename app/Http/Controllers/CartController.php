<?php

namespace App\Http\Controllers;

use App\Cart\ActivityItem;
use App\Cart\Cart;
use App\Cart\Item;
use App\Cart\ItemStatus;
use App\Cart\Itinerary;
use App\Cart\ItineraryNode;
use App\Cart\Plan;
use App\Cart\PlanItemType;
use App\Cart\RideItem;
use App\Cart\StandardItem;
use App\Cart\Timeslot;
use App\Cart\Traveller;
use App\Cart\TravellerType;
use App\Eloquent\Customer;
use App\Eloquent\City;
use App\Eloquent\Marker;
use App\Eloquent\Place;
use App\Eloquent\Venue;
use App\Http\Api\Kiwi\KiwiApi;
use App\Http\Api\Musement\MusementApi;
use App\ItemType;
use App\NotificationManager;
use App\Eloquent\Order;
use App\Eloquent\OrderItem;
use App\Eloquent\OrderItemCustomer;
use App\Eloquent\OrderItemProduct;
use App\Eloquent\OrderItemProductCustomer;
use App\Traits\Fails;
use App\Traits\QueryProcessor;
use App\Traits\ValidateEntity;
use App\Traits\Validation;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;

class CartController extends Controller
{
    use Validation, QueryProcessor, Fails, ValidateEntity;

    public function plans()
    {
        $cart = Cart::get();
        $plans = isset($cart['plans']) ? $cart['plans'] : [];

        $entities = [];
        foreach ($plans as $plan) {
            $entities[] = array_merge($plan->toArray(), $plan->toEstimate());
        }

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function estimates()
    {
        $cart = Cart::get();
        $plans = isset($cart['plans']) ? $cart['plans'] : [];

        $entities = [];
        foreach ($plans as $plan) {
            $entities[] = $plan->toEstimate();
        }

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function individualEstimate($planId)
    {
        $plan = Cart::getPlan($planId);

        $entities = [];
        foreach ($plan->getTravellers() as $traveller) {
            $entities[$traveller->referenceKey()] = [
                'key' => $traveller->getKey(),
                'total_payment' => 0,
            ];
        }

        $musementCart = $plan->musementCart();
        $musementItemsPrices = [];

        if ($musementCart) {
            foreach ($musementCart['items'] as $musementItem) {
                $musementItemsPrices[$musementItem['uuid']] = $musementItem['product']['retail_price']['value'];
            }
        }

        $musementItems = [];
        foreach ($plan->items() as $item) {
            if ($item->getType() === ItemType::ACTIVITY) {
                $musementItems = array_merge($musementItems, $item->externalActivities());
            }
        }

        foreach ($musementItems as $musementItem) {
            foreach ($musementItem['travellers'] as $traveller) {
                $entities[$traveller]['total_payment'] += isset($musementItemsPrices[$musementItem['uuid']])
                    ? $musementItemsPrices[$musementItem['uuid']]
                    : 0;
            }
        }

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function itemExists(Request $request, $planId, $itemId)
    {
        $state = false;

        $plan = Cart::getPlan($planId);
        if ($plan) {
            foreach ($plan->items() as $item) {
                $id = null;
                if ($item->getType() === PlanItemType::ACTIVITY_ITEM) {
                    $id = $item->getExternalId('activity_uuid');
                } elseif ($item->getType() === PlanItemType::STANDARD_ITEM) {
                    $id = $item->getExternalId('place_id');
                }

                if ($id == $itemId) {
                    $state = true;
                    break;
                }
            }
        }

        $response = [
            'state' => $state,
        ];

        return response()->json($response);
    }

    public function items(Request $request, $planId)
    {
        $request->validate([
            'date' => 'string',
        ]);

        $plan = Cart::getPlan($planId);

        $entities = [];
        if (! $plan) {
            return response()->json([
                'success' => false,
                'entities' => $entities,
            ]);
        }

        foreach ($plan->items($request->input('date')) as $item) {
            $entities[] = $item->toShort();
        }

        return response()->json([
            'success' => !!count($entities),
            'entities' => $entities,
        ]);
    }

    public function citiesPerDates($planId)
    {
        $plan = Cart::getPlan($planId);
        $itinerary = $plan->getItinerary();

        $entities = [];
        foreach ($itinerary->getDates() as $date) {
            $entities[$date->getDate()] = $date->toShort()['city'];
        }

        return response()->json([
            'success' => !!count($entities),
            'entities' => $entities,
        ]);
    }

    public function itemsIds(Request $request, $planId)
    {
        $request->validate([
            'city_id' => 'integer',
            'date' => 'string',
            'theme_id' => 'integer',
        ]);

        $plan = Cart::getPlan($planId);

        if (! $plan) {
            return $this->jsonFails();
        }

        $placesIds = [];

        $items = $plan->items();
        foreach ($items as $item) {
            if (in_array($item->getType(), [PlanItemType::STANDARD_ITEM, PlanItemType::ACTIVITY_ITEM])) {
                $placesIds = array_merge($placesIds, $item->placesIds());
            }
        }

        $query = Place::query()
            ->whereIn('id', $placesIds);

        if ($request->has('city_id')) {
            $query = $query->where('city_id', $request->input('city_id'));
        }

        if ($request->has('theme_id')) {
            $themeId = $request->input('theme_id');

            $markersIds = Marker::query()
                ->where('theme_id', $themeId)
                ->get()
                ->pluck('id')
                ->toArray();

            $query = $query->whereIn('marker_id', $markersIds);
        }

        $results = $query
            ->get()
            ->pluck('id')
            ->toArray();

        $response = [
            'success' => !!count($results),
            'entities' => $results,
        ];

        return response()->json($response);
    }

    public function renamePlan(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $plan = Cart::getPlan($id);
        $plan->rename($request->input('name'));

        $response = [
            'success' => Cart::updatePlan($plan),
        ];

        return response()->json($response);
    }

    public function travellers($planId)
    {
        $plan = Cart::getPlan($planId);

        $entities = [];
        foreach ($plan->getTravellers() as $traveller) {
            $entities[] = $traveller->toArray();
        }

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function storeTraveller(Request $request, $planId, $travellerId)
    {
        $validator = $this->validateTraveller($request->all());

        if ($validator->fails()) {
            return $this->jsonFails();
        }

        $plan = Cart::getPlan($planId);

        $entity = false;
        foreach ($plan->getTravellers() as &$traveller) {
            if ($traveller->getKey() === $travellerId) {
                $traveller->setAttributes($request->all());

                $entity = $traveller;
            }
        }

        $success = !!$entity && Cart::updatePlan($plan);

        $response = [
            'success' => !!$success,
            'entity' => $entity->toArray(),
        ];

        return response()->json($response);
    }

    public function removeItem($planId, $itemId, Request $request)
    {
        $request->validate([
            'type' => 'required|in:standard,activity',
        ]);

        $plan = Cart::getPlan($planId);
        if (! $plan) {
            return $this->jsonFails(__('Plan not found'));
        }

        $success = $plan->removeItem($itemId) && Cart::updatePlan($plan);

        if ($success) {
            NotificationManager::decrease();
        }

        return response()->json(['success' => $success]);
    }

    public function storePlan(Request $request)
    {
        $request->validate([
            'origin_city_id' => 'required|integer',
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'travellers' => 'required|string',
        ]);

        $plan = new Plan();

        // Set origin city id.
        $plan->setOriginCityId($request->input('origin_city_id'));

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Set timeslot.
        $plan->setTimeslot(new Timeslot([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]));

        $dates = [];

        $date = Carbon::parse($startDate);
        while ($date->format('Y-m-d') <= $endDate) {
            $dates[] = $date->format('Y-m-d');
            $date->addDay();
        }

        $itinerary = new Itinerary();
        $itinerary->setDates($dates);

        // Set itinerary
        $plan->setItinerary($itinerary);

        // Set travellers.
        $planTravellers = [];

        $travellers = $this->processTravellers($request->input('travellers'));
        for ($i = 0; $i < $travellers[TravellerType::ADULTS]; ++$i) {
            $planTravellers[] = new Traveller(TravellerType::ADULTS);
        }

        for ($i = 0; $i < $travellers[TravellerType::CHILDREN]; ++$i) {
            $planTravellers[] = new Traveller(TravellerType::CHILDREN);
        }

        for ($i = 0; $i < $travellers[TravellerType::INFANTS]; ++$i) {
            $planTravellers[] = new Traveller(TravellerType::INFANTS);
        }

        $plan->setTravellers($planTravellers);

        // Create external musement cart.
        $musementApi = resolve(MusementApi::class);
        $musementCart = json_decode($musementApi->createCart(), true);

        if (! $musementCart) {
            return $this->jsonFails();
        }

        $plan->setExternalId('musement_cart_id', $musementCart['uuid']);

        $success = Cart::addPlan($plan);

        $response = [
            'success' => $success,
            'entity_id' => $plan->getKey(),
        ];

        return response()->json($response);
    }

    public function itinerary(Request $request, $planId)
    {
        $plan = Cart::getPlan($planId);

        $itinerary = $plan->getItinerary()->toShort();

        $cities = [];
        foreach ($itinerary['nodes'] as &$node) {
            $cities[$node['from_city_id']] = isset($cities[$node['from_city_id']])
                ? $cities[$node['from_city_id']]
                : City::query()
                    ->select(['id', 'name', 'code'])
                    ->find($node['from_city_id'])
                    ->toArray();

            $node['from_city'] = $cities[$node['from_city_id']];

            $cities[$node['to_city_id']] = isset($cities[$node['to_city_id']])
                ? $cities[$node['to_city_id']]
                : City::query()
                    ->select(['id', 'name', 'code'])
                    ->find($node['to_city_id'])
                    ->toArray();

            $node['to_city'] = $cities[$node['to_city_id']];
        }

        $response = [
            'success' => !!count($itinerary),
            'entity' => $itinerary,
        ];

        return response()->json($response);
    }

    public function rides($planId)
    {
        $plan = Cart::getPlan($planId);

        /**
         * @var Item $item
         */
        $entities = [];
        foreach ($plan->items() as $item) {
            if ($item->getType() === PlanItemType::RIDE_ITEM) {
                $entities[] = [
                    'id' => $item->getKey(),
                    'ride_id' => $item->getExternalId('ride_id'),
                    'type' => $item->getType(),
                    'vehicle_type' => $item->getVehicleType(),
                ];
            }
        }

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function storeFlight(Request $request, $planId)
    {
        $request->validate([
            'id' => 'required|string',
            'booking_token' => 'required|string',
            'departure_date' => 'required|string',
            'arrival_date' => 'required|string',
            'departure_time' => 'required|string',
            'arrival_time' => 'required|string',
            'city_code_from' => 'required|string',
            'city_code_to' => 'required|string',
            'price' => 'required|numeric',
            'travellers' => 'required|array',
        ]);

        $plan = Cart::getPlan($planId);

        if ($plan) {
            $plan->flights[] = array_merge($request->all(), [
                'type' => 'flight',
            ]);
        }

        $success = $plan && Cart::updatePlan($plan);

        $response = [
            'success' => $success,
        ];

        return response()->json($response);
    }

    public function availableDays(Request $request, $planId)
    {
        $plan = Cart::getPlan($planId);

        if (! $plan) {
            return $this->jsonFails();
        }

        $dates = [];
        foreach ($plan->availableDates() as $date) {
            $dates[$date] = [
                'date' => $date,
                'day' => date('j', strtotime($date)),
                'month' => date('M', strtotime($date)),
                'remainingHours' => 18,
                'available' => true,
            ];
        }

        foreach ($plan->getItinerary()->getDates() as $date) {
            if (! isset($dates[$date->getDate()])) {
                $dates[$date->getDate()] = [
                    'date' => $date->getDate(),
                    'day' => date('j', strtotime($date->getDate())),
                    'month' => date('M', strtotime($date->getDate())),
                    'remainingHours' => 18,
                    'available' => true,
                ];
            }

            $dates[$date->getDate()]['remaining_hours'] = $date->getRemainingHours();
        }

        ksort($dates);
        $dates = array_values($dates);

        $response = [
            'success' => !!count($dates),
            'entities' => $dates,
        ];

        return response()->json($response);
    }

    public function remainingHours($planId, $date)
    {
        $plan = Cart::getPlan($planId);
        $minutes = 18 * 60;

        if ($plan) {
            foreach ($plan->items($date) as $item) {
                if ($item->getDate() === $date) {
                    $minutes -= $item->getDuration();
                }
            }
        }

        $response = [
            'value' => round($minutes/ 60),
        ];

        return response()->json($response);
    }

    public function storeRideItems(Request $request, $planId)
    {
        $request->validate([
            'items' => 'array',
        ]);

        $items = $request->input('items');
        foreach ($items as $item) {
            $validator = $this->validateItem(PlanItemType::RIDE_ITEM, $item);

            if ($validator->fails()) {
                return $this->jsonFails();
            }
        }

        $plan = Cart::getPlan($planId);
        $addItems = [];
        foreach ($items as $itemData) {
            if ($plan->routeExists($itemData['from_city_code'], $itemData['to_city_code'])) {
                continue;
            }

            $travellersIds = [];
            $travellersKeys = array_column($itemData['travellers'], 'key');

            foreach ($plan->getTravellers() as $traveller) {
                if (in_array($traveller->referenceKey(), $travellersKeys)) {
                    $travellersIds[] = $traveller->getKey();
                }
            }

            $data = $itemData;

            $item = new RideItem();
            $kiwiApi = resolve(KiwiApi::class);

            $apiData = \GuzzleHttp\json_decode($kiwiApi->getById($data['external_id'], [
                'fly_from' => $data['from_city_code'],
                'fly_to' => $data['to_city_code'],
                'date_to' => date('d/m/Y', strtotime($data['date'])),
                'date_from' => date('d/m/Y', strtotime($data['date'])),
            ]), true);

            if (! $apiData['success']) {
                return $this->jsonFails();
            }

            $entity = $apiData['entity'];
            $route = $entity['route'][0];

            $item->setAttributes([
                'duration' => $entity['duration']['total'] / 60, // Duration in minutes.
                'vehicle_type' => $route['vehicle_type'],
                'from_city_id' => isset($data['from_city_id']) ? $data['from_city_id'] : false,
                'to_city_id' => isset($data['to_city_id']) ? $data['to_city_id'] : false,
                'from_city_code' => isset($data['from_city_code']) ? $data['from_city_code'] : false,
                'to_city_code' => isset($data['to_city_code']) ? $data['to_city_code'] : false,
                'booking_token' => $entity['booking_token'],
            ]);

            $item->setDate(Carbon::parse($entity['dTime'])->format('Y-m-d'));

            $item->setTimeslot(new Timeslot([
                'start_date' => Carbon::parse($entity['dTime'])->format('Y-m-d'),
                'start_time' => Carbon::parse($entity['dTime'])->format('h:i'),
                'end_date' => Carbon::parse($entity['aTime'])->format('Y-m-d'),
                'end_time' => Carbon::parse($entity['aTime'])->format('h:i'),
            ]));

            $item->setExternalId('ride_id', $data['external_id']);
            $item->setPlanId($planId);
            $item->setExternalId('city_id', $request->input('city_id'));
            $item->setTravellersIds($travellersIds);

            $addItems[] = $item;
        }

        $lastRide = $plan->lastRide();

        $success = false;
        foreach ($addItems as $item) {
            $success = $plan->addItem($item);

            if (! $success) {
                break;
            }
        }

        if (! $success) {
            foreach ($addItems as $item) {
                if (isset($item)) {
                    $plan->removeItem($item->getKey());
                }
            }

            return $this->jsonFails();
        }

        if ($lastRide && isset($lastRide)) {
            $plan->removeItem($lastRide->getKey());
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function storeItem(Request $request, $planId, $itemId = false)
    {
        $request->validate([
            'type' => 'required|string',
            'modify' => 'boolean',
        ]);

        $validator = $this->validateItem($request->input('type'), $request->all());

        if ($validator->fails()) {
            return $this->jsonFails();
        }

        /**
         * @var Plan $plan
         */
        $plan = Cart::getPlan($planId);
        $type = $request->input('type');
        $modify = $request->input('modify');
        $id = $request->input('id');
        $cityId = $request->input('city_id');

        $item = null;
        if ($modify) {
            foreach ($plan->items() as $planItem) {
                if ($planItem->getKey() === $id) {
                    $item = $planItem;
                }
            }
        }

        $date = $request->input('start_date');
        foreach ($plan->items() as $planItem) {
            if ($planItem->getType() !== PlanItemType::RIDE_ITEM &&
                $planItem->getDate() === $date&&
                !$modify &&
                $cityId !== $planItem->getExternalId('city_id')) {
                return $this->jsonFails();
            }
        }

        $travellersIds = [];
        if ($type !== PlanItemType::RIDE_ITEM || ! count($request->input('travellers_ids'))) {
            $travellersKeys = array_column($request->input('travellers'), 'reference_key');

            foreach ($plan->getTravellers() as $traveller) {
                if (in_array($traveller->referenceKey(), $travellersKeys)) {
                    $travellersIds[] = $traveller->getKey();
                }
            }
        } else {
            $travellersIds = $request->input('travellers_ids');
        }

        $data = $request->all();

        /**
         * @var Item $item
         */
        switch ($type) {
            case PlanItemType::STANDARD_ITEM: {
                if (! $modify) {
                    $item = new StandardItem();
                }

                $item->setAttributes([
                    'name' => $request->input('name'),
                ]);

                $placeId = intval($request->input('external_id'));

                $item->setExternalId('place_id', $placeId);
                $item->setExternalId('places_ids', [$placeId]);

                break;
            }

            case PlanItemType::ACTIVITY_ITEM: {
                if (! $modify) {
                    $item = new ActivityItem();
                }

                $item->setAttributes([
                    'name' => $request->input('name'),
                ]);

                $musementApi = resolve(MusementApi::class);

                if ($modify) {
                    foreach ($item->externalActivities() as $activity) {
                        json_decode(
                            $musementApi->removeItemFromCart(
                                $plan->getExternalId('musement_cart_id'), $activity), true
                        );
                    }

                    $item->setExternalActivities([]);
                }

                $products = $request->input('products');
                $externalActivities = [];
                $availableCount = 0;
                foreach ($products as $product) {
                    if (! count($product['travellers']) || !isset($product['product_id'])) {
                        continue;
                    }

                    $mItemTravellers = [];
                    foreach ($product['travellers'] as $traveller) {
                        $mItemTravellers[] = $traveller['reference_key'];
                    }

                    $availableCount++;

                    $externalActivity = [
                        'type' => $product['product_type'],
                        'product_identifier' => $product['product_id'],
                        'quantity' => count($product['travellers']),
                    ];

                    $addedActivity = json_decode(
                        $musementApi->addItemToCart($plan->getExternalId('musement_cart_id'), $externalActivity),
                        true
                    );

                    if ($addedActivity && $addedActivity['status'] === ItemStatus::PREBOOK_OK) {
                        $externalActivities[] = [
                            'uuid' => $addedActivity['uuid'],
                            'product_id' => $addedActivity['product']['id'],
                            'travellers' => $mItemTravellers,
                        ];
                    }
                }

                $success = $availableCount === count($externalActivities);
                if ($success) {
                    $item->setExternalActivities($externalActivities);
                }

                $activityUuid = $request->input('external_id');
                $musementActivity = \GuzzleHttp\json_decode($musementApi->getActivity($activityUuid), true);
                $placesIds = [];

                if ($musementActivity && isset($musementActivity['venues'])) {
                    $placesIds = array_map(function ($el) {
                        $venue = Venue::query()
                            ->where('foreign_id', $el['id'])
                            ->first()
                            ->toArray();

                        if (isset($venue) && isset($venue['id'])) {
                            return $venue['place_id'];
                        }

                        return 0;
                    }, $musementActivity['venues']);
                }

                $item->setExternalId('activity_uuid', $activityUuid);
                $item->setExternalId('places_ids', $placesIds);

                break;
            }

            case PlanItemType::RIDE_ITEM: {
                if (! $modify) {
                    $item = new RideItem();
                }

                $kiwiApi = resolve(KiwiApi::class);

                $apiData = \GuzzleHttp\json_decode($kiwiApi->getById($data['external_id'], [
                    'fly_from' => $data['from_city_code'],
                    'fly_to' => $data['to_city_code'],
                    'date_to' => date('d/m/Y', strtotime($data['date'])),
                    'date_from' => date('d/m/Y', strtotime($data['date'])),
                ]), true);

                if (! $apiData['success']) {
                    return $this->jsonFails();
                }

                $entity = $apiData['entity'];
                $route = $entity['route'][0];

                $item->setAttributes([
                    'duration' => $entity['duration']['total'] / 60, // Duration in minutes.
                    'vehicle_type' => $route['vehicle_type'],
                    'from_city_id' => isset($data['from_city_id']) ? $data['from_city_id'] : false,
                    'to_city_id' => isset($data['to_city_id']) ? $data['to_city_id'] : false,
                    'from_city_code' => isset($data['from_city_code']) ? $data['from_city_code'] : false,
                    'to_city_code' => isset($data['to_city_code']) ? $data['to_city_code'] : false,
                    'booking_token' => $entity['booking_token'],
                ]);

                $item->setDate(Carbon::parse($entity['dTime'])->format('Y-m-d'));

                $item->setTimeslot(new Timeslot([
                    'start_date' => Carbon::parse($entity['dTime'])->format('Y-m-d'),
                    'start_time' => Carbon::parse($entity['dTime'])->format('h:i'),
                    'end_date' => Carbon::parse($entity['aTime'])->format('Y-m-d'),
                    'end_time' => Carbon::parse($entity['aTime'])->format('h:i'),
                ]));

                $item->setExternalId('ride_id', $data['external_id']);

//                foreach ($plan->itemsByType(PlanItemType::RIDE_ITEM) as $rideItem) {
//                    if ($rideItem->getToCityCode() === $plan->getOriginCityCode()
//                        && $rideItem->getToCityCode() === $item->getToCityCode()) {
//                        $plan->removeItem($rideItem->getKey());
//                        break;
//                    }
//                }

                break;
            }
            default: {
                return $this->jsonFails();
            }
        }

        /**
         * Set shared attributes.
         */
        $item->setPlanId($planId);
        $item->setExternalId('city_id', $request->input('city_id'));
        $item->setTravellersIds($travellersIds);

        if ($type !== PlanItemType::RIDE_ITEM) {
            $item->setDate($request->input('start_date'));
            $item->setTimeslot(new Timeslot([
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
            ]));
        }

        $insertSuccess = $modify
            ? $plan->updateItem($item)
            : $plan->addItem($item);

        $success = $insertSuccess;

        // Create / update itinerary
        if ($success) {
            /**
             * @var Itinerary $itinerary
             */
            $itinerary = $plan->getItinerary();

            if (in_array($item->getType(), [PlanItemType::STANDARD_ITEM, PlanItemType::ACTIVITY_ITEM])) {
                $dates = $itinerary->getDates();

                $allTravellersIds = [];
                foreach ($plan->getTravellers() as $traveller) {
                    $allTravellersIds[] = $traveller->getKey();
                }

                $timeslot = new Timeslot($item->getTimeslot()->toArray());

                if ($itinerary->isEmpty()) {
                    $itinerary->insertCity($plan->getOriginCityId());

                    $itinerary->insert([
                        'from_city_id' => $plan->getOriginCityId(),
                        'to_city_id' => $item->getExternalId('city_id'),
                        'timeslot' => $timeslot,
                        'ride_id' => false,
                        'travellers_ids' => $allTravellersIds,
                    ]);
                } else {
                    $lastNode = $itinerary->getLastNode();
                    $lastNodeData = $lastNode->getData();

                    $itinerary->insert([
                        'from_city_id' => $lastNodeData['to_city_id'],
                        'to_city_id' => $item->getExternalId('city_id'),
                        'timeslot' => $timeslot,
                        'ride_id' => false,
                        'travellers_ids' => $allTravellersIds,
                    ]);
                }

                $itinerary->insertCity($item->getExternalId('city_id'));

                $timeslotArray = $timeslot->toArray();

                $dates[$timeslotArray['start_date']]->setCity($item->getExternalId('city_id'));
                $dates[$timeslotArray['start_date']]->setRemainingHours(
                    $dates[$timeslotArray['start_date']]->getRemainingHours() - ($item->getDuration() / 60)
                );

                $itinerary->updateDates($dates);
            } else {
                /**
                 * @var ItineraryNode $node
                 */
                $node = &$itinerary->getStartNode();
                while ($node) {
                    $nodeData = $node->getData();

                    $fromCheck = 0;
                    $toCheck = 0;
                    if (isset($data['from_city_code']) && isset($data['to_city_code'])) {
                        $fromCheck = City::query()
                            ->where('code', $data['from_city_code'])
                            ->first()
                            ->id;
                        $toCheck = City::query()
                            ->where('code', $data['to_city_code'])
                            ->first()
                            ->id;
                    } else {
                        $fromCheck = $data['from_city_id'];
                        $toCheck = $data['to_city_id'];
                    }

                    if (intval($nodeData['from_city_id']) === intval($fromCheck)
                        && intval($nodeData['to_city_id']) === intval($toCheck)) {

                        $nodeData['ride_id'] = $item->getKey();

                        $node->setData($nodeData);
                        break;
                    }

                    $node = &$node->next();
                }
            }

            $plan->setItinerary($itinerary);

            $success = Cart::updatePlan($plan);

            if (in_array($item->getType(), [PlanItemType::STANDARD_ITEM, PlanItemType::ACTIVITY_ITEM])) {
                NotificationManager::increase();
            }
        }

        return response()->json([
            'success' => !!$success,
        ]);

//        if ($success && in_array($item->getType(), [PlanItemType::STANDARD_ITEM, PlanItemType::ACTIVITY_ITEM])) {

        /**
         * TODO: This is for searching flight by id using plan's details.
         */
//        $rideId = '113a013147ee000023076b11_0';
//
//        $kiwiApi = resolve(KiwiApi::class);
//        $currentPlan = Cart::currentPlan();
//        $travellers = [
//            'adults' => 0,
//            'children' => 0,
//            'infants' => 0,
//        ];
//
//        foreach ($currentPlan->getTravellers() as $traveller) {
//            $travellers[$traveller->getType()]++;
//        }
//
//        $timeslot = $currentPlan->getTimeslot()->toShort();
//
//        $helpers = [
//            'adults' => $travellers['adults'],
//            'children' => $travellers['children'],
//            'infants' => $travellers['infants'],
//            'date_from' => date('d/m/Y', strtotime($timeslot['start_date'])),
//            'date_to' => date('d/m/Y', strtotime($timeslot['end_date'])),
//            'fly_from' => 'PAR',
//            'fly_to' => 'ROM',
//            'curr' => strtoupper(AppConfig::getCurrency()),
////            'one_for_city' => 1,
//        ];
//
//        $data = \GuzzleHttp\json_decode($kiwiApi->getById($rideId, $helpers), true);

//            /**
//             * @var Itinerary $itinerary
//             */
//            $itinerary = $plan->getItinerary();
//
//            // Check if empty -> add origin city and new city.
//            if ($itinerary->isEmpty()) {
//                dd($plan, $item);
////                $itinerary->insert([
////                    'from_city_id' => $plan->getOriginCityId(),
////                    'to_city_id' => $plan->getOriginCityId(),
////                ]);
//            } else {
//                // Check if last city is different than this current item's city.
//            }
//
//            dd($itinerary);
//        }
    }

    public function checkout(Request $request)
    {
        $plan = Cart::currentPlan();

        $user = Auth::user();

        if (! $user) {
            return $this->jsonFails([
                    'success' => false,
                    'message' => [
                        'type' => 'error',
                        'text' => __('You are not logged in'),
                    ],
                ]);
        }

        $travellers = [];
        $mainCustomer = false;
        foreach ($plan->getTravellers() as $index => $traveller) {
            $travellerData = $traveller->toArray();

            if (! $this->travellerValidForCart($travellerData)) {
                return $this->jsonFails([
                    'success' => false,
                    'message' => [
                        'type' => 'error',
                        'text' => __('Please fill in all the information for travellers'),
                    ],
                ]);
            }

            /**
             * Map travellers by key
             */
            $travellers[$travellerData['reference_key']] = $traveller;

            if ($travellerData['email'] === $user->email) {
                $mainCustomer = $traveller;
            }
        }

        if (! $mainCustomer) {
            return response()->json([
                'success' => false,
                'message' => [
                    'type' => 'error',
                    'text' => 'Logged in user should be part of the customers (main customer).',
                ],
            ]);
        }

        $musementApi = resolve(MusementApi::class);

        /**
         * SET CART CUSTOMER.
         */

        /**
         * Setup Musement Cart
         */

        if ($plan->hasMusementItems()) {
            $customerSet = json_decode(
                $musementApi->setCustomer($plan->getExternalId('musement_cart_id'), $mainCustomer), true
            );

            if (! $customerSet) {
                return response()->json([
                    'success' => false,
                    'message' => [
                        'type' => 'error',
                        'text' => 'There was an error when setting the main customer.',
                    ],
                ]);
            }

            foreach ($plan->items() as $item) {
                if ($item->getType() === PlanItemType::ACTIVITY_ITEM) {
                    $musementItems = [];
                    foreach ($item->musementItems as $musementItem) {
                        $musementItems[] = $musementItem;
                    }

                    foreach ($musementItems as $musementItem) {
                        $musementItemSchema = json_decode(
                            $musementApi->participantsSchema($plan->musement_cart_id, $musementItem['uuid']), true
                        );

                        $requiredFields = $musementItemSchema['properties']['participants']['items']['required'];

                        $items = [];
                        foreach ($musementItem['travellers'] as $travellerKey) {
                            $item = [];

                            if (in_array('email', $requiredFields)) {
                                $item = array_merge($item,
                                    [
                                        'email' => $travellers[$travellerKey]->attributes['email'],
                                    ]
                                );
                            }

                            if (! count($item)) {
                                return $this->jsonFails([
                                    'success' => false,
                                    'message' => [
                                        'type' => 'error',
                                        'text' => 'Required fields are wrong: '.implode(', ', $requiredFields),
                                    ],
                                ]);
                            }

                            $items[] = $item;
                        }

                        $storeParticipant = json_decode(
                            $musementApi->putTravellerInCart($plan->musement_cart_id, $musementItem['uuid'], $items), true
                        );

                        if (! $storeParticipant) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Failed storing traveller details.',
                            ]);
                        }
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function order()
    {
        $plan = Cart::currentPlan();

        $musementApi = resolve(MusementApi::class);

        if ($plan->hasMusementItems()) {
            $musementOrder = json_decode($musementApi->createOrder($plan->getExternalId('musement_cart_id')), true);

            if (! $musementOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error occured when creating the order.',
                ]);
            }
        }

        $authenticatedUser = Auth::user();

        /**
         * Store customers.
         */
        $customerKeyMap = [];
        $customers = [];
        $mainCustomer = false;
        foreach ($plan->getTravellers() as $traveller) {
            $attributes = $traveller->toArray();

            $user = User::query()
                ->where('email', $attributes['email'])
                ->first();

            $customer = Customer::query()
                ->updateOrCreate(
                    [
                        'email' => $attributes['email'],
                    ],
                    [
                        'user_id' => isset($user) ? $user->id : null,
                        'first_name' => $attributes['first_name'],
                        'last_name' => $attributes['last_name'],
                        'middle_name' => $attributes['middle_name'],
                        'email' => $attributes['email'],
                        'type' => $attributes['type'],
                        'birthdate' => $attributes['birthdate'],
                        'passport_start_date' => $attributes['passport_start_date'],
                        'passport_end_date' => $attributes['passport_end_date'],
                        'gender' => $attributes['gender'],
                        'nationality' => $attributes['nationality'],
                        'mobile_number' => $attributes['mobile_number'],
                        'house_number' => $attributes['house_number'],
                        'street_number' => $attributes['street_number'],
                        'area' => $attributes['area'],
                        'city' => $attributes['city'],
                        'country' => $attributes['country'],
                        'pincode' => $attributes['pincode'],
                    ]
                );

            $customerKeyMap[$attributes['reference_key']] = $customer->id;

            if ($attributes['email'] === $authenticatedUser->email) {
                $mainCustomer = $customer;
            }

            $customers[] = $customer;
        }

        $searchOrder = [
            'customer_id' => $authenticatedUser->getKey(),
        ];

        $orderValues = [
            'name' => $plan->getName(),
            'customer_id' => $mainCustomer->id,
            'identifier' => 'R-'.Uuid::generate()->string,
        ];

        if (isset($musementOrder) && $musementOrder['uuid']) {
            $searchOrder['musement_uuid'] = $musementOrder['uuid'];

            $orderValues = array_merge($orderValues, [
                'musement_identifier' => $musementOrder['identifier'],
                'musement_uuid' => $musementOrder['uuid'],
                'currency' => $musementOrder['total_price']['currency'],
                'total_price' => $musementOrder['total_price']['value'],
                'date' => $musementOrder['date'],
                'status' => $musementOrder['status'],
            ]);
        }

        $order = Order::query()
            ->updateOrCreate($searchOrder, $orderValues);

        $orderItems = [];
        foreach ($plan->items() as $item) {
            $orderItem = false;
            $timeslot = $item->getTimeslot()->toArray();

            if ($item->getType() === ItemType::STANDARD) {
                $attributes = [
                    'order_id' => $order->id,
                    'type' => $item->getType(),
                    'place_id' => $item->getExternalId('place_id'),
                    'city_id' => $item->getExternalId('city_id'),
                ];

                $values = [
                    'order_id' => $order->id,
                    'identifier' => Uuid::generate()->string,
                    'name' => $item->getName(),
                    'type' => $item->getType(),
                    'date' => $timeslot['start_date'],
                    'start_time' => $timeslot['start_time'],
                    'end_time' => $timeslot['end_time'],
                    'place_id' => $item->getExternalId('place_id'),
                    'city_id' => $item->getExternalId('city_id'),
                    'total_price' => 0,
                ];
            } else {
                $attributes = [
                    'order_id' => $order->id,
                    'type' => $item->getType(),
                    'activity_uuid' => $item->getExternalId('activity_uuid'),
                    'city_id' => $item->getExternalId('city_id'),
                ];

                $values = [
                    'order_id' => $order->id,
                    'identifier' => Uuid::generate()->string,
                    'name' => $item->getName(),
                    'type' => $item->getType(),
                    'date' => $timeslot['start_date'],
                    'start_time' => $timeslot['start_time'],
                    'end_time' => $timeslot['end_time'],
                    'place_id' => $item->getExternalId('place_id'),
                    'activity_uuid' => $item->getExternalId('activity_uuid'),
                    'city_id' => $item->getExternalId('city_id'),
                    'total_price' => 0,
                ];
            }

            $orderItem = OrderItem::query()
                ->updateOrCreate($attributes, $values);

            foreach ($item->travellers() as $traveller) {
                $traveller = $traveller->toArray();

                if (! isset($customerKeyMap[$traveller['reference_key']])) {
                    continue;
                }

                OrderItemCustomer::query()
                    ->updateOrCreate(
                        [
                            'customer_id' => $customerKeyMap[$traveller['reference_key']],
                            'order_item_id' => $orderItem->id,
                        ],
                        [
                            'customer_id' => $customerKeyMap[$traveller['reference_key']],
                            'order_item_id' => $orderItem->id,
                        ]
                    );
            }

            if ($item->getType() === ItemType::ACTIVITY) {
                foreach ($item->externalActivities() as $musementItem) {
                    $musementOrderProduct = false;
                    foreach ($musementOrder['items'] as $tempItem) {
                        if ($tempItem['product']['id'] === $musementItem['product_id']) {
                            $musementOrderProduct = $tempItem;
                            break;
                        }
                    }

                    if (! $musementOrderProduct) {
                        continue;
                    }

                    $orderItemProduct = OrderItemProduct::query()
                        ->updateOrCreate(
                            [
                                'order_item_id' => $orderItem->id,
                                'musement_product_id' => strval($musementItem['product_id']),
                                'musement_order_item_id' => $musementOrderProduct['uuid'],
                            ],
                            [
                                'order_item_id' => $orderItem->id,
                                'musement_product_id' => strval($musementItem['product_id']),
                                'musement_order_item_id' => $musementOrderProduct['uuid'],
                                'musement_transaction_code' => $musementOrderProduct['transaction_code'],
                                'musement_type' => $musementOrderProduct['product']['type'],
                                'musement_activity_uuid' => $musementOrderProduct['product']['activity_uuid'],
                                'title' => $musementOrderProduct['product']['title'],
                                'currency' => $musementOrderProduct['product']['retail_price']['currency'],
                                'price' => $musementOrderProduct['product']['retail_price']['value'],
                                'status' => $musementOrderProduct['status'],
                            ]
                        );

                    foreach ($musementItem['travellers'] as $tempTravellerKey) {
                        if (! isset($customerKeyMap[$tempTravellerKey])) {
                            continue;
                        }

                        OrderItemProductCustomer::query()
                            ->updateOrCreate(
                                [
                                    'customer_id' => $customerKeyMap[$tempTravellerKey],
                                    'order_item_product_id' => $orderItemProduct->id,
                                ],
                                [
                                    'customer_id' => $customerKeyMap[$tempTravellerKey],
                                    'order_item_product_id' => $orderItemProduct->id,
                                ]
                            );
                    }
                }
            }

            if ($orderItem) {
                $orderItems[] = $orderItem;
            }
        }

        $success = Cart::removePlan($plan->getKey());

        $message = [
            'type' => 'error',
            'text' => 'Unfortunately, the order could not be processed at the moment.',
        ];
        if ($success) {
            $message = [
                'type' => 'success',
                'text' => 'Congratulations! Your order has been processed successfully. Have a Great Trip !',
            ];

            NotificationManager::decrease();
        }

        return response()->json([
            'success' => $success,
            'message' => $message,
        ]);
    }
}
