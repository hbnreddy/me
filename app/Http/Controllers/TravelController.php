<?php

namespace App\Http\Controllers;

use App\Http\Api\TravelFusion\Travel\ITravelService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Travel\BookTravelRequest;
use App\Http\Requests\Travel\CheckoutTravelPaymentRequest;
use App\Http\Requests\Travel\CheckTravelGroupRequest;
use App\Http\Requests\Travel\ProcessTravelRequest;
use App\Http\Requests\Travel\GetCheapestRouteRequest;
use App\Http\Requests\Travel\GetTravelGroupsRequest;
use App\Http\Requests\Travel\GetTravelRequest;
use App\Services\RideService;
use Illuminate\Http\JsonResponse;

class TravelController extends Controller
{
    private $travelService;

    /**
     * TravelController constructor.
     * @param ITravelService $travelService
     */
    public function __construct(ITravelService $travelService)
    {
        $this->travelService = $travelService;
    }

    /**
     * @param GetTravelRequest $request
     * @return JsonResponse
     */
    public function index(GetTravelRequest $request)
    {
        $statusCode = 200;

        try {
            $response = [
                'entities' => $this->travelService->all($request->toQuery()),
            ];
        } catch (\Exception $exception) {
            $response = [
                'error' => 'Invalid request.',
                'message' => $exception->getMessage(),
            ];

            $statusCode = 400;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * @param GetTravelGroupsRequest $request
     * @param RideService $rideService
     * @return JsonResponse
     */
    public function groups(GetTravelGroupsRequest $request, RideService $rideService)
    {
        $statusCode = 200;

        try {
            $response = [
                'entities' => $this->travelService->groups($request->toQuery()),
            ];
        } catch (\Exception $exception) {
            $response = [
                'error' => 'Invalid request.',
                'message' => $exception->getMessage(),
            ];

            $statusCode = 400;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * @param GetCheapestRouteRequest $request
     * @return JsonResponse
     */
    public function cheapest(GetCheapestRouteRequest $request)
    {
        $statusCode = 200;

        try {
            $response = [
                'entity' => $this->travelService->cheapest($request->toQuery()),
            ];
        } catch (\Exception $exception) {
            $response = [
                'error' => 'Invalid request.',
                'message' => $exception->getMessage(),
            ];

            $statusCode = 400;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * @param $routingId
     * @param $outwardId
     * @return JsonResponse
     */
    public function terms($routingId, $outwardId)
    {
        $statusCode = 200;

        try {
            $response = [
                'entities' => $this->travelService->terms([
                    'routing_id' => $routingId,
                    'outward_id' => $outwardId,
                ]),
            ];
        } catch (\Exception $exception) {
            $response = [
                'error' => 'Invalid request.',
                'message' => $exception->getMessage(),
            ];

            $statusCode = 400;
        }

        return response()->json($response, $statusCode);
    }

    public function checkGroup(CheckTravelGroupRequest $request)
    {
        $statusCode = 200;

        try {
            $group = $request->toQuery()['group'];

            foreach ($group as $travelItem) {
                $success = $this->travelService->terms([
                    'routing_id' => $travelItem['routing_id'],
                    'outward_id' => $travelItem['id'],
                ]);

                if (! $success) {
                    throw new \Exception('Travel group not available anymore.');
                }
            }

            $response = [
                'terms' => $success,
                'success' => true,
            ];
        } catch (\Exception $exception) {
            $response = [
                'error' => 'Invalid request.',
                'message' => $exception->getMessage(),
            ];

            $statusCode = 400;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * @param ProcessTravelRequest $request
     * @param $routingId
     * @param $outwardId
     * @return JsonResponse
     */
    public function process(ProcessTravelRequest $request, $routingId, $outwardId)
    {
        $statusCode = 200;

        try {
            $response = [
                'entity' => $this->travelService->process([
                    'routing_id' => $routingId,
                    'outward_id' => $outwardId,
                    'travellers' => $request->toQuery(),
                ]),
            ];
        } catch (\Exception $exception) {
            $response = [
                'error' => 'Invalid request.',
                'message' => $exception->getMessage(),
            ];

            $statusCode = 400;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * @param BookTravelRequest $request
     * @param $bookingId
     * @return JsonResponse
     */
    public function book(BookTravelRequest $request, $bookingId)
    {
        $statusCode = 200;

        try {
            $response = [
                'entity' => $this->travelService->book(array_merge([
                    'booking_id' => $bookingId
                ], $request->toQuery())),
            ];
        } catch (\Exception $exception) {
            $response = [
                'error' => 'Invalid request.',
                'message' => $exception->getMessage(),
            ];

            $statusCode = 400;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * @param $bookingId
     * @return JsonResponse
     */
    public function bookingStatus($bookingId)
    {
        $statusCode = 200;

        try {
            $response = [
                'entities' => $this->travelService->bookingStatus([
                    'booking_id' => $bookingId
                ]),
            ];
        } catch (\Exception $exception) {
            $response = [
                'error' => 'Invalid request.',
                'message' => $exception->getMessage(),
            ];

            $statusCode = 400;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * @param $bookingId
     * @return JsonResponse
     */
    public function bookingDetails($bookingId)
    {
        $statusCode = 200;

        try {
            $response = [
                'entities' => $this->travelService->bookingDetails([
                    'booking_id' => $bookingId
                ]),
            ];
        } catch (\Exception $exception) {
            $response = [
                'error' => 'Invalid request.',
                'message' => $exception->getMessage(),
            ];

            $statusCode = 400;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * @param $bookingId
     * @return JsonResponse
     */
    public function cancelBooking($bookingId)
    {
        $statusCode = 200;

        try {
            $response = [
                'entities' => $this->travelService->cancelBooking([
                    'booking_id' => $bookingId
                ]),
            ];
        } catch (\Exception $exception) {
            $response = [
                'error' => 'Invalid request.',
                'message' => $exception->getMessage(),
            ];

            $statusCode = 400;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * @param $bookingId
     * @return JsonResponse
     */
    public function checkCancelBooking($bookingId)
    {
        $statusCode = 200;

        try {
            $response = [
                'entities' => $this->travelService->cancelBookingStatus([
                    'booking_id' => $bookingId
                ]),
            ];
        } catch (\Exception $exception) {
            $response = [
                'error' => 'Invalid request.',
                'message' => $exception->getMessage(),
            ];

            $statusCode = 400;
        }

        return response()->json($response, $statusCode);
    }
}
