<?php namespace App\Models;

use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

/**
 * Election model. Represents an election, an event that has four timestamps
 * for opening, closing, nomination stop and acceptation stop.
 *
 * @author Jonas Dahl
 * @version 2016-10-14
 */
class Election extends Model {
    use SoftDeletes;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'elections';

    /**
     * The Positions open for voting in this election.
     *
     * @return relation
     */
    public function positions() {
        $rows = DB::select('SELECT * FROM election_position WHERE election_id = ?', [
            $this->id
        ]);

        $positions = [];
        foreach ($rows as $row) {
            $positions[] = $row->position;
        }

        return Position::dataForIds($positions);
    }

    /**
     * The Positions open for voting in this election.
     *
     * @return relation
     */
    public function positionIds() {
        $rows = DB::select('SELECT * FROM election_position WHERE election_id = ?', [
            $this->id
        ]);

        $positions = [];
        foreach ($rows as $row) {
            $positions[] = $row->position;
        }

        return $positions;
    }

    /**
     * Adds a position to the election.
     *
     * @param $identifier the position identifier from dfunct
     * @return true always and ever <3
     */
    public function addPosition($identifier) {
        DB::insert(
            'INSERT INTO election_position (position, election_id) values (?, ?)', 
            [
                $identifier,
                $this->id
            ]
        );
        return true;
    }

    /**
     * Removes all positions.
     *
     * @return true always and ever <3
     */
    public function removeAllPositions() {
        DB::delete(
            'DELETE FROM election_position WHERE election_id = ?', 
            [
                $this->id
            ]
        );
        return true;
    }

    /**
     * Get all open elections at the given timestep. If no argument is given the
     * point in time is right now.
     *
     * @param  $time the timestep to consider (default: now())
     * @return All elections that are open right now
     */
    public static function open($time = null) {
        if ($time === null)
            $now = Carbon::now(new DateTimeZone('Europe/Stockholm'));

        return Election::where('opens', '<', $now)->where('closes', '>', $now)->get();
    }

    /**
     * Get all nominateable elections at the given timestep. If no argument is given the
     * point in time is right now.
     *
     * @param  $time the timestep to consider (default: now())
     * @return All nominateable elections
     */
    public static function nominateable($time = null) {
        if ($time === null)
            $now = Carbon::now(new DateTimeZone('Europe/Stockholm'));

        return Election::where('opens', '<', $now)->where('nomination_stop', '>', $now)->get();
    }

    /**
     * Returns true if this election accepts anwers right now.
     * 
     * @return boolean true if users can accept positions right now, false otherwise
     */
    public function acceptsAnswers() {
        $tz = new DateTimeZone('Europe/Stockholm');
        $now = Carbon::now($tz);
        $opens = Carbon::createFromFormat("Y-m-d H:i:s", $this->opens, $tz);
        $stop = Carbon::createFromFormat("Y-m-d H:i:s", $this->acceptance_stop, $tz);
        return $opens->lt($now) && $stop->gt($now);
    }

    /**
     * Gets all positions open for election in the given elections.
     * 
     * @param  [Election] $elections List of elections to consider 
     * @return [Position]            List of positions for the current elections
     */
    public static function positionsForElections($elections) {
        $positions = [];
        foreach ($elections as $election) {
            $rows = DB::select('SELECT * FROM election_position WHERE election_id = ?', [
                $election->id
            ]);
            foreach ($rows as $row) {
                $positions[] = $row->position;
            }
        }

        return Position::dataForIds(array_unique($positions));
    }

    /**
     * Nominations for this position.
     *
     * @param  $position The position we want to know nominees of
     * @return relation
     */
    public function nominees($position) {
        $x = DB::table('position_user')
            ->join('users', 'users.id', '=', 'position_user.user_id')
            ->whereNull('position_user.deleted_at')
            ->where('position_user.election_id', '=', $this->id)
            ->where('position_user.position', '=', $position->identifier)
            ->orderBy(DB::raw(" CASE status 
                WHEN 'accepted' then 1 
                WHEN 'declined' then 3 
                ELSE 2 
                END"));
        return $x;
    }

    /**
     * Cascade deletion posts and nominations.
     * 
     * @return void
     */
    public function delete() {
        DB::table('position_user')
            ->where('election_id', $this->id)
            ->delete();

        DB::table('election_position')
            ->where('election_id', $this->id)
            ->delete();
            
        parent::delete();
    }



    /**
     * @deprecated 2016-10-16 bad name
     */
    public static function nominateableElections() {
        return Election::nominateable();
    }

    /**
     * @deprecated 2016-10-16 bad name
     */
    public static function positionsForAllNominateableElections() {
        return Election::positionsForElections(Election::nominateable());
    }
}
