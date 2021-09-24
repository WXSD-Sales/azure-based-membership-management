<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

# Azure OAuth
Route::get('/auth/azure/redirect', function () {
    return Socialite::driver('azure')
        ->scopes(['User.Read', 'User.Read.All', 'Group.Read.All'])
        ->redirect();
});
Route::get('/auth/azure/callback', function () {
    $user = Socialite::driver('azure')->user();
    Log::info($user->getEmail());
});

# Webex OAuth
Route::get('/auth/webex/redirect', function () {
    return Socialite::driver('webex')
        ->redirect();
});
Route::get('/auth/webex/callback', function () {
    $user = Socialite::driver('webex')->user();
    Log::info($user->getEmail());
    Log::info($user->getNickname());
});
