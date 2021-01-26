<?php

namespace App;

use App\Http\Api\Musement\MusementApi;

class AppTest
{
    public static function test()
    {
        $musementApi = resolve(MusementApi::class);

        $activities = json_decode($musementApi->getVenueActivities(3, [
            'start_date' => '2020-03-25',
            'end_date' => '2020-04-25',
        ]), true);

        // https://sandbox.musement.com/api/v3/activities/a0eb01b4-588c-4a03-9568-efc971bcc857/dates/2020-04-05

        $activityUuid = 'a0eb01b4-588c-4a03-9568-efc971bcc857';
        $productId = 'zMH2Z73ARYnVY+jXnbtL5s2sCcBtAxNXYxYqLhVhfQst6rkpgV9O49TFtXJEfmpmSzcljrx50cx1PLq0BoGwGV0GVVHF7GoeaR8JRuP778R7Gfi3TRO+vWgEpzZ/MPsaVCMrTVYDbkTQROdJB1AcE9lOL3HxYISdbNXTHxpKz6tKsaIcssP7YK1CUQ3h0sQc7Ck3fni8psxT5x4kaRvECUD6JkNL5v8yjBBfzHCYUTBK6eHRvm1QNBZAt9t9NxZzfsppoZ0cvpwklirhfkkwUTBSlSqIWpy9YBVm+n47X25FEnRDXpmMqYDDv/KimhaKDSqop0L4aNQbNK31OWh5X7nHsvAhbr5QOeEOZ7BdQodcL+FJYDZlrVgdsIyw8pUQ1tzoB1LNGYek1DHY8MhJzikVclPiVsEPOh2X6vtP4zo=';

        $cartUuid = 'f591f1ac-815a-4bbb-8581-be443f4b4b75';
        $cart = json_decode($musementApi->getCart($cartUuid), true);

        dd($cart);

        $itemModel = [
            'type' => 'musement-realtime',
            'product_identifier' => $productId,
            'quantity' => 1,
        ];

        $addItem = json_decode($musementApi->addItemToCart($cart['uuid'], $itemModel));

        dd(123, $addItem);

//        $activityDateInfo = json_decode($musementApi->getActivityDateInfo($activityUuid, '2020-03-26'), true);

        $cart = json_decode($musementApi->getCart($cartUuid));
        dd($cart);

        $activityUuid = $activities[1]['uuid'];

        $activity = json_decode($musementApi->getActivity($activityUuid), true);

        dd($activity['uuid']);

        $internalActivity = [
            'uuid' => $activity['uuid'],
            'booking_type' => $activity['booking_type'],
            'venues' => $activity['venues'],
        ];

        $activityDates = json_decode($musementApi->getActivityDates($activityUuid, [
            'start_date' => '2020-03-25',
            'end_date' => '2020-04-25',
        ]), true);
        $activityDateInfo = json_decode($musementApi->getActivityDateInfo($activityUuid, '2020-03-26'), true);

        dd($internalActivity['uuid'], $activityDateInfo);
    }
}
