<?php namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Webpatser\Uuid\Uuid;
use Session;
use Illuminate\Database\Eloquent\SoftDeletes;
use GuzzleHttp\Client;
use Carbon\Carbon;
use DateTimeZone;
use DateInterval;

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

    public static function notAnswered() {
        $now = Carbon::now(new DateTimeZone('Europe/Stockholm'));

        $x = User::select('users.name', 'users.id', 'elections.name AS election_name', 'position_user.position', 'users.reminded')
            ->join('position_user', 'position_user.user_id', '=', 'users.id')
            ->join('election_position', function ($query) {
                $query->on('position_user.position', '=', 'election_position.position')
                    ->on('election_position.election_id', '=', 'position_user.election_id');
            })
            ->join('elections', 'elections.id', 'position_user.election_id')
            ->where('status', 'waiting')
            ->where(function ($query) use ($now) {
                $query->where(function ($query) use ($now) {
                    $query->whereNull('election_position.acceptance_stop')
                        ->where('elections.acceptance_stop', '>', $now);
                })->orWhere(function ($query) use ($now) {
                    $query->whereNotNull('election_position.acceptance_stop')
                        ->where('election_position.acceptance_stop', '>', $now);
                });
            })
            ->orderBy('users.reminded', 'ASC')
            ->get();

        return $x;
    }

    public static function notAnsweredAggregated() {
        $usersPositions = User::notAnswered();
        $res = [];
        foreach ($usersPositions as $up) {
            $res[$up->id][] = $up;
        }
        return $res;
    }

    public function remind() {
        $tz = new DateTimeZone('Europe/Stockholm');
        $now = Carbon::now($tz);
        $reminded = Carbon::createFromFormat("Y-m-d H:i:s", $this->reminded, $tz);
        $reminded->add(new DateInterval('P1D'));
        if ($reminded->gt($now)) {
            return false;
        }
        $x = User::select('users.name', 'users.id', 'elections.id AS election_id', 'elections.name AS election_name', 'position_user.position', 'users.reminded')
            ->join('position_user', 'position_user.user_id', '=', 'users.id')
            ->join('election_position', function ($query) {
                $query->on('position_user.position', '=', 'election_position.position')
                    ->on('election_position.election_id', '=', 'position_user.election_id');
            })
            ->join('elections', 'elections.id', 'position_user.election_id')
            ->where('status', 'waiting')
            ->where('users.id', $this->id)
            ->where(function ($query) use ($now) {
                $query->where(function ($query) use ($now) {
                    $query->whereNull('election_position.acceptance_stop')
                        ->where('elections.acceptance_stop', '>', $now);
                })->orWhere(function ($query) use ($now) {
                    $query->whereNotNull('election_position.acceptance_stop')
                        ->where('election_position.acceptance_stop', '>', $now);
                });
            })
            ->get();
        
        foreach ($x as $position) {
            $positionIds[] = $position->position;
        }
        $positions = Position::dataForIds($positionIds);

        $to = $this->kth_username . "@kth.se";
        $from = "valberedning@d.kth.se";
        $subject = "Påminnelse: Svara på dina nomineringar";
        $html = view('emails.remind')
            ->with('person', $this)
            ->with('positions', $positions)
            ->with('election', Election::find($x->first()->election_id));
        $postData = [
            'to' => $to,
            'from' => $from,
            'subject' => $subject,
            'html' => $html,
            'key' => env('SPAM_API_KEY')
        ];
        $concat = function ($array) {
            $res = "";
            foreach ($array as $key => $val) {
                $res .= $key . "=" . rawurlencode($val) . "&";
            }
            return $res;
        };
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('SPAM_API_URL'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $concat($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);

        $this->reminded = $now;
        $this->save();

        return true;
    }

    /**
     * Nominates person to position.
     *
     * @param int $electionId The id of the election to nominate to
     * @param int $positionId The id of the position to nominate to
     * @return true if successful, false otherwise
     */
    public function nominate($electionId, $positionId) {
        $election = Election::find($electionId);

        if ($election === null)
            return false;

        if (DB::table('position_user')
            ->where('user_id', $this->id)
            ->where('position', $positionId)
            ->where('election_id', $election->id)
            ->get()->count() > 0)
            return false;

        DB::insert('INSERT INTO position_user (user_id, position, election_id, status, uuid, deleted_at) values (?, ?, ?, ?, ?, ?)', [
            $this->id,
            $positionId,
            $election->id,
            'waiting',
            Uuid::generate()->string,
            null
        ]);

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
    public function bulkNominate($electionPositions) {
        $positionIds = [];
        $shouldSendMail = false;
        foreach ($electionPositions as $electionPosition) {
            $parts = explode("_", $electionPosition);
            if (count($parts) != 2) {
                continue;
            }
            $election = Election::find($parts[0]);
            if ($election === null) {
                continue;
            }
            $positionIds[] = $parts[1];
            $shouldSendMail = $this->nominate($election->id, $parts[1]) || $shouldSendMail;
        }

        if (!$shouldSendMail) {
            return false;
        }

        $to = $this->kth_username . "@kth.se";
        $positions = Position::dataForIds($positionIds);
        $from = "valberedning@d.kth.se";
        $subject = "Du har nya nomineringar";
        $html = view('emails.notify-nomination')
            ->with('person', $this)
            ->with('election', $election)
            ->with('positions', $positions);
        $postData = [
            'to' => $to,
            'from' => $from,
            'subject' => $subject,
            'html' => $html,
            'key' => env('SPAM_API_KEY')
        ];
        $concat = function ($array) {
            $res = "";
            foreach ($array as $key => $val) {
                $res .= $key . "=" . rawurlencode($val) . "&";
            }
            return $res;
        };
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('SPAM_API_URL'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $concat($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);

        return true;
    }

    /**
     * Returns true if this user is admin.
     * 
     * @return boolean true if admin
     */
    public function isAdmin() {
        return Session::has('admin') && Session::get('admin') === $this->id;
    }

    /**
     * Cascade deletion to nominations.
     * 
     * @return void
     */
    public function delete() {
        DB::table('position_user')
            ->where('user_id', $this->id)
            ->delete();
            
        parent::delete();
    }
}
