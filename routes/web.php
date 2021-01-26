<?php

use Illuminate\Support\Facades\Route;
        /**
         * App routes
         */

        Route::get('/', function () {
            return redirect(\App\AppConfig::getLocale());
        })->name('app.root');
        
        Route::group([
            'prefix' => '{locale}',
            'where' => [
                'locale' => '[a-zA-Z]{2}',
            ],
            'middleware' => ['locale'],
        ], function () {
                /**
                 * App routes
                 */
                Route::get('/', 'AppController@home')->name('app.home');
                Route::get('/explore', 'AppController@explore')->name('app.explore');
                Route::get('/place/{id}', 'AppController@place')->name('app.place');
        
                Route::get('/booking', 'AppController@booking')->name('app.booking');
        });
/**
 * Autentication
 */
Route::get('auth', 'Auth\LoginController@auth')->name('auth.view');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::post('login', 'Auth\LoginController@login')->name('login.post');

Route::middleware('pass-code')->group(function () {
    Route::get('email-verification/{id}', 'Auth\VerificationController@verify')
        ->name('verification.verify');
    Route::get('email-verified', 'Auth\VerificationController@verified')
        ->name('verification.verified');
    Route::get('not-activated', 'Auth\VerificationController@notActivatedAccount')
        ->name('verification.not-activated');
    Route::get('email-confirmation-sent', 'Auth\VerificationController@emailConfirmationSent')
        ->name('verification.email-confirmation-sent');

    Route::post('reset-password-email', 'Auth\ResetPasswordController@sendResetPasswordNotification')
        ->name('password.reset.index');
    Route::post('reset-password', 'Auth\ResetPasswordController@reset')
        ->name('password.reset.token');
});
Route::post('register', 'Auth\RegisterController@register')->name('register.post');

/**
 * Admin
 */
Route::prefix('admin')->middleware(['admin', 'activated'])->group(function () {
    Route::get('/', 'Admin\AdminController@dashboard')->name('admin.index');

    Route::get('settings', 'Settings\SettingController@get')->name('settings.get');

    Route::prefix('entity')->group(function () {
        Route::get('{id}/state', 'EntityStateController@state')->name('admin.entity.state');
        Route::get('states', 'EntityStateController@states')->name('admin.entity.states');
        Route::post('states', 'EntityStateController@setState')->name('admin.entity.set.state');
    });

    Route::prefix('locales')->group(function () {
        Route::get('/', 'LocaleController@index')->name('locales.index');
        Route::post('/store', 'LocaleController@store')->name('locales.store');
    });

    Route::post('settings/store', 'Settings\SettingController@store')->name('settings.store');
});
