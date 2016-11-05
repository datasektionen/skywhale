<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Auth;

use App\Models\Election;
use App\Models\Position;
use App\Models\User;

/**
 * Handles user, for example settings.
 *
 * @author Jonas Dahl <jonas@jdahl.se>
 * @version 2016-11-05
 */
class UserController extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * Shows the page with user settings.
	 * 
	 * @return view the user settings view
	 */
	public function getSettings() {
		return view('user.settings')
			->with('user', Auth::user());
	}

	/**
	 * Unsubscribes user with token.
	 * 
	 * @return redirect to settings page with success message
	 */
	public function getUnsubscribe() {
		$user = Auth::user();
		$user->wants_email = 'no';
		$user->save();

		return redirect('/user/settings')
			->with('success', 'Du kommer inte längre få några e-postmeddelanden när du nomineras.');
	}

	/**
	 * Shows the page with user settings.
	 * 
	 * @return view the user settings view
	 */
	public function postSettings(Request $request) {
		$this->validate($request, [
			'wants_email' => 'required|in:yes,no'
		]);

		$user = Auth::user();
		$user->wants_email = $request->input('wants_email') === 'yes' ? 'yes' : 'no';
		$user->save();

		return redirect('/user/settings')
			->with('success', 'Inställningarna sparades!');
	}
}