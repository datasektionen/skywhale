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

/**
 * Handles administrator actions concerning elections.
 *
 * @author  Jonas Dahl <jonas@jdahl.se>
 * @version 2016-10-14
 */
class RemindersAdminController extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * Shows all elections as a list.
	 * 
	 * @return view containing a list over elections
	 */
	public function getShow() {
		return view('admin.reminders.index')
			->with('nominees', User::notAnsweredAggregated());
	}

	/**
	 * Shows all elections as a list.
	 * 
	 * @return view containing a list over elections
	 */
	public function postShow(Request $request) {
		$this->validate($request, [
			'users' => 'required|array|minCount:1'
		]);

		$good = [];
		$bad = [];
		foreach ($request->input('users') as $userId) {
			$user = User::find(intval($userId));
			if ($user === null) {
				continue;
			}

			if ($user->remind()) {
				$good[] = $user;
			} else {
				$bad[] = $user;
			}
		}

		$message = "";
		$g = false;
		if (count($good) > 0) {
			if (count($good) < 10) {
				$message .= "<p>Skickade mejl till: <ul>";
				foreach ($good as $p) {
					$message .= "<li>" . $p->name . "</li>";
				}
				$message .= "</ul></p>";
			} else {
				$message .= "<p>Skickade mejl till " . count($good) . " personer.</p>";
			}
			$g = true;
		}
		if (count($bad) > 0) {
			if (count($bad) < 10) {
				$message .= "<p>Misslyckades med att skicka mejl till: <ul>";
				foreach ($bad as $p) {
					$message .= "<li>" . $p->name . "</li>";
				}
				$message .= "</ul></p>";
			} else {
				$message .= "<p>Misslyckades med att skicka mejl till " . count($bad) . " personer.</p>";
			}
			$message .= "<p>Detta kan bero på att den/de redan blivit påminda inom de senaste 24 timmarna.</p>";
		}
		return redirect('admin/reminders')
			->with($g ? 'success' : 'error', $message);
	}
}
