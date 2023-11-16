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
 * Main controller. Handles requests that does not fit in under other controllers.
 *
 * @author Jonas Dahl <jonas@jdahl.se>
 * @version 2016-10-14
 */
class NominationController extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * Shows the page for nomination.
	 *
	 * @return view the nomination view
	 */
	public function getNominate() {
		return view('nominate')
			->with('positions', Election::nominateablePositions());
	}

	/**
	 * Lets user answer to nominations. Displays a view with the different nominations.
	 *
	 * @param  Request $request the request sent
	 * @return view             the nominations view
	 */
	public function getNominationAnswer(Request $request) {
		if (Auth::guest()) {
			return redirect()->back()->with('error', 'Du är inte inloggad.');
		}

		// Now get the open elections and their positions that are open for nomination
		$openElections = Election::open();
		$nominations = Position::forUser(Auth::user()->id, $openElections);

		return view('answer-nomination')
			->with('positions', $nominations);
	}

	/**
	 * Receives a post request on a nomination. A nomination request contains
	 * 		- name the name of the person nominated
	 * 		- election the id of the election of nomination
	 * 		- positions array with the ids of the positions of nomination
	 *
	 * @param  Request $request the request object
	 * @return redirection           back if unsuccessful with error message, to '/' if successful
	 */
	public function postNominate(Request $request) {
		$this->validate($request, [
			'name' => 'required',
			'email' => 'required|email|kth_email|not_blacklisted',
			'election_position' => 'required'
		]);

		// Try to get user form kth_username
		$kth_username = strtolower(explode("@", $request->input('email'))[0]);
		$user = User::where('kth_username', '=', $kth_username)->first();

		// If no success, create one in the database
		if ($user === null) {
			$hodis = file_get_contents(env('HODIS_API_URL') . '/uid/' . $kth_username);
			if ($hodis === FALSE) {
				return redirect('/')->with('error', $kth_username . ' kunde inte hittas.');
			}
			try {
				$body = json_decode($hodis);
			} catch (Exception $e) {
				return redirect('/')->with('error', $kth_username . ' kunde inte hittas.');
			}
			$user = new User;
			$user->name = $body->displayName;
			$user->kth_user_id = strtolower($body->ugKthid);
			$user->kth_username = strtolower($body->uid);
			$user->year = $body->tag;
			$user->save();
		}

		// And finally nominate to the post
		if (!$user->nominate($request->input('election_position'))) {
			return redirect('/')->with('error', $user->name . ' nominerades inte.');
		}

		// Redirect to main page with success message
		return redirect('/')->with('success', $user->name . ' nominerades.');
	}

	/**
	 * Accepts nomination for user.
	 *
	 * @param  string $uuid the uuid of the nomination to accept
	 * @return redirect     to last page with success message
	 */
	public function getAccept($uuid) {
		// Get election and see if acceptance stop has been
		$row = \DB::table('position_user')
			->where('uuid', $uuid)
			->first();

		// Throw out user if not authenticated
		if (Auth::guest() || Auth::user()->id != $row->user_id) {
			return redirect()->back()->with('error', 'Du har inte tillåtelse att visa den här sidan.');
		}

		// Only accept answer if open
		if ($row === null || !Election::find($row->election_id)->acceptsAnswers($uuid)) {
			return redirect()->back()->with('error', 'Du kan inte längre svara på denna nominering.');
		}

		Auth::user()->accept($uuid);

		return redirect()->back()->with('success', 'Du tackade ja!');
	}

	/**
	 * Declines nomination for user.
	 *
	 * @param  string $uuid the uuid of the nomination to decline
	 * @return redirect     to last page with success message
	 */
	public function getDecline($uuid) {
		// Get election and see if acceptance stop has been
		$row = \DB::table('position_user')
			->where('uuid', $uuid)
			->first();

		// Throw out user if not authenticated
		if (Auth::guest() || Auth::user()->id != $row->user_id) {
			return redirect()->back()->with('error', 'Du har inte tillåtelse att visa den här sidan.');
		}

		// Only accept answer if open
		if ($row === null || !Election::find($row->election_id)->acceptsAnswers($uuid)) {
			return redirect()->back()->with('error', 'Du kan inte längre svara på denna nominering.');
		}

		Auth::user()->decline($uuid);

		return redirect()->back()->with('success', 'Du tackade nej!');
	}

	/**
	 * Set nomination to waiting for user.
	 *
	 * @param  string $uuid the uuid of the nomination to regret
	 * @return redirect     to last page with success message
	 */
	public function getRegret($uuid) {
		// Get election and see if acceptance stop has been
		$row = \DB::table('position_user')
			->where('uuid', $uuid)
			->first();

		// Throw out user if not authenticated
		if (Auth::guest() || Auth::user()->id != $row->user_id) {
			return redirect()->back()->with('error', 'Du har inte tillåtelse att visa den här sidan.');
		}

		// Only accept answer if open
		if ($row === null) {
			return redirect()->back()->with('error', 'Du kan inte längre svara på denna nominering.');
		}

		Auth::user()->regret($uuid);

		return redirect()->back()->with('success', 'Du ångrade dig!');
	}
}
