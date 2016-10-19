<?php namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Webpatser\Uuid\Uuid;
use Session;
use Illuminate\Database\Eloquent\SoftDeletes;
use GuzzleHttp\Client;

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
    public function bulkNominate($electionId, $positionIds) {
        $election = Election::find($electionId);

        $shouldSendMail = false;
        foreach ($positionIds as $positionId) {
            $shouldSendMail = $this->nominate($electionId, $positionId) || $shouldSendMail;
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
        print_r($server_output);
        
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
}
