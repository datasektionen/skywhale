<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers;
use Illuminate\Http\Request;

use \App\Models\Election;
use \App\Models\Position;
use \App\Models\User;
use \App\Models\Blacklist;

/**
 * Handles administrator actions concerning elections.
 *
 * @author  Jonas Dahl <jonas@jdahl.se>
 * @version 2016-10-14
 */
class WhitelistAdminController extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * Shows all blacklisted emails as a list.
	 * 
	 * @return view containing a list over blacklist entries
	 */
	public function getShow() {
		return view('admin.whitelist.index')
			->with('whitelist', User::notDecidedEmail());
	}

	/**
	 * Shows all blacklisted emails as a list.
	 * 
	 * @return view containing a list over blacklist entries
	 */
	public function postShow(Request $request) {
		$this->validate($request, [
			'responses' => 'required|array|minCount:1'
		]);

		foreach ($request->input('responses') as $userId => $response) {
			if ($response == 'accept') {
				$user = User::find(intval($userId));
				if ($user === null) {
					continue;
				}
				$user->wants_email = 'yes';
				$user->save();
				$user->notify();
			} else if ($response == 'blacklist') {
				$user = User::find(intval($userId));
				if ($user === null) {
					continue;
				}
				if (Blacklist::isBlacklisted($user->kth_username)) {
					$user->delete();
					continue;
				}
				$blacklist = new Blacklist;
				$blacklist->kth_username = $user->kth_username;
				$blacklist->save();
				$user->delete();
			}
		}

		return redirect('admin/whitelist')->with('success', 'Personerna uppdaterades!');
	}
}
