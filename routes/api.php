<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => ['api'], 'prefix' => 'account'], function ($router) {

    // Account Routes
    Route::get('index', 'AccountController@index');
    Route::post('filter/index', 'AccountController@filterIndex');
    Route::post('search/index', 'AccountController@searchIndex');
    Route::post('store', 'AccountController@store');
    Route::get('show', 'AccountController@show');
    Route::get('link-wa-qr-code', 'AccountController@linkWhatsAppQRCode');
    Route::get('poll-wa-qr-code', 'AccountController@pollWhatsAppQRCode');
    Route::get('fetch-wa-groups', 'AccountController@fetchWhatsAppGroups');
    Route::get('me', 'AccountController@me');
    Route::post('update', 'AccountController@update');
    Route::post('delete', 'AccountController@destroy');
    Route::get('test', 'AccountController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'auth'], function ($router) {

    // Auth Routes
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('forgot', 'AuthController@forgotPassword');
    Route::post('reset', 'AuthController@resetPassword');
    Route::post('change', 'AuthController@changePassword');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::get('verify', 'AuthController@verifyEmail');
    Route::get('test', 'AuthController@test');

    // Socialite Routes
    Route::get('socialite/facebook', 'SocialiteController@redirectToFacebookProvider');
    Route::get('socialite/facebook/callback', 'SocialiteController@handleFacebookProviderCallback');
    Route::get('socialite/linkedin', 'SocialiteController@redirectToLinkedinProvider');
    Route::get('socialite/linkedin/callback', 'SocialiteController@handleLinkedinProviderCallback');
    Route::get('socialite/google', 'SocialiteController@redirectToGoogleProvider');
    Route::get('socialite/google/callback', 'SocialiteController@handleGoogleProviderCallback');
    Route::get('socialite/apple', 'SocialiteController@redirectToAppleProvider');
    Route::get('socialite/apple/callback', 'SocialiteController@handleAppleProviderCallback');
    Route::get('socialite/test', 'SocialiteController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'broadcast'], function ($router) {

    // Broadcast Routes
    Route::get('index', 'BroadcastController@index');
    Route::post('filter/index', 'BroadcastController@filterIndex');
    Route::post('search/index', 'BroadcastController@searchIndex');
    Route::post('store', 'BroadcastController@store');
    Route::get('show', 'BroadcastController@show');
    Route::get('me', 'BroadcastController@me');
    Route::post('update', 'BroadcastController@update');
    Route::get('placeholder/index', 'BroadcastController@placeHolderIndex');
    Route::post('placeholder/update', 'BroadcastController@placeHoldersUpdate');
    Route::post('delete', 'BroadcastController@destroy');
    Route::get('test', 'BroadcastController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'broadcast-template'], function ($router) {

    // Broadcast Template Routes
    Route::get('index', 'BroadcastTemplateController@index');
    Route::post('filter/index', 'BroadcastTemplateController@filterIndex');
    Route::post('search/index', 'BroadcastTemplateController@searchIndex');
    Route::post('store', 'BroadcastTemplateController@store');
    Route::get('show', 'BroadcastTemplateController@show');
    Route::get('me', 'BroadcastTemplateController@me');
    Route::post('update', 'BroadcastTemplateController@update');
    Route::post('delete', 'BroadcastTemplateController@destroy');
    Route::get('test', 'BroadcastTemplateController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'cache'], function ($router) {

    // Cache Routes
    Route::get('index', 'CacheController@index');
    Route::post('clear', 'CacheController@clear');
    Route::get('test', 'CacheController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'contact'], function ($router) {

    // Contact Routes
    Route::get('index', 'ContactController@index');
    Route::post('filter/index', 'ContactController@filterIndex');
    Route::post('search/index', 'ContactController@searchIndex');
    Route::post('store', 'ContactController@store');
    Route::get('show', 'ContactController@show');
    Route::get('me', 'ContactController@me');
    Route::post('update', 'ContactController@update');
    Route::post('delete', 'ContactController@destroy');
    Route::get('test', 'ContactController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'misc'], function ($router) {

    // Miscellaneous Routes
    Route::post('response', 'MiscellaneousController@responseTypes');
    Route::post('patcher', 'MiscellaneousController@applicationPatcher');
});

Route::group(['middleware' => ['api'], 'prefix' => 'permission'], function ($router) {

    // Permission Routes
    Route::get('index', 'PermissionController@index');
    Route::post('store', 'PermissionController@store');
    Route::get('show', 'PermissionController@show');
    Route::post('update', 'PermissionController@update');
    Route::post('assign', 'PermissionController@assign');
    Route::post('retract', 'PermissionController@retract');
    Route::post('delete', 'PermissionController@destroy');
    Route::get('test', 'PermissionController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'role'], function ($router) {

    // Role Routes
    Route::get('index', 'RoleController@index');
    Route::post('store', 'RoleController@store');
    Route::get('show', 'RoleController@show');
    Route::post('update', 'RoleController@update');
    Route::post('assign', 'RoleController@assign');
    Route::post('retract', 'RoleController@retract');
    Route::post('delete', 'RoleController@destroy');
    Route::get('test', 'RoleController@test');
});
