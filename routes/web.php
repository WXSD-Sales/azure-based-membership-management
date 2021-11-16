<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

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

Route::name('home')->get('/', [
    HomeController::class,
    'index'
]);
Route::name('dashboard')->get('/dashboard', [
    DashboardController::class,
    'index'
]);

Route::name('memberships')->get('/memberships', [
    DashboardController::class,
    'getMappings'
]);

Route::name('azure.memberships')->get('/azure/memberships', [
    DashboardController::class,
    'getAzureMemberships'
]);

Route::name('webex.memberships')->get('/webex/memberships', [
    DashboardController::class,
    'getWebexMemberships'
]);

Route::name('azure.groups')->get('/azure/groups', [
    DashboardController::class,
    'getAzureGroups'
]);

Route::name('webex.groups')->get('/webex/groups', [
    DashboardController::class,
    'getWebexGroups'
]);

Route::name('azure.users')->get('/azure/users', [
    DashboardController::class,
    'getAzureUsers'
]);

Route::name('webex.users')->get('/webex/users', [
    DashboardController::class,
    'getWebexUsers'
]);

Route::get('/refreshAzureToken', [
    App\Http\Controllers\JobsController::class,
    'RefreshAzureToken'
]);

Route::get('/refreshWebexToken', [
    App\Http\Controllers\JobsController::class,
    'RefreshWebexToken'
]);

Route::get('/performCrossSync', [
    App\Http\Controllers\JobsController::class,
    'performCrossSync'
]);

Route::get('/retrieveAzureGroups', [
    App\Http\Controllers\JobsController::class,
    'retrieveAzureGroups'
]);

Route::get('/retrieveAzureUsers', [
    App\Http\Controllers\JobsController::class,
    'retrieveAzureUsers'
]);

Route::get('/retrieveWebexGroups', [
    App\Http\Controllers\JobsController::class,
    'retrieveWebexGroups'
]);

Route::get('/retrieveWebexUsers', [
    App\Http\Controllers\JobsController::class,
    'retrieveWebexUsers'
]);

Route::name('login')->get('/login', [
    App\Http\Controllers\Auth\LoginController::class,
    'showLoginForm'
]);
Route::post('/login', [
    App\Http\Controllers\Auth\LoginController::class,
    'login'
]);

Route::name('logout')->post('/logout', [
    App\Http\Controllers\Auth\LoginController::class,
    'logout'
]);
Route::name('reset')->post('/reset', [
    App\Http\Controllers\Auth\LoginController::class,
    'logout'
]);


Route::name('auth.email')->post('/auth/email/redirect', [
    App\Http\Controllers\Auth\RegisterController::class,
    'emailOauthRedirect'
]);

Route::name('auth.azure')->get('/auth/azure/redirect', [
    App\Http\Controllers\Auth\RegisterController::class,
    'azureOauthRedirect'
]);
Route::get('/auth/azure/callback', [
    App\Http\Controllers\Auth\RegisterController::class,
    'azureOauthCallback'
]);

Route::name('auth.webex')->get('/auth/webex/redirect', [
    App\Http\Controllers\Auth\RegisterController::class,
    'webexOauthRedirect'
]);
Route::get('/auth/webex/callback', [
    App\Http\Controllers\Auth\RegisterController::class,
    'webexOauthCallback'
]);


