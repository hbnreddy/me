<?php

use Illuminate\Support\Facades\Route;


Route::get('notifications', 'NotificationController@index')->name('notifications.index');
/**
 * Access codes
 */
Route::get('access-codes', 'AccessCodeController@index');
Route::post('access-codes/store/{id?}', 'AccessCodeController@store');
Route::post('access-codes/{id}/delete', 'AccessCodeController@delete');

/**
 * Cart
 */
Route::prefix('cart')->group(function () {
    Route::prefix('plans')->group(function () {
        Route::get('/', 'CartController@plans')->name('cart.plans');
        Route::get('/estimates', 'CartController@estimates')->name('cart.plans.estimates');

        Route::post('/store', 'CartController@storePlan')->name('cart.plans.store');

        Route::prefix('{id}')->group(function () {
            Route::get('/{date}/remaining-hours', 'CartController@remainingHours')->name('cart.plans.remainingHours');
            Route::get('/itinerary', 'CartController@itinerary')->name('cart.plans.itinerary');
            Route::get('/available-days', 'CartController@availableDays')->name('cart.plans.availableDays');

            Route::get('/cities-per-dates', 'CartController@citiesPerDates');
            Route::get('/individual-estimate', 'CartController@individualEstimate');

            Route::post('/rename', 'CartController@renamePlan')->name('cart.plans.rename');

            // Items
            Route::prefix('items')->group(function () {
                Route::get('/', 'CartController@items')->name('cart.items');

                Route::post('/store/{item_id?}', 'CartController@storeItem')->name('cart.plans.items.store');
                Route::post('/store-rides', 'CartController@storeRideItems')->name('cart.plans.items.storeRideItems');

                Route::prefix('{item_id}')->group(function () {
                    Route::get('exists', 'CartController@itemExists')->name('cart.plans.items.exists');

                    Route::post('remove', 'CartController@removeItem')->name('cart.plans.items.remove');
                });
            });

            Route::get('/items-ids', 'CartController@itemsIds')->name('cart.itemsIds');

            // Travellers
            Route::prefix('travellers')->group(function () {
                Route::get('/', 'CartController@travellers')->name('cart.plans.travellers');

                Route::prefix('{item_id}')->group(function () {
                    Route::post('store', 'CartController@storeTraveller')->name('cart.plan.travellers.store');
                });
            });

            // Rides
            Route::prefix('rides')->group(function () {
                Route::get('/', 'CartController@rides')->name('cart.rides');
            });
        });
    });

    Route::post('/checkout', 'CartController@checkout')->name('cart.checkout');
    Route::post('/order', 'CartController@order')->name('cart.order');
});

// DEPRECATED / TODO: Check useful routes.
Route::prefix('cart')->group(function () {
    Route::prefix('plans')->group(function () {
        Route::prefix('{id}')->group(function () {
//                ->name('cart.individualEstimate');
        });
    });
});

/**
 * Continents
 */
Route::prefix('continents')->group(function () {
    Route::get('/', 'ContinentController@index')->name('continents.all');
    Route::get('/{id}', 'ContinentController@get')->name('continents.get');
    Route::post('/set-state', 'ContinentController@setState')->name('continents.setState');
});

/**
 * Countries
 */
Route::prefix('countries')->group(function () {
    Route::get('/', 'CountryController@index')->name('countries.all');
    Route::get('/{id}', 'CountryController@get')->name('countries.get');
    Route::post('/set-state', 'CountryController@setState')->name('countries.setState');
});

/**
 * Cities
 */
Route::get('/get-explore', 'CityController@exploreAll')->name('cities.exploreAll');
Route::prefix('cities')->group(function () {
    Route::get('/', 'CityController@index')->name('cities.all');
    Route::get('/search', 'CityController@search')->name('cities.search');
    Route::get('/explore-more', 'CityController@exploreMoreCities')->name('cities.exploreMoreCities');
    Route::post('/set-state', 'CityController@setState')->name('cities.setState');
    Route::post('/upload-image', 'CityController@uploadImage')->name('cities.uploadImage');
    Route::get('/explore', 'CityController@explore')->name('cities.explore');
    Route::prefix('{id}')->group(function () {
        Route::get('/', 'CityController@get')->name('cities.get');
        Route::get('/name', 'CityController@name')->name('cities.name');
        Route::get('/places', 'CityController@places')->name('cities.places');
        Route::get('/counts', 'CityController@counts')->name('cities.counts');
        Route::get('/cheapest-experience', 'CityController@cheapestExperience')->name('cities.cheapestExperience');
    });
});

