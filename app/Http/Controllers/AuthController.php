<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Auth;
use Session;

use Jumbojett\OpenIDConnectClient;

use App\Models\Election;
use App\Models\Position;
use App\Models\User;

/**
* Authentication controller. Handles login via sso.datasektionen.se.
*
* @author Jonas Dahl <jonas@jdahl.se>, Viktor Ekby <viktoe@datasektionen.se>
* @version 2025-10-12
*/
class AuthController extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private OpenIDConnectClient $oidc;

    function __construct() {
        $this->oidc = new OpenIDConnectClient(
            env('OIDC_PROVIDER'),
            env('OIDC_ID'),
            env('OIDC_SECRET')
        );
        $this->oidc->setRedirectURL(env('REDIRECT_URL'));
    }

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
	* The login page. Just redirects to sso.
	* 
	* @return redirect to sso.datasektionen.se
	*/
	public function getLogin(Request $request) {
        $this->oidc->authenticate();
    }

	/**
	* Show a person.
	* 
	* @return view the person view
	*/
	public function getLoginComplete(Request $request) {
        if ($this->oidc->authenticate() === FALSE) {
			return redirect('/')->with('error', 'Du loggades inte in.');
		}

        $kthid = $this->oidc->getVerifiedClaims('sub');

		$user = User::where('kth_username', $kthid)->first();

		if ($user === null) {
            try {
                $ssoUser = file_get_contents(env('SSO_API_URL') . '/api/users?format=single&u=' . $kthid);
                $ssoUser = json_decode($ssoUser);
                if (!property_exists($ssoUser, 'yearTag')) {
                    $ssoUser->yearTag = "";
                }
            } catch (Exception $e) {
                return redirect('/')->with('error', 'Du loggades inte in.');
            }
			// Create new user in our systems if did not exist
			$user = new User;
			$user->name = $ssoUser->firstName . " " . $ssoUser->familyName;
			$user->kth_username = strtolower($kthid);
			$user->year = $ssoUser->yearTag;
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
