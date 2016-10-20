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
use Illuminate\Pagination\LengthAwarePaginator;

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
		$p = Position::all();
		$positions = new LengthAwarePaginator($p, count($p), 20);
		return view('admin.positions.index')->with('positions', $positions);
	}
}