<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers;
use Illuminate\Http\Request;

use \App\Models\Election;
use \App\Models\Position;
use \App\Models\User;
use \App\Models\Blacklist;

/**
 * Handles administrator actions concerning elections.
 *
 * @author  Jonas Dahl <jonas@jdahl.se>
 * @version 2016-10-14
 */
class BlacklistAdminController extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * Shows all blacklisted emails as a list.
	 * 
	 * @return view containing a list over blacklist entries
	 */
	public function getShow() {
		return view('admin.blacklist.index')
			->with('blacklist', Blacklist::all());
	}

	/**
	 * Shows a form for adding blacklist entry.
	 * 
	 * @return view containing a form
	 */
	public function getNew() {
		return view('admin.blacklist.new');
	}

	/**
	 * Shows a form for adding blacklist entry.
	 * 
	 * @return view containing a form
	 */
	public function postNew(Request $request) {
		$this->validate($request, [
			'kth_username' => 'required|email|kth_email|not_blacklisted'
		]);

		$blacklist = new Blacklist;
		$blacklist->kth_username = explode("@", $request->input('kth_username'))[0];
		$blacklist->save();

		return redirect('/admin/blacklist')
			->with('success', $request->input('kth_username') . ' lades till i blacklisten!');
	}

	/**
	 * Deletes blacklist entries.
	 * 
	 * @return redirect
	 */
	public function postRemove(Request $request) {
		$this->validate($request, [
			'remove' => 'required',
			'blackentries' => 'required|array|minCount:1'
		]);

		if (count($request->input('blackentries')) > 10) {
			return redirect()->back()->with('error', 'Du kan bara ta bort 10 åt gången.');
		}

		$i = 0;
		foreach ($request->input('blackentries') as $e) {
			$blackentry = Blacklist::find(intval($e));
			$blackentry->delete();
			$i++;
		}

		return redirect('/admin/blacklist')
			->with('success', 'Tog bort ' . $i . ' KTH-adresser!');
	}
}
