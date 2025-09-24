<?php

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Election;
use App\Models\Position;

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

Route::get('/elections', 'Api\UserApiController@getElections');
Route::get('/user/search', 'Api\UserApiController@getSearch');
