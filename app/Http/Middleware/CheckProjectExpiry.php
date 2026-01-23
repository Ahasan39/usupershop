<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CheckProjectExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // $expiryDate = Carbon::parse(env('PROJECT_EXPIRY_DATE'));
        // $today = Carbon::now();

        // if ($today->gt($expiryDate)) {
        //     return response()->view('project_expired');
        // }

        if (Auth::check() && Auth::user()->status == 0 && Auth::user()->code !== null) {
            return redirect()->route('logout2');
        }

        return $next($request);
    }
}
