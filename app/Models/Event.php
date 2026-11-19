<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Election model. Represents an election, an event that has four timestamps
 * for opening, closing, nomination stop and acceptation stop.
 */
class Event extends Model {
    public function election(): BelongsTo {
        return $this->belongsTo('App\Models\Election');
    }

    public function user(): BelongsTo {
        return $this->belongsTo('App\Models\User');
    }
}
