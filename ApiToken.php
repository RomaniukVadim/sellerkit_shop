<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\DB;
use Closure;

class ApiToken
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
		If (!isset($_GET['token'])) {
			return response('Please provide an API token',401);
		} else {
			if (DB::select("SELECT COUNT(*) as count FROM tokens WHERE token = '".$_GET['token']."'")[0]->count > 0) {
				DB::update("UPDATE tokens SET requests = requests + 1 WHERE token = '".$_GET['token']."'");
				return $next($request);
			} else {
				return response('Invalid API token',401);
			}
		}
    }
}
