<?php namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Election;
use App\Models\Position;
use App\Models\User;

/**
 * Handles requests on administrating positions.
 *
 * @author  Jonas Dahl <jonas@jdahl.se>
 * @version 2016-10-14
 */
class PositionAdminController extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * Shows all positions.
	 * 
	 * @return view the view with all the poisitions
	 */
	public function getShow() {
		$positions = \App\Models\Position::orderBy('name', 'DESC')->paginate(20);
		return view('admin.positions.index')->with('positions', $positions);
	}

	/**
	 * View for creating new position.
	 * 
	 * @return view the view with the form
	 */
	public function getNew() {
		return view('admin.positions.new');
	}

	/**
	 * View for editing position. Abort(400) if $id is not found in database.
	 * 
	 * @param  int $id the id of the position
	 * @return view    the view with the edit form and position data sent to $position
	 */
	public function getEdit($id) {
		$position = Position::find($id);
		if ($position === null)
			abort(400);

		return view('admin.positions.edit')
			->with('position', $position);
	}

	/**
	 * Handles post request on creating of position. Request
	 * must contain 'name' and 'description' of new position.
	 * 
	 * @param  Request $request the request to handle
	 * @return redirect         to /admin/positions
	 */
	public function postNew(Request $request) {
		$this->validate($request, [
			'name' => 'required',
			'description' => 'required'
		]);

		$position = new Position;
		$position->name = $request->input('name');
		$position->description = $request->input('description');
		$position->save();

		return redirect('/admin/positions');
	}

	/**
	 * Handles post request on editing a position. Request must contain
	 * 'name' and 'description' of position. $id must also be supplied.
	 * 
	 * @param  int     $id      the id of the post to edit
	 * @param  Request $request the post request
	 * @return redirect         to /admin/positions or back if position $id was not found
	 */
	public function postEdit($id, Request $request) {
		$this->validate($request, [
			'name' => 'required',
			'description' => 'required'
		]);

		$position = Position::find($id);
		if ($position === null) {
			return redirect()->back()->withInput()->with('error', 'Posten kunde inte hittas.');
		}
		$position->name = $request->input('name');
		$position->description = $request->input('description');
		$position->save();

		return redirect('/admin/positions');
	}
}