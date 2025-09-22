<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ResetLoggedIn
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }

        if ($request->session()->has('user_id')) {
            $userId = $request->session()->get('user_id');
            DB::table('users')->where('id', $userId)->update(['is_logged_in' => 0]);
            $request->session()->forget('user_id');
        }

        return $next($request);
    }
}
