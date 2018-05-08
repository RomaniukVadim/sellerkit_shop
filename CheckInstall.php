<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\DB;
use Closure;

class CheckInstall
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		try {
			DB::connection()->getPdo();
		} catch (\Exception $e) {
			return redirect('install');
		}
        return $next($request);
    }
}
