<?php namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers;
use Illuminate\Http\Request;

use \App\Models\Election;
use \App\Models\Position;
use \App\Models\User;

use stdClass;

/**
 * Main user api controller.
 *
 * @author Jonas Dahl <jonas@jdahl.se>
 * @version 2016-10-14
 */
class UserApiController extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * Returns a json encoded array of elections, see API docs.
	 * 
	 * @return string json encoded array of open elections
	 */
	public function getElections() {
		$elections = [];
		foreach (Election::open() as $election) {
			$x = [];
			$x["id"] = $election->id;
			$x["name"] = $election->name;
			$x["description"] = $election->description;
			$x["opens"] = $election->opens;
			$x["nomination_stop"] = $election->nomination_stop;
			$x["acceptance_stop"] = $election->acceptance_stop;
			$x["closes"] = $election->closes;
			$positions = $election->positions();
			foreach ($positions as $position) {
				$pos = [];
				$pos["identifier"] = $position->identifier;
				$pos["title"] = $position->title;
				$pos["nominees"] = array();
				foreach ($election->nominees($position) as $nominee) {
					$nom = [];
					$nom["name"] = $nominee->name;
					$nom["uuid"] = $nominee->uuid;
					$nom["kth_username"] = $nominee->kth_username;
					$nom["status"] = $nominee->status;
                    $nom["picture"] = \App\Models\User::picture($nominee->kth_username);
					$pos["nominees"][] = $nom;
				}
				$x["positions"][] = $pos;
			}
			$elections[] = $x;
		}
		return response()->json($elections)->header('Access-Control-Allow-Origin', '*');
	}

	/**
	 * Searches for user in sso.
	 * 
	 * @param  Request $request the request that must contain the get parameter term
	 * @return string the sso response
	 */
	public function getSearch(Request $request) {
		$this->validate($request, [
			'term' => 'required'
		]);

		$url = env('SSO_API_URL') . '/api/search?query=' . rawurlencode($request->input('term'));
		$get = file_get_contents($url);
		$obj = json_decode($get);
		$res = [];

		foreach ($obj as $entry) {
			$a = new \stdClass;
			$a->id = $entry->kthid;
			$a->label = $entry->firstName . " " . $entry->familyName . " (" . $entry->kthid . "@kth.se)";
			$a->value = $entry->firstName . " " . $entry->familyName;
			$a->name = $entry->firstName . " " . $entry->familyName;
            $a->picture = \App\Models\User::picture($entry->kthid);
            if (property_exists($entry, 'yearTag')) {
                $a->year = $entry->yearTag;
            }
			$res[] = $a;
		}
		return response()->json($res);
	}
}
