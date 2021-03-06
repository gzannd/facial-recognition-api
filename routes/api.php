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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Person
Route::get('person', 'App\Http\Controllers\SecurityController@index');
Route::get('person/{person}', 'App\Http\Controllers\SecurityController@show');
Route::post('person', 'App\Http\Controllers\SecurityController@store');
Route::put('person/{person}', 'App\Http\Controllers\SecurityController@update');
Route::delete('person/{person}', 'App\Http\Controllers\SecurityController@delete');

//Configuration
Route::get('configuration', 'App\Http\Controllers\ConfigurationController@index');
Route::put('configuration/{config}', 'App\Http\Controllers\ConfigurationController@update');
