<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BlacklistAdminController;
use App\Http\Controllers\Admin\ElectionAdminController;
use App\Http\Controllers\Admin\PersonAdminController;
use App\Http\Controllers\Admin\PositionAdminController;
use App\Http\Controllers\Admin\RemindersAdminController;
use App\Http\Controllers\Admin\WhitelistAdminController;
use App\Http\Middleware\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NominationController;
use App\Http\Controllers\UserController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

/**
 * Main routes.
 */
Route::get('/', [Controller::class, 'getWelcome']);
Route::get('/rss', [Controller::class, 'getRss']);
Route::get('person/{id}', [Controller::class, 'getPerson']);

Route::get('nominate', [NominationController::class, 'getNominate']);
Route::get('nomination/answer', [NominationController::class, 'getNominationAnswer'])->middleware(Authenticate::class);
Route::get('nomination/answer/accept/{uuid}', [NominationController::class, 'getAccept'])->middleware(Authenticate::class);
Route::get('nomination/answer/decline/{uuid}', [NominationController::class, 'getDecline'])->middleware(Authenticate::class);
Route::get('nomination/answer/regret/{uuid}', [NominationController::class, 'getRegret'])->middleware(Authenticate::class);
Route::post('nominate', [NominationController::class, 'postNominate']);

Route::get('user/settings', [UserController::class, 'getSettings'])->middleware(Authenticate::class);
Route::post('user/settings', [UserController::class, 'postSettings'])->middleware(Authenticate::class);
Route::get('user/unsubscribe', [UserController::class, 'getUnsubscribe'])->middleware(Authenticate::class);



/**
 * Admin routes.
 */
Route::get('admin', [AdminController::class, 'getIndex'])->middleware(Admin::class);

Route::get('admin/positions', [PositionAdminController::class, 'getShow'])->middleware(Admin::class);

Route::get('admin/elections', [ElectionAdminController::class, 'getShow'])->middleware(Admin::class);
Route::get('admin/elections/new', [ElectionAdminController::class, 'getNew'])->middleware(Admin::class);
Route::post('admin/elections/new', [ElectionAdminController::class, 'postNew'])->middleware(Admin::class);
Route::get('admin/elections/edit/{id}', [ElectionAdminController::class, 'getEdit'])->middleware(Admin::class);
Route::post('admin/elections/edit/{id}', [ElectionAdminController::class, 'postEdit'])->middleware(Admin::class);
Route::get('admin/elections/edit/{id}/positions', [ElectionAdminController::class, 'getEditPositions'])->middleware(Admin::class);
Route::post('admin/elections/edit/{id}/positions', [ElectionAdminController::class, 'postEditPositions'])->middleware(Admin::class);
Route::get('admin/elections/remove-nomination/{uuid}', [ElectionAdminController::class, 'getRemoveNomination'])->middleware(Admin::class);
Route::get('admin/elections/remove-nomination-sure/{uuid}', [ElectionAdminController::class, 'getRemoveNominationSure'])->middleware(Admin::class);
Route::get('admin/elections/remove/{id}', [ElectionAdminController::class, 'getRemove'])->middleware(Admin::class);
Route::get('admin/elections/remove-confirmed/{id}', [ElectionAdminController::class, 'getRemoveConfirmed'])->middleware(Admin::class);
Route::get('admin/elections/edit-nomination/{uuid}', [ElectionAdminController::class, 'getEditNomination'])->middleware(Admin::class);
Route::post('admin/elections/edit-nomination/{uuid}', [ElectionAdminController::class, 'postEditNomination'])->middleware(Admin::class);

Route::get('admin/persons', [PersonAdminController::class, 'getShow'])->middleware(Admin::class);
Route::post('admin/persons/merge', [PersonAdminController::class, 'postMerge'])->middleware(Admin::class);
Route::get('admin/persons/merge', [PersonAdminController::class, 'getMerge'])->middleware(Admin::class);
Route::post('admin/persons/merge-final', [PersonAdminController::class, 'postMergeFinal'])->middleware(Admin::class);
Route::get('admin/persons/new', [PersonAdminController::class, 'getNew'])->middleware(Admin::class);
Route::post('admin/persons/new', [PersonAdminController::class, 'postNew'])->middleware(Admin::class);
Route::get('admin/persons/edit/{id}', [PersonAdminController::class, 'getEdit'])->middleware(Admin::class);
Route::post('admin/persons/edit/{id}', [PersonAdminController::class, 'postEdit'])->middleware(Admin::class);
Route::get('admin/persons/remove/{id}', [PersonAdminController::class, 'getRemove'])->middleware(Admin::class);
Route::get('admin/persons/remove-confirmed/{id}', [PersonAdminController::class, 'getRemoveConfirmed'])->middleware(Admin::class);

Route::get('admin/blacklist', [BlacklistAdminController::class, 'getShow'])->middleware(Admin::class);
Route::post('admin/blacklist', [BlacklistAdminController::class, 'postRemove'])->middleware(Admin::class);
Route::get('admin/blacklist/new', [BlacklistAdminController::class, 'getNew'])->middleware(Admin::class);
Route::post('admin/blacklist/new', [BlacklistAdminController::class, 'postNew'])->middleware(Admin::class);

Route::get('admin/whitelist', [WhitelistAdminController::class, 'getShow'])->middleware(Admin::class);
Route::post('admin/whitelist', [WhitelistAdminController::class, 'postShow'])->middleware(Admin::class);

Route::get('admin/reminders', [RemindersAdminController::class, 'getShow'])->middleware(Admin::class);
Route::post('admin/reminders', [RemindersAdminController::class, 'postShow'])->middleware(Admin::class);

Route::get('logout', [AuthController::class, 'getLogout'])->middleware(Authenticate::class);
Route::get('login', [AuthController::class, 'getLogin'])->middleware('guest');
Route::get('login-complete', [AuthController::class, 'getLoginComplete']);

Route::get('js/cors/{file}', function($file) {
	return response(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/js/' . $file))
		->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', '*');
});
