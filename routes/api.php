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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    // Routes within this version group will require authentication.
    $api->post('login', 'App\Http\Controllers\API\AuthController@login');
});

$api->version('v1', ['middleware' => 'api.auth'], function ($api) {
    // Routes within this version group will require authentication.
    $api->post('me', 'App\Http\Controllers\API\AuthController@me');
});