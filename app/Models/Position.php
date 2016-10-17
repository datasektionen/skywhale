<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Position model. Represents a position (Post in Swedish).
 *
 * @author Jonas Dahl
 * @version 2016-10-14
 */
class Position extends Model {
    use SoftDeletes;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'positions';

    /**
     * Nominations for this position.
     *
     * @param  $election The election we want to know nominees of
     * @return relation
     */
    public function nominees($election) {
        return $this
            ->belongsToMany('App\Models\User')
            ->withPivot('status', 'uuid')
            ->whereNull('position_user.deleted_at')
            ->where('position_user.election_id', '=', $election->id)
            ->orderBy(DB::raw("FIELD(status, 'acccepted', 'waiting', 'declined')"));
    }

    /**
     * The election for this position.
     *
     * @return relation
     */
    public function election() {
        return $this->hasOne('App\Models\Election');
    }
}
