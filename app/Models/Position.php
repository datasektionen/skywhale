<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Position model. Represents a position (Post in Swedish).
 *
 * @author Jonas Dahl
 * @version 2016-10-14
 */
class Position extends Model {
    use SoftDeletes;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'positions';

    /**
     * The election for this position.
     *
     * @return relation
     */
    public function election() {
        return $this->hasOne('App\Models\Election');
    }

    public static function nominationsForUser($user_id, $openElections = null) {
        if ($openElections === null) {
            $openElections = Election::open();
        }

        $electionIds = [];
        foreach ($openElections as $election) {
            $electionIds[] = $election->id;
        }

        return DB::table('position_user')
            ->where('user_id', $user_id)
            ->whereNull('position_user.deleted_at')
            ->whereIn('position_user.election_id', $electionIds);
    }

    public static function forUser($user_id, $openElections = null) {
        $nominations = Position::nominationsForUser($user_id, $openElections)->get();
        
        $positions = Position::allKey();

        $res = [];
        foreach ($nominations as $nomination) {
            $obj = $nomination;
            $obj->positionObject = $positions[$nomination->position];
            $obj->electionObject = Election::find($nomination->election_id);
            $res[] = $obj;
        }
        
        return $res;
    }

    /**
     * Get all positions from API.
     * 
     * @param  array  $columns
     * @return array           the roles
     */
    public static function all($columns = array()) {
        $rolesString = file_get_contents('http://dfunkt.froyo.datasektionen.se/api/roles');
        $roles = json_decode($rolesString);

        return $roles;
    }

    public static function find($identifier) {
        $ans = file_get_contents("http://dfunkt.froyo.datasektionen.se/api/role/chefred");
        return json_decode($ans);
    }

    /**
     * Get all positions from API with their identifier as key.
     * 
     * @param  array  $columns
     * @return array  the roles
     */
    public static function allKey($columns = array()) {
        $rolesString = file_get_contents('http://dfunkt.froyo.datasektionen.se/api/roles');
        $roles = json_decode($rolesString);

        $res = [];
        foreach ($roles as $role) {
            $res[$role->identifier] = $role;
        }

        return $res;
    }

    /**
     * [dataForIds description]
     * @param  [type] $positions [description]
     * @return [type]            [description]
     */
    public static function dataForIds($positions) {
        $ans = [];
        $rolesString = file_get_contents('http://dfunkt.froyo.datasektionen.se/api/roles');
        $roles = json_decode($rolesString);

        foreach ($positions as $position) {
            foreach ($roles as $role) {
                if ($role->identifier == $position) {
                    $ans[] = $role;
                }
            }
        }
        return collect($ans);
    }
}
