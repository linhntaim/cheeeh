<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'namespace' => 'Api',
], function () {
    #region Common
    Route::get('prerequisite', 'PrerequisiteController@index');
    Route::post('auth/login', 'LoginController@issueToken');
    Route::post('auth/password', 'PasswordController@store');
    Route::get('auth/password', 'PasswordController@show');
    Route::post('auth/register', 'RegisterController@store');
    Route::post('auth/verify-email', 'VerifyEmailController@store');
    Route::post('device/current', 'DeviceController@currentStore');
    #endregion

    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::post('auth/logout', 'LogoutController@logout');

        Route::group([
            'prefix' => 'account',
            'namespace' => 'Account',
        ], function () {
            Route::post('/', 'AccountController@store');
            Route::get('/', 'AccountController@show');
            Route::put('/', 'AccountController@update');

            Route::post('device/current', 'DeviceController@currentStore');

            Route::put('main-email', 'AccountController@mainEmailUpdate');

            Route::group([
                'prefix' => 'email',
            ], function () {
                Route::get('/', 'EmailController@index');
                Route::post('/', 'EmailController@store');
                Route::delete('/', 'EmailController@bulkDestroy');
                Route::get('{id}', 'EmailController@show');
                Route::put('{id}', 'EmailController@update');
            });
        });
    });
});
