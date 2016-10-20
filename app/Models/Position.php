<?php namespace App\Models;

use DB;

/**
 * Position model. Represents a position (Post in Swedish).
 * Data source is the dfunkt API.
 *
 * @author Jonas Dahl
 * @version 2016-10-14
 */
class Position {
    /**
     * Returns all nominations for the specified user.
     * 
     * @param  int    $userId        the id of the user to get nominations for
     * @param  [Election]  $openElections A list of elections, will default to all open ones if null or not given
     * @return DB query              querying for all relevant rows in position_user
     */
    public static function nominationsForUser($userId, $openElections = null) {
        if ($openElections === null) {
            $openElections = Election::open();
        }

        $electionIds = [];
        foreach ($openElections as $election) {
            $electionIds[] = $election->id;
        }

        return DB::table('position_user')
            ->where('user_id', $userId)
            ->whereNull('position_user.deleted_at')
            ->whereIn('position_user.election_id', $electionIds);
    }

    /**
     * Get all nominated positions for given user in given elections.
     * 
     * @param  int         $userId        the id of the user to get nominations for
     * @param  [Election]  $openElections A list of elections, will default to all open ones if null or not given
     * @return [Position]                 A list of Positions connected with the user and election
     */
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
     * @return json decoded roles list
     */
    public static function all($columns = array()) {
        $rolesString = file_get_contents('http://dfunkt.froyo.datasektionen.se/api/roles');
        $roles = json_decode($rolesString);
        usort($roles, function ($a, $b) {
            return strcmp($a->title, $b->title);
        });
        return $roles;
    }

    /**
     * Get one Position from API. Notice: This function queries the API. Do not spam.
     *
     * @param  string $identifier the defunct identifier
     * @return json decoded response from api
     */
    public static function find($identifier) {
        $ans = file_get_contents("http://dfunkt.froyo.datasektionen.se/api/role/" . $identifier);
        return json_decode($ans);
    }

    /**
     * Get all positions from API with their dfunkt identifier as key.
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
     * Returns a list of dfunkt roles. The list only contains roles that are on the $positions argument list.
     * @param  [string] $positions dfunkt identifiers
     * @return collection of positions
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
