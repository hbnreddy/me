<?php

namespace App\Cart;

use App\Cart\Item;
use App\Cart\Representational;
use App\Cart\Timeslot;
use App\Eloquent\City;
use App\Http\Api\Kiwi\KiwiApi;
use App\Http\Api\Musement\MusementApi;
use App\Traits\HasExternalIds;
use App\Traits\HasKey;
use Illuminate\Support\Str;

class Plan implements Representational
{
    use HasKey, HasExternalIds;

    protected $id;
    protected $name;
    protected $items = [];
    protected $origin_city_id = false;
    protected $timeslot;
    protected $travellers = [];
    protected $itinerary;
    protected $externalIds;

    /**
     * Plan constructor.
     */
    public function __construct()
    {
        $this->id = Str::random();
        $this->name = 'New plan';

        $this->externalIds = [
            'musement_cart_id' => null,
        ];
    }

    public function hasMusementItems()
    {
        return !!count($this->itemsByType(PlanItemType::ACTIVITY_ITEM));
    }

    public function items($date = false)
    {
        if (! $date) {
            return $this->items;
        }

        $items = [];
        foreach ($this->items as $item) {
            if ($item->getType() === PlanItemType::RIDE_ITEM) {
                continue;
            }

            if ($item->getDate() === $date) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public function toRidesApi()
    {
        $ridesMap = [];
        foreach ($this->itemsByType(PlanItemType::RIDE_ITEM) as $item) {
            //$item->toUniqueId()
            $ridesMap[] = $item->toApi();
        }

        return $ridesMap;
    }

    public function itemsByType($type = PlanItemType::STANDARD_ITEM, $toArray = false)
    {
        $items = [];
        foreach ($this->items as $item) {
            if ($item->getType() === $type) {
                $items[] = $toArray ? $item->toArray() : $item;
            }
        }

        return $items;
    }

    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * Add item to plan.
     *
     * @param Item $item
     * @return bool
     */
    public function addItem(Item $item)
    {
        $this->items[] = $item;

        $this->sortItems();

        return true;
    }

    public function setOriginCityId($id)
    {
        $this->origin_city_id = $id;
    }

    public function getOriginCityId()
    {
        return $this->origin_city_id;
    }

    protected function sortItems()
    {
        usort($this->items, function ($a, $b) {
            return $a->getDate() > $b->getDate();
        });
    }

    public function routeExists($fromCityCode, $toCityCode)
    {
        foreach ($this->itemsByType(PlanItemType::RIDE_ITEM) as $item) {
            if ($item->getFromCityCode() === $fromCityCode && $item->getToCityCode() === $toCityCode) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update item from plan.
     *
     * @param Item $item
     * @return bool
     */
    public function updateItem(Item $item)
    {
        foreach ($this->items as $index => $planItem) {
            if ($planItem->getKey() === $item->getKey()) {
                $this->items[$index] = $planItem;

                $this->sortItems();

                return true;
            }
        }

        return false;
    }

    public function removeItem($id)
    {
        $items = $this->items();
        foreach ($items as $index => $item) {
            if ($item->getKey() === $id) {
                if ($item->getType() === PlanItemType::ACTIVITY_ITEM) {
                    $musementApi = resolve(MusementApi::class);

                    $musementItems = $item->externalActivities();

                    $removeCount = count($musementItems);
                    foreach ($musementItems as $musementItem) {
                        $removeSuccess = json_decode(
                            $musementApi->removeItemFromCart(
                                $this->getExternalId('musement_cart_id'), $musementItem['uuid']), true
                        );

                        if ($removeSuccess) {
                            $removeCount--;
                        }
                    }

                    if ($removeCount !== 0) {
                        return false;
                    }
                }

                array_splice($items, $index, 1);

                $this->setItems($items);
                return true;
            }
        }

        return false;
    }

    /**
     * Get item by id.
     *
     * @param $id
     * @return bool|mixed
     */
    public function getItem($id)
    {
        foreach ($this->items as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }

        return false;
    }

    public function setTimeslot(Timeslot $timeslot)
    {
        $this->timeslot = $timeslot;
    }

    public function getTimeslot()
    {
        return $this->timeslot;
    }

    public function setItinerary(Itinerary $itinerary)
    {
        $this->itinerary = $itinerary;
    }

    public function lastRide()
    {
        foreach ($this->itemsByType(PlanItemType::RIDE_ITEM) as $item) {
            if ($item->getDate() === $this->lastDate()) {
                return $item;
            }
        }

        return false;
    }

    public function startDate()
    {
        return $this->getTimeslot()->toArray()['start_date'];
    }

    public function lastDate()
    {
        return $this->getTimeslot()->toArray()['end_date'];
    }

    public function clearRides()
    {
        $items = $this->items();
        $this->setItems([]);
        foreach ($items as $item) {
            if ($item->getType() !== PlanItemType::RIDE_ITEM) {
                $this->addItem($item);
            }
        }

        return true;
    }

    public function getItinerary()
    {
        return $this->itinerary;
    }

    public function availableDates()
    {
        return array_map(function ($el) {
            return $el->getDate();
        }, $this->getItinerary()->availableDates());
    }

    public function getOriginCityCode()
    {
        return City::query()
            ->find($this->getOriginCityId())
            ->code;
    }

    /**
     * Set travellers to plan.
     *
     * @param $travellers
     */
    public function setTravellers($travellers)
    {
        $this->travellers = $travellers;
    }

    /**
     * Get plan travellers.
     *
     * @return array
     */
    public function getTravellers()
    {
        return $this->travellers;
    }

    /**
     * Rename plan.
     *
     * @param $newName
     */
    public function setName($newName)
    {
        $this->name = $newName;
    }

    /**
     * Return plan's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function musementCart()
    {
        if (! $cartId = $this->getExternalId('musement_cart_id')) {
            return false;
        }

        return json_decode(resolve(MusementApi::class)->getCart($cartId), true);
    }

    /**
     * Get representation as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->toArray();
        }

        $travellers = [];
        foreach ($this->travellers as $traveller) {
            $travellers[] = $traveller->toArray();
        }

        $timeslot = $this->getTimeslot()->toArray();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_date' => $timeslot['start_date'],
            'end_date' => $timeslot['end_date'],
            'travellers' => $travellers,
            'items' => $items,
        ];
    }

    public function countTravellers()
    {
        $counts = [
            'adults' => 0,
            'children' => 0,
            'infants' => 0,
        ];

        $travellers = $this->getTravellers();
        foreach ($travellers as $traveller) {
            $counts[$traveller->getType()]++;
        }

        return $counts;
    }

    public function getItineraryCities()
    {
        return $this->itinerary->cities();
    }

    public function toIndividualEstimate()
    {
        $travellers = [];
        foreach ($this->getTravellers() as $traveller) {
            $travellers[$traveller->getKey()] = array_merge($traveller->toShort(), ['total_payment' => 0]);
        }

        $kiwiApi = resolve(KiwiApi::class);
        $musementApi = resolve(MusementApi::class);

        $musementCart = $this->musementCart();
        $musementItemsPrices = [];
        if ($musementCart) {
            foreach ($musementCart['items'] as $item) {
                $musementItemsPrices[$item['uuid']] = $item['product']['retail_price']['value'];
            }
        }

        $externalActivities = [];
        foreach ($this->items() as $item) {
            if ($item->getType() === PlanItemType::ACTIVITY_ITEM) {
                $externalActivities = array_merge($externalActivities, $item->externalActivities());
            } elseif ($item->getType() === PlanItemType::RIDE_ITEM) {
                $cityFrom = City::query()->find($item->getFromCityId());
                $cityTo = City::query()->find($item->getToCityId());

                $apiData = \GuzzleHttp\json_decode($kiwiApi->getById($item->getExternalId('ride_id'), [
                    'fly_from' => $cityFrom->code,
                    'fly_to' => $cityTo->code,
                ]), true);

                if ($apiData['success']) {
                    $fare = $apiData['entity']['fare'];
                    $travellersIds = $item->getTravellersIds();

                    foreach ($travellers as &$traveller) {
                        if (in_array($traveller['id'], $travellersIds)) {
                            $traveller['total_payment'] += $fare[$traveller['type']];
                        }
                    }
                }
            }
        }

        foreach ($travellers as &$traveller) {
            foreach ($externalActivities as $externalActivity) {
                if (in_array($traveller['reference_key'], $externalActivity['travellers'])) {
                    $traveller['total_payment'] += $musementItemsPrices[$externalActivity['uuid']];
                }
            }
        }

        return array_values($travellers);
    }

    /**
     * Returns representation of estimate.
     *
     * @return array
     */
    public function toEstimate()
    {
        $totals = [
            'flights_price' => 0,
            'hotels_price' => 0,
            'activities_price' => 0,
            'discount_coupons' => [],
            'total_price' => 0,
        ];

//        $kiwiApi = resolve(KiwiApi::class);

        $musementCart = $this->musementCart();

        if ($musementCart) {
            $totals['activities_price'] += $musementCart['full_price']['value'];
        }

        $totals['total_price'] = array_sum($totals);

        foreach ($this->items() as $item) {
            if ($item->getType() === PlanItemType::RIDE_ITEM) {
//                $cityFrom = City::query()->find($item->getFromCityId());
//                $cityTo = City::query()->find($item->getToCityId());
//
//                $apiData = \GuzzleHttp\json_decode($kiwiApi->getById($item->getExternalId('ride_id'), [
//                    'fly_from' => $cityFrom->code,
//                    'fly_to' => $cityTo->code,
//                ]), true);
//
//                $price = 0;
//                if ($apiData['success']) {
//                    $fare = $apiData['entity']['fare'];
//
//                    $travellersCount = $item->getTravellersCount();
//                    foreach ($travellersCount as $key => $value) {
//                        $price += $fare[$key] * $value;
//                    }
//                }
//
//                $totals['flights_price'] += $price;
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'totals' => $totals,
        ];
//
//        $totals['total_price'] = $totals['flights_price']
//            + $totals['hotels_price']
//            + $totals['activities_price'];
//
//        return [
//            'id' => $this->id,
//            'name' => $this->name,
//            'travellers' => $this->travellers,
//            'totals' => $totals,
//        ];
    }

    /**
     * @inheritDoc
     */
    public function toShort()
    {
        return [
            'name' => $this->name,
        ];
    }

    /**
     * @inheritDoc
     */
    public function toDatabase()
    {
        // TODO: Implement toDatabase() method.
    }
}
