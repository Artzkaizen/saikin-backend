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

Route::group(['middleware' => ['api'], 'prefix' => 'cache'], function ($router) {

    // Cache Routes
    Route::get('index', 'CacheController@index');
    Route::post('clear', 'CacheController@clear');
    Route::get('test', 'CacheController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'misc'], function ($router) {

    // Miscellaneous Routes
    Route::post('response', 'MiscellaneousController@responseTypes');
    Route::post('patcher', 'MiscellaneousController@applicationPatcher');
});
