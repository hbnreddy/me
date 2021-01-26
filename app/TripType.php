<?php

namespace App;

class TripType
{
    public const EXACT_DATES_TRIP = 'exact';
    public const FIFTEEN_DAYS_TRIP = '15_days_trip';
    public const ONE_WEEK_TRIP = '1_week_trip';
    public const WEEKEND_TRIP = 'weekend_trip';
    private static $nightsCount = [
        self::EXACT_DATES_TRIP => 0,
        self::FIFTEEN_DAYS_TRIP => 15,
        self::ONE_WEEK_TRIP => 7,
        self::WEEKEND_TRIP => 2,
    ];

    public static function countNights($dateType = self::EXACT_DATES_TRIP)
    {
        return self::$nightsCount[$dateType];
    }
}
