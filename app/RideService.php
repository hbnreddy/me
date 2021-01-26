<?php

namespace App;

use App\Cart\Cart;
use App\Cart\PlanItemType;
use App\Cart\RideItem;
use App\Http\Api\Kiwi\KiwiApi;
use App\Traits\Fails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RideService
{
    use Fails;

    public function cheapestRoute($parameters)
    {
        $cacheKeyPart = '';
        foreach ($parameters['activities'] as $activity) {
            $cacheKeyPart = implode('.', [implode('.', $activity), $cacheKeyPart]);
        }

        $cacheKey = implode('.', [
            $parameters['fly_from'],
            $cacheKeyPart,
            $parameters['fly_to'],
            $parameters['fly_from'],
            $parameters['date_from'],
            $parameters['date_to'],
            $parameters['adults'],
            $parameters['children'],
            $parameters['infants'],
        ]);

        return Cache::remember($cacheKey, 1000 * 60 * 60, function () use ($parameters) {
            $kiwiApi = resolve(KiwiApi::class);

            $startDate = $parameters['date_from'];
            $endDate = $parameters['date_to'];

            $flyFrom = $parameters['fly_from'];
            $flyTo = $parameters['fly_to'];

            $routes = [];

            $activities = $parameters['activities'];

            $parameters = array_except($parameters, ['activities']);
            if (count($activities)) {
                foreach ($activities as $index => $activity) {
                    if ($index === 0) {
                        $routes[] = array_merge($parameters, [
                            'date_from' => $startDate,
                            'date_to' => $startDate,
                            'fly_from' => $flyFrom,
                            'fly_to' => $activity['city_code'],
                        ]);
                    } else {
                        $prevItem = $activities[$index - 1];
                        $nextDate = Carbon::createFromFormat(AppDateFormat::RIDE_DATE_FORMAT, $prevItem['date'])
                            ->addDay()->format(AppDateFormat::RIDE_DATE_FORMAT);

                        $routes[] = array_merge($parameters, [
                            'date_from' => $nextDate,
                            'date_to' => $nextDate,
                            'fly_from' => $prevItem['city_code'],
                            'fly_to' => $activity['city_code'],
                        ]);
                    }
                }

                $nextDate = Carbon::createFromFormat(AppDateFormat::RIDE_DATE_FORMAT, $activity['date'])
                    ->addDay()->format(AppDateFormat::RIDE_DATE_FORMAT);

                $routes[] = array_merge($parameters, [
                    'date_from' => $nextDate,
                    'date_to' => $nextDate,
                    'fly_from' => $activity['city_code'],
                    'fly_to' => $flyTo,
                ]);
            } else {
                $routes[] = array_merge($parameters, [
                    'date_from' => $startDate,
                    'date_to' => $startDate,
                    'fly_from' => $flyFrom,
                    'fly_to' => $flyTo,
                ]);
            }

            $routes[] = array_merge($parameters, [
                'date_from' => $endDate,
                'date_to' => $endDate,
                'fly_from' => $flyTo,
                'fly_to' => $flyFrom,
            ]);

            $requestsParameters = [
                'body' => $routes,
                'sort' => 'quality',
            ];

            $flights = \GuzzleHttp\json_decode($kiwiApi->multiSearch($requestsParameters), true);

            $entity = isset($flights[0]) ? $flights[0] : false;
            $entity = isset($entity[0]) ? $entity[0] : $entity;

            if ($entity && count($entity['route'])) {
                $result = $this->formattedEntity($entity);

                return $result ? $result : false;
            }

            return false;
        });
    }

    public function cheapestRidesRoute1($item)
    {
        $defaultParameters = [
            'sort' => 'quality',
            'flight_type' => RideItem::ONEWAY_TYPE,
        ];

        $kiwiApi = resolve(KiwiApi::class);

        $requestsParams = [];
        if (Cart::hasCurrentPlan()) {
            $plan = Cart::currentPlan();
            $availableDates = $plan->availableDates();

            if (! count($availableDates) || ! isset($availableDates[1])) {
                return $this->jsonFails('No more days available');
            }

            $planLastDate = date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($plan->lastDate()));

            $planRidesItems = $plan->toRidesApi();
            if (count($planRidesItems)) {
                foreach ($planRidesItems as $planRide) {
                    if ($planRide['date_from'] !== $planLastDate) {
                        $requestsParams[] = $planRide;
                        $prevLastItem = $planRide;
                    }

                    $lastItem = $planRide;
                }

                $item = array_merge($item, [
                    'date_from' => date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($availableDates[1])),
                    'date_to' => date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($availableDates[1])),
                    'return_from' => $planLastDate,
                    'return_to' => $planLastDate,
                    'fly_from' => $prevLastItem['fly_to'],
                ]);
            }
        }

        $requestsParams[] = $item;

        $requestsParams[] = array_merge($item, [
            'date_from' => isset($item['return_from']) ? $item['return_from'] : $item['date_from'],
            'date_to' => isset($item['return_to']) ? $item['return_to'] : $item['date_to'],
            'fly_from' => $item['fly_to'],
            'fly_to' => isset($lastItem) ? $lastItem['fly_to'] : $item['fly_from'],
        ]);

        foreach ($requestsParams as &$requestsParam) {
            $requestsParam = array_merge($requestsParam, $defaultParameters);
        }

        $parameters = [
            'body' => $requestsParams,
            'sort' => 'quality',
        ];

        $flights = \GuzzleHttp\json_decode($kiwiApi->multiSearch($parameters), true);
        $entity = isset($flights[0]) ? $flights[0] : false;
        $entity = isset($entity[0]) ? $entity[0] : $entity;

        if ($entity && count($entity['route'])) {
            $result = $this->formattedEntity($entity);
            return $result ? $result : false;
        }

        return false;
    }

    public function planRidesRoute()
    {
        if (! Cart::hasCurrentPlan()) {
            return false;
        }

        $kiwiApi = resolve(KiwiApi::class);

        $defaultParameters = [
            'sort' => 'quality',
            'flight_type' => RideItem::ONEWAY_TYPE,
        ];

        $plan = Cart::currentPlan();
        $availableDates = $plan->availableDates();

        if (! count($availableDates)) {
            return $this->jsonFails('No more days available');
        }

        $planLastDate = date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($plan->lastDate()));
        $planRidesItems = $plan->toRidesApi();

        if (! count($planRidesItems)) {
            return false;
        }

        foreach ($planRidesItems as $planRide) {
            if ($planRide['date_from'] !== $planLastDate) {
//                if ($planRide['fly_to'] === $plan->getOriginCityCode()) {
//                    $planRide = array_merge($planRide, [
//
//                    ]);
//                }

                $requestsParams[] = $planRide;
            }

            $lastItem = $planRide;
        }

        foreach ($requestsParams as &$requestsParam) {
            $requestsParam = array_merge($requestsParam, $defaultParameters);
        }

        $requestsParams[] = $lastItem;

        $parameters = [
            'body' => $requestsParams,
            'sort' => 'quality',
        ];

        $flights = \GuzzleHttp\json_decode($kiwiApi->multiSearch($parameters), true);
        $entity = isset($flights[0]) ? $flights[0] : false;

        if ($entity && count($entity['route'])) {
            $result = $this->formattedEntity($entity);
            return $result ? $result : false;
        }

        return false;
    }

    public function cheapestRide($parameters)
    {
        $kiwiApi = resolve(KiwiApi::class);

        $searchRides = \GuzzleHttp\json_decode($kiwiApi->search($parameters), true);

        $entity = false;
        if ($searchRides && count($searchRides['data'])) {
            $entity = $searchRides['data'][0];
        }

        if ($entity && count($entity['route']) >= 2) {
            return $this->formattedEntity($entity);
        }

        return false;
    }

    public function processDates($startDate, $endDate, $dateType)
    {
        $startMonth = Carbon::createFromFormat(AppDateFormat::RIDE_DATE_FORMAT, $startDate)->startOfMonth();
        $endMonth = Carbon::createFromFormat(AppDateFormat::RIDE_DATE_FORMAT, $endDate)->lastOfMonth();

        $lastMonthDate = $endMonth->format(AppDateFormat::RIDE_DATE_FORMAT);

        $nightsCount = TripType::countNights($dateType);
        $endMonth->subDays($nightsCount > 2 ? $nightsCount : 0);

        if (! $nightsCount) {
            $parameters = [
                'return_from' => $endDate,
                'return_to' => $endDate,
                'date_to' => $startDate,
            ];
        } else {
            $parameters = [
                'flight_type' => 'round',
                'only_weekends' => intval($nightsCount === 2),
                'nights_in_dst_from' => $nightsCount,
                'nights_in_dst_to' => $nightsCount,
                'date_from' => $startMonth->format(AppDateFormat::RIDE_DATE_FORMAT),
                'date_to' => $endMonth->format(AppDateFormat::RIDE_DATE_FORMAT),
            ];

            if ($nightsCount === 7) {
                $parameters['return_from'] = $startMonth->addDays($nightsCount)->format(AppDateFormat::RIDE_DATE_FORMAT);
                $parameters['return_to'] = $lastMonthDate;
            }
        }

        if (isset($parameters['only_weekends']) && $parameters['only_weekends']) {
            unset($parameters['nights_in_dst_from']);
            unset($parameters['nights_in_dst_to']);
        }

        return $parameters;
    }

    public function formattedEntity($entity)
    {
        $routes = [];
        foreach ($entity['route'] as $route) {
            $routes[] = [
                'id' => $route['id'],
                'from_city_code' => $route['cityCodeFrom'],
                'to_city_code' => $route['cityCodeTo'],
                'price' => $route['price'],
                'type' => PlanItemType::RIDE_ITEM,
                'date' => date(AppDateFormat::BASE_DATE_FORMAT, $route['dTime']),
                'duration' => ($route['aTime'] - $route['dTime']) / 60,
                'timeslot' => [
                    'start_date' => date(AppDateFormat::BASE_DATE_FORMAT, $route['dTime']),
                    'start_time' => date(AppDateFormat::BASE_TIME_FORMAT, $route['dTime']),
                    'end_date' => date(AppDateFormat::BASE_DATE_FORMAT, $route['aTime']),
                    'end_time' => date(AppDateFormat::BASE_TIME_FORMAT, $route['aTime']),
                ],
                'airline' => isset($route['airline']) ? $route['airline'] : false,
            ];
        }

        return [
            'id' => isset($entity['id']) ? $entity['id'] : implode('|', array_pluck($routes, 'id')),
            'price' => $entity['price'],
            'departure_date' => date('Y-m-d', $entity['route'][0]['dTime']),
            'return_date' => date('Y-m-d', $entity['route'][count($entity['route']) - 1]['dTime']),
            'routes' => $routes,
        ];
    }

    public static function generateKey($parameters)
    {
        $keys = [
            $parameters['fly_from'],
            implode('.', $parameters['fly_to']),
            $parameters['flight_type'],
            $parameters['date_from'],
            $parameters['date_to'],
            $parameters['adults'],
            $parameters['children'],
            $parameters['infants'],
        ];

        return sha1(implode('.', $keys));
    }
}
