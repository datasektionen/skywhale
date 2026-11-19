<?php namespace App\Http\Controllers;

use DateTimeZone;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\Election;
use App\Models\Position;
use App\Models\User;
use App\Models\Event;
use Illuminate\View\View;

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
	public function getWelcome(): View {
        $now = \Carbon\Carbon::now(new DateTimeZone('Europe/Stockholm'));
        $elections = Election::where('opens', '<', $now)->where('closes', '>', $now)->get();

        // Load profile pictures
        $nominees = [];
        foreach ($elections as $election) {
            $positions = $election->positions();
            foreach ($positions as $position) {
                foreach($election->nominees($position) as $nominee) {
                    if (!apcu_exists($nominee->kth_username)) {
                        $nominees[] = $nominee->kth_username;
                    }
                }
            }
        }

        if (count($nominees) !== 0) {
            $opts = [
                'http' => [
                    'method' => "POST",
                    'header' => array(
                        'Authorization: Bearer ' . env('RFINGER_API_KEY'),
                        'Content-type: application/json',
                    ),
                    'content' => json_encode($nominees)
                ]
            ];
            $context = stream_context_create($opts);
            $response = file_get_contents(env('RFINGER_API_URL') . '/batch', false, $context);
            foreach (json_decode($response) as $user => $link) {
                apcu_store($user, $link, 3600);
            }
        }

		return view('welcome')
			->with('elections', Election::open());
	}

	/**
	 * Show a person.
	 * @param  int $id the id of the person to show
	 *
	 * @return view     the person view
	 */
	public function getPerson($username) {
		$user = User::where('kth_username', $username)->first();
		if ($user == null) {
			$username = User::findOrFail($username)->kth_username;
			return redirect('/person/' . $username);
		}

		try {
			$roles = @file_get_contents('https://dfunkt.datasektionen.se/api/user/kthid/' . $user->kth_username);
			if ($roles === FALSE) {
				$mandates = collect([]);
			} else {
				$mandates = json_decode($roles)->mandates;
			}
		} catch (Exception $e) {
			$mandates = collect([]);
		}

        if (is_array($mandates)) {
            usort($mandates, function ($a, $b) {
                return strcmp($b->start, $a->start);
            });
        }

		return view('show-person')->with('user', $user)->with('mandates', $mandates);
	}

	public function getRss() {
		return response(view('rss')
			->with('positions', Position::allKey())
			->with('events', Event::orderBy('id', 'DESC')->limit(100)->get()))
			->header('Content-Type', 'application/rss+xml; charset=utf-8')
			->header('X-Robots-Tag: noindex, nofollow', true);
	}
}
