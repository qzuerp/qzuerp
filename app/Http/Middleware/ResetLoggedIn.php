<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResetLoggedIn
{
    public function handle(Request $request, Closure $next)
    {
        $lifetime = config('session.lifetime');

        if (Auth::check()) {
            $userId = Auth::id();
            DB::table('users')->where('id', $userId)
                ->update(['last_activity' => Carbon::now()]);
            return $next($request);
        }

        if (mt_rand(1, 1) === 1) {
            $threshold = Carbon::now()->subMinutes($lifetime);
            DB::table('users')
                ->where('is_logged_in', 1)
                ->where('last_activity', '<', $threshold)
                ->update(['is_logged_in' => 0, 'last_activity' => null]);
        }

        return $next($request);
    }
}
