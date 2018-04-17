<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Auth;
use Session;

use App\Models\Election;
use App\Models\Position;
use App\Models\User;

/**
* Authentication controller. Handles login via login2.datasektionen.se.
*
* @author Jonas Dahl <jonas@jdahl.se>
* @version 2016-10-14
*/
class AuthController extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	* The logout url. Redirects to main page with success message.
	* 
	* @return view the welcome view
	*/
	public function getLogout() {
		Auth::logout();
		\Session::forget('admin');
		return redirect('/')->with('success', 'Du är nu utloggad från valsystemet.');
	}

	/**
	* The login page. Just redirects to login2.
	* 
	* @return redirect to login2.datasektionen.se
	*/
	public function getLogin(Request $request) {
		return redirect(env('LOGIN_API_URL') . '/login?callback=' . url('/login-complete') . '/');
	}

	/**
	* Show a person.
	* @param  int $id the id of the person to show
	* 
	* @return view     the person view
	*/
	public function getLoginComplete($token) {
		// Send get request to login server
		$client = new Client();
		$res = file_get_contents(env('LOGIN_API_URL') . '/verify/' . $token . '.json?api_key=' . env('LOGIN_API_KEY'));
		if ($res === FALSE) {
			return redirect('/')->with('error', 'Du loggades inte in.');
		}

		// We now have a response. If it is good, parse the json and login user
		try {
			$body = json_decode($res);	
		} catch (Exception $e) {
			return redirect('/')->with('error', 'Du loggades inte in.');
		}

		$user = User::where('kth_username', $body->user)->first();

		if ($user === null) {
			// Create new user in our systems if did not exist
			$user = new User;
			$user->name = $body->first_name . " " . $body->last_name;
			$user->kth_username = $body->user;
			$user->kth_user_id = $body->ugkthid;
			$user->year = "";
			$user->save();
		}

		Auth::login($user);

		// Check if user is admin
		$admin = file_get_contents(env('PLS_API_URL') . '/user/' . $user->kth_username . '/skywhale/admin');
		if ($admin === "true") {
			Session::set('admin', Auth::user()->id);
		} else {
			Session::forget('admin');
		}

		return redirect()->intended('/')->with('success', 'Du loggades in.');
	}
}