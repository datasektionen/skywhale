<?php namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Blacklist model that contains blacklisted email addresses.
 */
#[Table('blacklist')]
class Blacklist extends Model {
    /**
     * Returns true if given username is blacklisted. Input must be string (email address or kth username)
     * with length > 0.
     * @param  string  $kth_username username or email, length > 0
     * @return boolean               true if blacklisted, else otherwise
     */
    public static function isBlacklisted(string $kth_username): bool {
    	return Blacklist::where('kth_username', explode("@", $kth_username)[0])->count() > 0;
    }
}
