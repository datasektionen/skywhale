<?php namespace App\Models;

use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use DateTime;

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
    public function positions($onlyNominateable = false) {
        $now = Carbon::now(new DateTimeZone('Europe/Stockholm'));

        $db = DB::table('election_position')
            ->select('elections.name', 'election_position.*')
            ->join('elections', 'elections.id', '=', 'election_position.election_id')
            ->where('election_id', $this->id);

        if ($onlyNominateable) {
            $db = $db->where(function ($query) use ($now) {
                $query->where(function ($query) use ($now) {
                    $query->whereNull('election_position.nomination_stop')
                        ->where('elections.nomination_stop', '>', $now);
                })->orWhere(function ($query) use ($now) {
                    $query->whereNotNull('election_position.nomination_stop')
                        ->where('election_position.nomination_stop', '>', $now);
                });
            });
        }

        $rows = $db->get();

        $positions = [];
        $pivots = [];
        foreach ($rows as $row) {
            $positions[$row->position] = $row->position;
            $pivots[$row->position] = $row;
        }

        $positionsWithData = Position::dataForIds($positions);
        $res = [];
        foreach ($positionsWithData as $p) {
            $res[$p->identifier] = $p;
            $res[$p->identifier]->pivot = $pivots[$p->identifier];
        }

        return collect($res);
    }

    /**
     * The Positions open for voting in this election.
     *
     * @return relation
     */
    public function positionsPivot() {
        $rows = DB::select('SELECT * FROM election_position WHERE election_id = ?', [
            $this->id
        ]);

        $pivots = [];
        foreach ($rows as $row) {
            $pivots[$row->position] = $row;
        }

        return collect($pivots);
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
        $count = DB::table('election_position')
            ->where('position', $identifier)
            ->where('election_id', $this->id)
            ->count();
        if ($count > 0)
            return false;

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
    public function removeAllPositions($except = null) {
        if ($except == null) {
            $except = collect([]);
        }

        DB::table('election_position')
            ->where('election_id', $this->id)
            ->whereNotIn('election_position.position', $except)
            ->delete();
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
        else
            $now = $time;

        $q = Election::where('opens', '<', $now)
            ->where(function($query) use ($now) {
                $query->where('nomination_stop', '>', $now)
                    ->orWhere(function($query) use ($now) {
                        $query->whereExists(function ($query) use ($now) {
                            $query->select(DB::raw(1))
                                  ->from('election_position')
                                  ->whereRaw('election_position.election_id = elections.id')
                                  ->whereNotNull('election_position.nomination_stop')
                                  ->where('election_position.nomination_stop', '>', $now);
                        });
                    });
            });

        return $q->get();
    }

    /**
     * Returns true if this election accepts anwers right now.
     *
     * @return boolean true if users can accept positions right now, false otherwise
     */
    public function acceptsAnswers($uuid) {
        $row = DB::table('position_user')
            ->select('election_position.acceptance_stop')
            ->join('election_position', 'election_position.position', '=', 'position_user.position')
            ->where('uuid', $uuid)
            ->where('election_position.election_id', $this->id)
            ->first();

        if (!$row) return false;

        $tz = new DateTimeZone('Europe/Stockholm');
        $now = Carbon::now($tz);
        $opens = Carbon::createFromFormat("Y-m-d H:i:s", $this->opens, $tz);
        $stop = Carbon::createFromFormat("Y-m-d H:i:s", $this->acceptance_stop, $tz);
        if (property_exists($row, 'acceptance_stop') && $row->acceptance_stop !== null)
            $specStop = Carbon::createFromFormat("Y-m-d H:i:s", $row->acceptance_stop, $tz);

        return
            $opens->lt($now)
            && (
                (
                    !isset($specStop) && $stop->gt($now)
                )
                ||
                (
                    isset($specStop) && $specStop->gt($now)
                )
            );
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
            ->select('users.name AS name', 'users.id AS user_id', 'status', 'uuid', 'kth_username', 'elections.acceptance_stop AS acceptance_stop', 'election_position.acceptance_stop AS a_s')
            ->join('users', 'users.id', '=', 'position_user.user_id')
            ->join('elections', 'elections.id', '=', 'position_user.election_id')
            ->join('election_position', 'elections.id', '=', 'election_position.election_id')
            ->where('election_position.position', '=', DB::raw('position_user.position'))
            ->whereNull('position_user.deleted_at')
            ->where('position_user.election_id', '=', $this->id)
            ->where('position_user.position', '=', $position->identifier)
            ->orderBy(DB::raw(" CASE status
                WHEN 'accepted' then 1
                WHEN 'declined' then 3
                ELSE 2
                END"));
        $collection = $x->get();

        $tz = new DateTimeZone('Europe/Stockholm');
        $now = Carbon::now($tz);

        foreach ($collection as $nomination) {
            $acceptanceStop = Carbon::createFromFormat("Y-m-d H:i:s", $nomination->acceptance_stop, $tz);
            if ($nomination->a_s != null)
                $acceptanceStopLocal = Carbon::createFromFormat("Y-m-d H:i:s", $nomination->a_s, $tz);
            if  (
                    (
                        (
                            $nomination->a_s === null && $acceptanceStop->lt($now)
                        ) ||
                        (
                            $nomination->a_s !== null && $acceptanceStopLocal->lt($now)
                        )
                    ) &&
                    $nomination->status != 'accepted'
                ) {
                $nomination->status = 'declined';
            }
        }
        //die("Hej: " . $collection->count());
        return $collection;
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

    public function setPositionCount($positionIdentifier, $count) {
        DB::table('election_position')
            ->where('election_id', $this->id)
            ->where('position', $positionIdentifier)
            ->update(['count' => $count]);
    }

    /**
     * Sets the nomination stop for position.
     *
     * @param string $positionIdentifier the position
     * @param string(Y-m-d H:i) $value the datetime
     * @param bool $null true if should be null
     */
    public function setNominationStop($positionIdentifier, $value, $null) {
        DB::table('election_position')
            ->where('election_id', $this->id)
            ->where('position', $positionIdentifier)
            ->update(['nomination_stop' => $null ? null : date("Y-m-d H:i:s", strtotime($value))]);
    }

    /**
     * Sets the acceptance stop for position.
     *
     * @param string $positionIdentifier the position
     * @param string(Y-m-d H:i) $value the datetime
     * @param bool $null true if should be null
     */
    public function setAcceptanceStop($positionIdentifier, $value, $null) {
        DB::table('election_position')
            ->where('election_id', $this->id)
            ->where('position', $positionIdentifier)
            ->update(['acceptance_stop' => $null ? null : date("Y-m-d H:i:s", strtotime($value))]);
    }


    /**
     * @deprecated 2016-10-16 bad name
     */
    public static function nominateablePositions() {
        $positions = collect([]);
        foreach (Election::open() as $election) {
            $p = $election->positions(true);
            if ($p->count() > 0)
                $positions->push($p);
        }
        return $positions;
    }

    /**
     * @deprecated 2016-10-16 bad name
     */
    public static function positionsForAllNominateableElections() {
        return Election::positionsForElections(Election::nominateable());
    }
}
