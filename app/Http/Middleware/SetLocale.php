<?php

namespace App\Http\Middleware;

use App\AppCache;
use App\AppConfig;
use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        AppConfig::setLocale($request->segment(1));

        return $next($request);
    }
}
