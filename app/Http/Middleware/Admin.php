<?php namespace App\Http\Middleware;

use Closure;
use Auth;

/**
 * Handles admin requests. If user is not admin, send to '/'.
 *
 * @author Jonas Dahl <jonas@jdahl.se>
 * @version 2016-10-14 
 */
class Admin {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (!Auth::check() || !($request->session()->has('admin') && $request->session()->get('admin') == Auth::user()->id)) {
            return redirect('/')->with('error', 'Du har inte behörighet att visa den här sidan.');
        }

        return $next($request);
    }
}
