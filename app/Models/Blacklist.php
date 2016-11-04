<?php namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

/**
 * Blacklist model that contains blacklisted email addresses.
 *
 * @author Jonas Dahl
 * @version 2016-11-04
 */
class Blacklist extends Model {
    protected $table = 'blacklist';

    /**
     * Returns true if given username is blacklisted. Input must be string (email address or kth username)
     * with length > 0.
     * @param  string  $kth_username username or email, length > 0
     * @return boolean               true if blacklisted, else otherwise
     */
    public static function isBlacklisted($kth_username) {
    	return Blacklist::where('kth_username', explode("@", $kth_username)[0])->count() > 0;
    }
}
