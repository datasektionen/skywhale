<?php namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Webpatser\Uuid\Uuid;
use Session;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * User model. Represents a user.
 *
 * @author Jonas Dahl
 * @version 2016-10-14
 */
class User extends Authenticatable {
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Nominates person to position.
     *
     * @param int $electionId The id of the election to nominate to
     * @param int $positionId The id of the position to nominate to
     * @return true if successful, false otherwise
     */
    public function nominate($electionId, $positionId) {
        $election = Election::find($electionId);
        $position = Position::find($positionId);

        if ($election === null || $position === null)
            return false;

        if ($position->nominees($election)->get()->contains($this))
            return false;

        DB::insert(
            'INSERT INTO position_user (user_id, position_id, election_id, status, uuid, deleted_at) values (?, ?, ?, ?, ?, ?)', 
            [
                $this->id,
                $position->id,
                $election->id,
                'waiting',
                Uuid::generate()->string,
                null
            ]
        );
        // TODO Check if insert was successful
        return true;
    }

    /**
     * Nominates user to a bunch of positions.
     * 
     * @param int $electionId The id of the election to nominate to
     * @param [int] $positionIds The ids of the positions to nominate to
     * @return void
     */
    public function bulkNominate($electionId, $positionIds) {
        foreach ($positionIds as $positionId) {
            $this->nominate($electionId, $positionId);
        }

        // TODO Skicka mejl
        // Med länk till samtliga personens nomineringar, som ska finnas på 
        // /nomination/answer om personen är inloggad.
    }

    /**
     * Returns a relation of Positions belonging to this user 
     * via nominations in the given elections.
     * 
     * @param  [Election] $openElections A list of elections that the Positions are in
     * @return Relation the positions as a relation
     */
    public function nominations($openElections = null) {
        if ($openElections === null) {
            $openElections = Election::open();
        }

        $electionIds = [];
        foreach ($openElections as $election) {
            $electionIds[] = $election->id;
        }

        return $this
            ->belongsToMany('App\Models\Position')
            ->withPivot('status','uuid','election_id')
            ->whereNull('position_user.deleted_at')
            ->whereIn('position_user.election_id', $electionIds);
    }

    /**
     * Returns true if this user is admin.
     * 
     * @return boolean true if admin
     */
    public function isAdmin() {
        return Session::has('admin') && Session::get('admin') === $this->id;
    }
}
