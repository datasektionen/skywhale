<?php

use \App\Models\Election;
use \App\Models\Position;
use \App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

/**
 * Main routes.
 */
Route::get ('/', 'Controller@getWelcome');
Route::get('person/{id}', 'Controller@getPerson');

Route::get ('nominate', 'NominationController@getNominate');
Route::get ('nomination/answer', 'NominationController@getNominationAnswer')		->middleware('auth');
Route::get ('nomination/answer/accept/{uuid}', 'NominationController@getAccept')	->middleware('auth');
Route::get ('nomination/answer/decline/{uuid}', 'NominationController@getDecline')	->middleware('auth');
Route::get ('nomination/answer/regret/{uuid}', 'NominationController@getRegret')	->middleware('auth');
Route::post('nominate', 'NominationController@postNominate');


/**
 * Admin routes.
 */
Route::get('admin', 'Admin\AdminController@getIndex')								->middleware('admin');

Route::get ('admin/positions', 'Admin\PositionAdminController@getShow')				->middleware('admin');

Route::get ('admin/elections', 'Admin\ElectionAdminController@getShow')				->middleware('admin');
Route::get ('admin/elections/new', 'Admin\ElectionAdminController@getNew')			->middleware('admin');
Route::post('admin/elections/new', 'Admin\ElectionAdminController@postNew')			->middleware('admin');
Route::get ('admin/elections/edit/{id}', 'Admin\ElectionAdminController@getEdit')	->middleware('admin');
Route::post('admin/elections/edit/{id}', 'Admin\ElectionAdminController@postEdit')	->middleware('admin');
Route::get ('admin/elections/remove-nomination/{uuid}', 'Admin\ElectionAdminController@getRemoveNomination')->middleware('admin');
Route::get ('admin/elections/remove-nomination-sure/{uuid}', 'Admin\ElectionAdminController@getRemoveNominationSure')->middleware('admin');
Route::get ('admin/elections/edit-nomination/{uuid}', 'Admin\ElectionAdminController@getEditNomination')->middleware('admin');
Route::post('admin/elections/edit-nomination/{uuid}', 'Admin\ElectionAdminController@postEditNomination')->middleware('admin');

Route::get ('admin/persons', 'Admin\PersonAdminController@getShow')					->middleware('admin');
Route::post('admin/persons/merge', 'Admin\PersonAdminController@postMerge')			->middleware('admin');
Route::get ('admin/persons/merge', 'Admin\PersonAdminController@getMerge')			->middleware('admin');
Route::post('admin/persons/merge-final', 'Admin\PersonAdminController@postMergeFinal')->middleware('admin');
Route::get ('admin/persons/new', 'Admin\PersonAdminController@getNew')				->middleware('admin');
Route::post('admin/persons/new', 'Admin\PersonAdminController@postNew')				->middleware('admin');
Route::get ('admin/persons/edit/{id}', 'Admin\PersonAdminController@getEdit')		->middleware('admin');
Route::post('admin/persons/edit/{id}', 'Admin\PersonAdminController@postEdit')		->middleware('admin');
Route::get('admin/persons/remove/{id}', 'Admin\PersonAdminController@getRemove')    ->middleware('admin');
Route::get('admin/persons/remove-confirmed/{id}', 'Admin\PersonAdminController@getRemoveConfirmed')->middleware('admin');

Route::get ('logout', 'AuthController@getLogout')									->middleware('auth');
Route::get ('login', 'AuthController@getLogin')										->middleware('guest');
Route::get ('login-complete/{token}', 'AuthController@getLoginComplete');