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
use App\Models\Event;

/**
 * Main controller. Handles requests that does not fit in under other controllers.
 *
 * @author Jonas Dahl <jonas@jdahl.se>
 * @version 2016-10-14
 */
class Controller extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * The welcome page. Just shows a view.
	 * 
	 * @return view the welcome view
	 */
	public function getWelcome() {
		return view('welcome')
			->with('elections', Election::open());
	}

	/**
	 * Show a person.
	 * @param  int $id the id of the person to show
	 * 
	 * @return view     the person view
	 */
	public function getPerson($id) {
		$user = User::find($id);
		if ($user === null) {
			abort(400);
		}

		return view('show-person')->with('user', $user);
	}

	public function getRss() {
		return response(view('rss')
			->with('positions', Position::allKey())
			->with('events', Event::orderBy('id', 'DESC')->limit(100)->get()))
			->header('Content-Type', 'application/rss+xml; charset=utf-8');
	}
}