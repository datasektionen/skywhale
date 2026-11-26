<?php namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use stdClass;

/**
 * Position model. Represents a position (Post in Swedish).
 * Data source is the dfunkt API.
 */
class Position extends Model {
    /**
     * Returns all nominations for the specified user.
     *
     * @param  int    $userId        the id of the user to get nominations for
     * @param  [Election]  $openElections A list of elections, will default to all open ones if null or not given
     * @return DB query              querying for all relevant rows in position_user
     */
    public static function nominationsForUser(int $userId, Collection $openElections = null) {
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
    public static function forUser(int $user_id, Collection $openElections = null): array {
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
     * Get all active positions from API.
     *
     * @param  $columns
     * @return json decoded roles list
     */
    public static function active(array $columns = array()): array {
        $rolesString = file_get_contents('https://dfunkt.datasektionen.se/api/active-roles');
        $roles = json_decode($rolesString);
        usort($roles, function ($a, $b) {
            return strcmp($a->title, $b->title);
        });
        return $roles;
    }

    /**
     * Get positions with a mandate ending within three months.
     *
     * @return end dates keyed by dfunkt identifier
     */
    public static function expiring(): array {
        $rolesString = file_get_contents('https://dfunkt.datasektionen.se/api/roles/all/current');
        $roles = json_decode($rolesString);
        $limit = now()->addMonths(3)->toDateString();

        $res = [];
        foreach ($roles as $role) {
            foreach ($role->Mandates as $mandate) {
                if ($mandate->end <= $limit) {
                    $res[$role->identifier] = min($res[$role->identifier] ?? $mandate->end, $mandate->end);
                }
            }
        }
        return $res;
    }

    /**
     * Get one Position from API. Notice: This function queries the API. Do not spam.
     *
     * @param  $identifier the defunct identifier
     * @return json decoded response from api
     */
    public static function find(string $identifier): stdClass {
        $ans = file_get_contents("https://dfunkt.datasektionen.se/api/role/" . $identifier);
        return json_decode($ans);
    }

    /**
     * Get all positions from API with their dfunkt identifier as key.
     *
     * @param  $columns
     * @return the roles
     */
    public static function allKey(array $columns = array()): array {
        $rolesString = file_get_contents('https://dfunkt.datasektionen.se/api/active-roles');
        $roles = json_decode($rolesString);

        $res = [];
        foreach ($roles as $role) {
            $res[$role->identifier] = $role;
        }

        return $res;
    }

    /**
     * Returns a list of dfunkt roles. The list only contains roles that are on the $positions argument list.
     * @param  $positions dfunkt identifiers
     * @return collection of positions
     */
    public static function dataForIds(array $positions): Collection {
        $ans = [];
        $rolesString = file_get_contents('https://dfunkt.datasektionen.se/api/roles');
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
