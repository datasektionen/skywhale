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
		return redirect(env('LOGIN_FRONTEND_URL') . '/login?callback=' . url('/login-complete') . '/');
	}

	/**
	* Show a person.
	* @param  int $id the id of the person to show
	* 
	* @return view     the person view
	*/
	public function getLoginComplete($token, Request $request) {
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
            try {
                $hodis = file_get_contents(env('HODIS_API_URL') . '/ugkthid/' . $body->ugkthid);
                $hodis = json_decode($hodis);
            } catch (Exception $e) {
                return redirect('/')->with('error', 'Du loggades inte in.');
            }
			// Create new user in our systems if did not exist
			$user = new User;
			$user->name = $hodis->displayName;
			$user->kth_user_id = strtolower($body->ugkthid);
			$user->kth_username = strtolower($body->user);
			$user->year = $hodis->tag;
			$user->save();
		}

		Auth::login($user);

		// Check if user is admin
        $opts = [
            'http' => [
                'method' => "GET",
                'header' => "Authorization: Bearer " . env('HIVE_API_KEY')
            ]
        ];

        $context = stream_context_create($opts);


		$admin = file_get_contents(env('HIVE_API_URL') . '/user/' . $user->kth_username . '/permission/admin', false, $context);
        $admin = str_replace(["\n"], '', $admin);
		if ($admin === "true") {
			session(['admin' => Auth::user()->id]);
		} else {
			$request->session()->forget('admin');
		}

		return redirect()->intended('/')->with('success', 'Du loggades in.');
	}
}
