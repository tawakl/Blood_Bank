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

Route::group(['prefix' => 'v1' , 'namespace' => 'Api'],function(){

Route::get('governorates','MainController@governorates');
Route::get('cities','MainController@cities');
Route::post('register','AuthController@register');
Route::post('login','AuthController@login');
Route::post('reset-password', 'AuthController@resetPassword');
Route::post('new-password', 'AuthController@password');




Route::group(['middleware'=> 'auth:api'],function(){

    Route::post('register-token', 'AuthController@registerToken');
    Route::get('posts','MainController@posts');
    Route::get('categories','MainController@categories');
    Route::post('profile','AuthController@profile');
    Route::get('settings','MainController@settings');
    Route::get('donation-requests','MainController@donationRequests');
    Route::get('donation-request','MainController@donationRequest');
    Route::get('notifications', 'MainController@notifications');
    Route::get('notifications-count', 'MainController@notificationsCount');
    Route::post('notifications-settings','AuthController@notificationsSettings');
    Route::get('test-notification', 'MainController@testNotification');






    });

});



