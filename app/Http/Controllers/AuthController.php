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
        $this->oidc->addScope(['profile', 'email', 'year_tag', 'permissions']);
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
        $given_name = $this->oidc->getVerifiedClaims('given_name');
        $family_name = $this->oidc->getVerifiedClaims('family_name');
        $name = $given_name . " " . $family_name;
        $email = $this->oidc->getVerifiedClaims('email');
        $year_tag = $this->oidc->getVerifiedClaims('year_tag');
        $permissions = $this->oidc->getVerifiedClaims('permissions');

		$user = User::where('kth_username', $kthid)->first();

		if ($user === null) {
			// Create new user in our systems if did not exist
			$user = new User;
			$user->name = $name;
			$user->kth_username = strtolower($kthid);
            $user->email = $email;
			$user->year = $year_tag;
            $user->save();
        } else if ($user->name != $name || $user->email != $email || $user->year != $year_tag) {
            // Update user to match info from sso
            $user->name = $name;
            $user->email = $email;
			$user->year = $year_tag;
            $user->save();
        }

		Auth::login($user);

		// Check if user is admin
        $request->session()->forget('admin');
        foreach ($permissions as $permission) {
            if ($permission->id === "admin") {
                session(['admin' => Auth::user()->id]);
            }
        }

		return redirect()->intended('/')->with('success', 'Du loggades in.');
	}
}
