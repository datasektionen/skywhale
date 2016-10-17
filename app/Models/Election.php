<?php namespace App\Models;

use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        return $this->belongsToMany('App\Models\Position');
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
            foreach ($election->positions as $position) {
                $positions[$position->id] = $position;
            }
        }
        return $positions;
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