/**
 * Places
 */
Route::prefix('places')->group(function () {
    Route::get('/', 'PlaceController@index')->name('places.all');
    Route::get('/by-ids', 'PlaceController@placesByIds')->name('places.placesByIds');
    Route::prefix('{id}')->group(function () {
        Route::get('/', 'PlaceController@get')->name('places.get');
        Route::get('/media', 'PlaceController@media')->name('places.media');
        Route::get('/theme', 'PlaceController@theme')->name('places.theme');
    });
    Route::post('/media', 'PlaceController@setVenue')->name('places.setVenue');
    Route::post('/set-venue', 'PlaceController@setVenue')->name('places.setVenue');
});

/**
 * Themes
 */
Route::prefix('themes')->group(function () {
    Route::get('/', 'ThemeController@index')->name('themes.all');
    Route::post('/store/{id?}', 'ThemeController@store')->name('themes.store');
    Route::post('/delete', 'ThemeController@destroy')->name('themes.destroy');
});

/**
 * Themes
 */
Route::prefix('travel')->group(function () {
    Route::get('/cheapest', 'TravelController@cheapest')->name('travel.cheapest');
});

/**
 * Markers
 */
Route::prefix('markers')->group(function () {
    Route::get('/', 'MarkerController@index')->name('markers.all');
    Route::post('/set-theme', 'MarkerController@setTheme')->name('markers.setTheme');
});

/**
 * Venues
 */
Route::prefix('venues')->group(function () {
    Route::get('/', 'VenueController@index')->name('venues.all');
    Route::get('/get-by-ids', 'VenueController@getByIds')->name('places.getByIds');
    Route::get('/total-count', 'VenueController@totalCount')->name('venues.totalCount');
    Route::post('/set-place', 'VenueController@setPlace')->name('venues.setPlace');
    Route::get('/{id}/information', 'VenueController@information')->name('venues.information');
    Route::get('/{id}/activities', 'VenueController@activities')->name('venues.activities');
    Route::get('/{id}/activities-count', 'VenueController@activitiesCount')->name('venues.activitiesCount');
    Route::get('/{id}/best-activity-price', 'VenueController@bestActivityPrice')->name('venues.bestActivityPrice');
});

/**
 * Activities
 */
Route::prefix('activities')->group(function () {
    Route::prefix('{id}')->group(function () {
        Route::get('/duration', 'ActivityController@duration')->name('activity.duration');
        Route::get('/venues', 'ActivityController@venues')->name('activity.venues');
        Route::get('/dates', 'ActivityController@dates')->name('activity.dates');
        Route::get('/media', 'ActivityController@media')->name('activity.media');
        Route::get('/taxonomies', 'ActivityController@taxonomies')->name('activity.taxonomies');
        Route::get('/refund-policies', 'ActivityController@refundPolicies')->name('activity.refundPolicies');
        Route::get('/date-info', 'ActivityController@dateInfo')->name('activity.dateInfo');
        Route::get('/comments', 'ActivityController@comments')->name('activity.comments');
    });
});

/**
 * City Weathers
 */

Route::prefix('city-weathers')->group(function () {
    Route::get('/', 'CityWeatherController@index')->name('city-weathers.all');
    Route::get('/{id}/weathers', 'CityWeatherController@weathers')->name('city-weathers.get');
    Route::post('/{id}/set-weathers', 'CityWeatherController@store')->name('city-weathers.store');
});

/**
 * App Settings
 */

Route::prefix('settings')->group(function () {
    Route::get('/', 'Settings\SettingController@get')->name('settings.get');
    Route::post('/store', 'Settings\SettingController@store')->name('settings.store');
});

/**
 * Statistics
 */

Route::prefix('statistics')->group(function () {
    Route::get('/', 'Admin\AdminController@statistics')->name('admin.statistics');
});

/**
 * Orders
 */

Route::prefix('orders')->group(function () {
    Route::get('/', 'OrderController@index')->name('orders.all');
});

/**
 * Rides
 */

Route::prefix('rides')->group(function () {
    Route::get('/', 'RideController@index')->name('rides');
    Route::get('/cheapest-route', 'RideController@cheapestRoute')->name('rides.cheapestRoute');
    Route::get('/airports', 'RideController@airports')->name('rides.airports');
});

/**
 * More entities
 */

Route::get('currencies', 'CurrencyController@index')->name('currencies.index');
Route::get('languages', 'LanguageController@index')->name('languages.index');
