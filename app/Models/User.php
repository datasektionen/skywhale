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
use Auth;

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
        if ($this->reminded !== null) {
            $reminded = Carbon::createFromFormat("Y-m-d H:i:s", $this->reminded, $tz);
            $reminded->add(new DateInterval('P1D'));
            if ($reminded->gt($now)) {
                return false;
            }
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
        $from = "valberedning@datasektionen.se";
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

    public function accept($uuid) {
        // Get election and see if acceptance stop has been
        $row = \DB::table('position_user')
            ->where('uuid', $uuid)
            ->first();

        // Only accept answer if open
        if ($row === null || !Election::find($row->election_id)->acceptsAnswers($uuid)) {
            return false;
        }

        if ($row->status == 'accepted') {
            return false;
        }
        
        // Change status to accepted
        \DB::table('position_user')
            ->where('uuid', $uuid)
            ->update(['status' => 'accepted']);

        $event = new Event;
        $event->user_id = $row->user_id;
        $event->election_id = $row->election_id;
        $event->action = "accepted";
        $event->position = $row->position;
        $event->save();

        return true;
    }

    public function decline($uuid) {
        // Get election and see if acceptance stop has been
        $row = \DB::table('position_user')
            ->where('uuid', $uuid)
            ->first();

        // Only accept answer if open
        if ($row === null || !Election::find($row->election_id)->acceptsAnswers($uuid)) {
            return false;
        }

        if ($row->status == 'declined') {
            return false;
        }
        
        // Change status to declined
        \DB::table('position_user')
            ->where('uuid', $uuid)
            ->update(['status' => 'declined']);

        $event = new Event;
        $event->user_id = $row->user_id;
        $event->election_id = $row->election_id;
        $event->action = "declined";
        $event->position = $row->position;
        $event->save();

        return true;
    }

    public function regret($uuid) {
        // Get election and see if acceptance stop has been
        $row = \DB::table('position_user')
            ->where('uuid', $uuid)
            ->first();

        if ($row->status == 'waiting') {
            return false;
        }
        
        // Change status to waiting
        \DB::table('position_user')
            ->where('uuid', $uuid)
            ->update(['status' => 'waiting']);

        $event = new Event;
        $event->user_id = $row->user_id;
        $event->election_id = $row->election_id;
        $event->action = "regretted";
        $event->position = $row->position;
        $event->save();

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

        $event = new Event;
        $event->user_id = $this->id;
        $event->election_id = $election->id;
        $event->action = "nominated";
        $event->position = $positionId;
        $event->save();

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

        if ($this->wants_email != "yes") {
            return true;
        }

        $to = $this->kth_username . "@kth.se";
        $positions = Position::dataForIds($positionIds);
        $from = "valberedning@datasektionen.se";
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

        foreach ($positionIds as $p) {
            DB::table('position_user')->where('position', '=', $p)->where('user_id', '=', $this->id)->where('election_id', '=', $election->id)->update(['notified' => DB::raw('NOW()')]);
        }

        return true;
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

    public static function notDecidedEmail() {
        return User::where('wants_email', 'unknown')->get();
    }

    public static function countNewUsers() {
        return User::where('wants_email', 'unknown')->count();
    }

    public function notify() {
        // TODO Check if elections still are open, do not notify old elections
        $positionPivot = DB::table('position_user')
            ->where('user_id', '=', $this->id)
            ->whereIn('election_id', Election::open()->pluck('id'))
            ->get();

        if ($positionPivot->count() == 0) {
            return true;
        }

        $election = Election::find($positionPivot->first()->election_id);

        $positionIds = [];
        foreach ($positionPivot as $pp) {
            $positionIds[] = $pp->position;
        }

        $to = $this->kth_username . "@kth.se";
        $positions = Position::dataForIds($positionIds);
        $from = "valberedning@datasektionen.se";
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

        DB::table('position_user')
            ->where('user_id', '=', $this->id)
            ->update(['notified' => DB::raw('NOW()')]);
    }
}
