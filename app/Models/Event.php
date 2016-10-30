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
class Event extends Model {
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events';

    public function election() {
        return $this->belongsTo('App\Models\Election');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
