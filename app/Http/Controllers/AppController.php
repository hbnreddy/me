<?php

namespace App\Http\Controllers;

use App\AppCache;
use App\AppConfig;
use App\Cart\Cart;
use App\Eloquent\City;
use App\Eloquent\Marker;
use App\Eloquent\Place;
use App\Eloquent\Theme;
use App\NotificationManager;
use App\Traits\Validation;
use App\Cart\TravellerType;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AppController extends Controller
{
    use Validation;

    /**
     * @return Factory|View
     * @throws FileNotFoundException
     */
    public function home(Request $request)
    {
        NotificationManager::init();

        $success = Storage::disk('local')->exists('settings.json');

        $textBoxes = [];
        if ($success) {
            $settings = json_decode(Storage::disk('local')->get('settings.json'), true);

            if (isset($settings['text_boxes'])) {
                $textBoxes = $settings['text_boxes'];
            }
        }

        $notifications = NotificationManager::getNotifications();

        $data = [
            'message' => [
                'text' => '',
                'type' => '',
            ],
            'user' => Auth::user(),
            'notifications' => $notifications,
            'themes' => AppCache::themes(),
            'text_boxes' => $textBoxes,
            'updated' => true,
            'locale' => AppConfig::getLocale(),
        ];

        return view('welcome', compact('data'));
    }

    /**
     * @param Request $request
     * @return Factory|RedirectResponse|View
     */
    public function explore(Request $request)
    {
        $validator = $this->validateQuery($request->all());

        if ($validator->fails()) {
            return redirect()->route('app.root');
        }

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

        return view('explore', compact('data'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Factory|View
     */
    public function place(Request $request)
    {
        $validator = $this->validateQuery($request->all());

        if ($validator->fails()) {
            return redirect()->route('app.root');
        }

        $id = intval($request->route('id'));

        AppConfig::setCurrency($request->input('currency'));

        $themes = array_map('intval', explode('-', $request->input('themes')));
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

        $flightId = false;
        if ($request->has('flight_id')) {
            $flightId = $request->input('flight_id');
        }

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
            'date' => $date,
            'themes' => $themes,
            'flight_id' => $flightId,
        ];

        $place = Place::query()
            ->with([
                'city',
                'media_resources',
                'venue',
            ])
            ->find($id);

        $cart = Cart::get();
        $currentPlan = false;
        if (isset($cart['current_plan_id'])) {
            $currentPlan = $cart['current_plan_id'];
        }

        $message = [
            'text' => '',
            'type' => '',
        ];
        if (Session::has('message')) {
            $message = Session::get('message');

            Session::forget('message');
        }

        $data = [
            'message' => $message,
            'locale' => AppConfig::getLocale(),
            'user' => Auth::user(),
            'query' => $query,
            'notifications' => NotificationManager::getNotifications(),
            'place' => $place,
            'current_plan_id' => $currentPlan,
        ];

        return view('place', compact('data'));
    }

    public function booking(Request $request)
    {
//        dd(Cart::get());

        $validator = $this->validateQuery($request->all());

        if ($validator->fails()) {
            return redirect()->route('app.root');
        }

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

        $date['start_date'] = $request->input('start_date');
        $date['end_date'] = $request->input('end_date');
        $date['date_type'] = $request->input('date_type');

        $originCity = City::query()
            ->find($request->input('origin_city_id'));

        $query = [
            'currency' => AppCache::currencies()
                ->where('code', strtoupper(AppConfig::getCurrency()))
                ->first(),
            'locale' => AppConfig::getLocale(),
            'origin_city' => [
                'id' => $originCity->id,
                'full_name' => $originCity->name.(
                    isset($originCity->name_suffix)
                        ? ', '.$originCity->name_suffix
                        : ''
                    ),
            ],
            'travellers' => $travellers,
            'date' => $date,
            'themes' => $themes,
        ];

        $cart = Cart::get();
        $currentPlan = false;
        if (isset($cart['current_plan_id'])) {
            $currentPlan = $cart['current_plan_id'];
        }

        $data = [
            'message' => [
                'text' => '',
                'type' => '',
            ],
            'user' => Auth::user(),
            'query' => $query,
            'current_plan_id' => $currentPlan,
            'notifications' => NotificationManager::getNotifications(),
            'locale' => AppConfig::getLocale(),
        ];

        return view('booking', compact('data'));
    }
}
