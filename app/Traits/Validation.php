<?php

namespace App\Traits;

use App\Cart\PlanItemType;
use Illuminate\Support\Facades\Validator;

trait Validation
{
    public function validateQuery($requestParams)
    {
        return Validator::make($requestParams, [
            'origin_city_id' => 'required|integer',
            'travellers' => 'required|string',
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'date_type' => 'required|string',
            'currency' => 'required|string',
        ]);
    }

    public function validateTraveller($requestParams)
    {
        return Validator::make($requestParams, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'middle_name' => 'required|string',
            'email' => 'required|string',
            'birthdate' => 'required|string',
            'passport_start_date' => 'required|string',
            'passport_end_date' => 'required|string',
            'gender' => 'required|string',
            'nationality' => 'required|string',
            'mobile_number' => 'required|string',
            'house_number' => 'required|string',
            'street_number' => 'required|string',
            'area' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'pincode' => 'required|string',
        ]);
    }

    public function validateItem($type, $requestParams)
    {
        switch ($type) {
            case PlanItemType::STANDARD_ITEM: {
                return $this->validateStandardItem($requestParams);
            }
            case PlanItemType::ACTIVITY_ITEM: {
                return $this->validateActivityItem($requestParams);
            }
            case PlanItemType::RIDE_ITEM: {
                return $this->validateRideItem($requestParams);
            }
            default: {
                return false;
            }
        }
    }

    protected function validateStandardItem($requestParams)
    {
        return Validator::make($requestParams, [
            'external_id' => 'required|string',
            'name' => 'required|string',
            'city_id' => !$requestParams['modify'] ? 'required|integer' : 'integer|nullable',
            'start_time' => 'required|string',
            'end_date' => 'required|string',
            'end_time' => 'required|string',
            'travellers' => 'array',
        ]);
    }

    protected function validateActivityItem($requestParams)
    {
        return Validator::make($requestParams, [
            'external_id' => 'required|string',
            'name' => 'required|string',
            'city_id' => !$requestParams['modify'] ? 'required|integer' : 'integer|nullable',
            'start_time' => 'required|string',
            'end_date' => 'required|string',
            'end_time' => 'required|string',
            'travellers' => 'array',
            'products' => 'array',
            'identifiers' => 'array',
        ]);
    }

    protected function validateRideItem($requestParams)
    {
        return Validator::make($requestParams, [
            'external_id' => 'required|string',
//            'start_date' => 'required|string',
//            'start_time' => 'required|string',
//            'end_date' => 'required|string',
//            'end_time' => 'required|string',
            'from_city_code' => 'required|string',
            'to_city_code' => 'required|string',
//            'price' => 'required|numeric',
            'travellers_ids' => 'array',
            'travellers' => 'array',
        ]);
    }
}
