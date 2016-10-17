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
			$elections[$election->id]["opens"] = $election->opens;
			$elections[$election->id]["nomination_stop"] = $election->nomination_stop;
			$elections[$election->id]["acceptance_stop"] = $election->acceptance_stop;
			$elections[$election->id]["closes"] = $election->closes;
			foreach ($election->positions as $position) {
				$pos = [];
				$pos["id"] = $position->id;
				$pos["name"] = $position->name;
				$pos["description"] = $position->description;
				foreach ($position->nominees($election)->get() as $nominee) {
					$nom = [];
					$nom["name"] = $nominee->name;
					$nom["kth_username"] = $nominee->kth_username;
					$nom["pivot"]["status"] = $nominee->pivot->status;
					$pos["nominees"][] = $nom;
				}
				$elections[$election->id]["positions"][] = $pos;
			}
		}
		return response()->json($elections);
	}

	/**
	 * Searches for user in zfinger.
	 * 
	 * @param  Request $request the request that must contain the get parameter term
	 * @return string the zfinger response
	 */
	public function getSearch(Request $request) {
		$this->validate($request, [
			'term' => 'required'
		]);

		$url = env("ZFINGER_API_URL") . "/users/" . rawurlencode($request->input('term'));
		$get = file_get_contents($url);
		$obj = json_decode($get);
		$res = [];

		foreach ($obj->results as $entry) {
			$a = new \stdClass;
			$a->id = $entry->uid;
			$a->label = $entry->givenName . " " . $entry->sn . " (" . $entry->uid . "@kth.se)";
			$a->value = $entry->givenName . " " . $entry->sn;
			$a->name = $entry->givenName . " " . $entry->sn;
			$res[] = $a;
		}
		return response()->json($res);
	}
}