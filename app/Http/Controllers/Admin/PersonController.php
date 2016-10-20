<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Election;
use App\Models\Position;
use App\Models\User;

use Session;

/**
 * Handles request concerning administration of persons.
 *
 * @author  Jonas Dahl <jonas@jdahl.se>
 * @version 2016-10-14
 */
class PersonAdminController extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * Show a list of all persons.
	 * 
	 * @return view showing all the persons in a table
	 */
	public function getShow() {
		$persons = User::orderBy('name', 'ASC')->paginate(20);
		return view('admin.persons.index')->with('persons', $persons);
	}

	/**
	 * Show a create person form.
	 * 
	 * @return view the create person form view
	 */
	public function getNew() {
		return view('admin.persons.new');
	}

	/**
	 * Show a edit person form. Returns 400 if person $id not found.
	 * 
	 * @param  int      $id the id to edit
	 * @return view     the view containing the form
	 */
	public function getEdit($id) {
		$person = User::find($id);
		if ($person === null) {
			abort(400);
		}

		return view('admin.persons.edit')
			->with('person', $person);
	}

	/**
	 * Handles post request when user wants to add a new person.
	 * Just saves supplied info to database. Request must contain
	 * 'name', or it will break.
	 * 
	 * @param  Request $request the post request
	 * @return redirect         to /admin/persons
	 */
	public function postNew(Request $request) {
		$this->validate($request, [
			'name' => 'required'
		]);

		$person = new User;
		$person->name = $request->input('name');
		$person->kth_user_id = $request->input('kth_user_id');
		$person->kth_username = $request->input('kth_username');
		$person->year = $request->input('year');
		$person->save();

		return redirect('/admin/persons')->with('success', 'Personen skapades.');
	}

	/**
	 * Handles post request when user wants to edit a person.
	 * Just saves supplied info to database. Request must contain
	 * 'name', or it will break.
	 *
	 * @param  int 	   $id 		id of the person to edit
	 * @param  Request $request the post request
	 * @return redirect         to /admin/persons or back if person $id was not found
	 */
	public function postEdit($id, Request $request) {
		$this->validate($request, [
			'name' => 'required'
		]);

		$person = User::find($id);
		if ($person === null) {
			return redirect()->back()->withInput()->with('error', 'Personen kunde inte hittas.');
		}
		$person->name = $request->input('name');
		$person->kth_username = $request->input('kth_username');
		$person->year = $request->input('year');
		$person->save();

		return redirect('/admin/persons')->with('success', 'Ändringarna genomfördes.');
	}

	/**
	 * Handles post request on mergin two or more persons.
	 * 
	 * @param  Request $request the post request
	 * @return view             a view asking about more information
	 */
	public function postMerge(Request $request) {
		$this->validate($request, [
			'users' => 'required|array|minCount:2',
		]);

		return redirect('/admin/persons/merge')
			->with('u', $request->input('users'));
	}

	public function getMerge() {
		$inputUsers = session('u');
		if (!is_array($inputUsers)) {
			return redirect('/admin/persons')->with('error', 'Ett fel uppstod.');
		}
		$users = []; // For saving the User objects instead of ids
		$info  = []; // For saving what to present as standard values in form
		$personsString = ""; // The comma-separated string over user ids

		foreach ($inputUsers as $u) {
			$user = User::find($u);
			if ($user === null) continue; // If we could not find user, skip

			// Add up to personsString
			if (strlen($personsString > 0)) {
				$personsString .= ",";
			}
			$personsString .= $user->id;

			// Save user to array and maybe build on $info
			$users[] = $user;
			if (strlen($user->name) > 0) 		 $info['name'] = $user->name;
			if (strlen($user->kth_user_id) > 0)  $info['kth_user_id'] = $user->kth_user_id;
			if (strlen($user->kth_username) > 0) $info['kth_username'] = $user->kth_username;
			if (strlen($user->year) > 0)		 $info['year'] = $user->year;
		}

		// TODO Remove all nominations for both users and then add them to skip duplicates 

		// If we have reduced the number of users below 2, throw error
		if (count($users) < 2) {
			redirect('/admin/persons')->with('error', 'Finns för få personer att slå ihop.');
		}

		return view('admin.persons.merge')
			->with('persons', $users)
			->with('info', $info)
			->with('personsString', $personsString);
	}

	/**
	 * Handles post request on mergin persons. Actually merging is done here.
	 * 
	 * @param  Request $request the post request
	 * @return redirect         to /admin/persons
	 */
	public function postMergeFinal(Request $request) {
		$validator = \Validator::make($request->all(), [
			'name' => 'required',
			'kth_username' => 'required|email|regex:/[^@]*@kth\.se/',
			'kth_user_id' => '',
			'year' => ''
		]);

		if ($validator->fails()) {
            return redirect()->back()
            	->with('u', explode(",", $request->input('persons')))
                ->withErrors($validator)
                ->withInput();
        }
		
		// Create new person
		$person = new User;
		$person->name = $request->input('name');
		$person->kth_username = explode("@", $request->input('kth_username'))[0];
		$person->kth_user_id = $request->input('kth_user_id');
		$person->year = $request->input('year');
		$person->save();
		
		// Point all the persons to the new person and delete old persons
		$user_ids = explode(",", $request->input('persons'));
		echo $request->input('persons') . "<br>";
		foreach ($user_ids as $uid) {
			echo $uid . "<br>";
			\DB::table('position_user')
	            ->where('user_id', $uid)
	            ->update(['user_id' => $person->id]);
			User::destroy($uid);
		}

		return redirect('/admin/persons')->with('success', 'Personerna slogs ihop.');
	}

	/**
	 * Handles get request on removing persons. Shows confirm view.
	 *
	 * @param $id the id of the Person to remove
	 * @return view with confirmation page
	 */
	public function getRemove($id) {
		$person = User::find($id);

		if ($person === null) {
			return redirect()->back()->with('error', 'Kunde inte hitta personen.');
		}

		return view('admin.persons.remove')->with('person', $person);
	}

	/**
	 * Handles get request on removing persons. Removes person with $id and returns redirect.
	 * 
	 * @param $id the id of the Person to remove
	 * @return redirect         to /admin/persons
	 */
	public function getRemoveConfirmed($id) {
		$person = User::find($id);

		if ($person === null) {
			return redirect('/admin/persons')->with('error', 'Kunde inte hitta personen.');
		}

		$person->delete();

		return redirect('/admin/persons')->with('Användaren togs bort.');
	}
}