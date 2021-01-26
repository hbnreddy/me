<?php

namespace App\Http\Middleware;

use App\AccessCode;
use App\AppConfig;
use Closure;
use Illuminate\Support\Facades\Cookie;

class PassKeycode
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
        if (Cookie::has('ACCESS')) {
            $code = Cookie::get('ACCESS');

            $accessCodes = AccessCode::all();

            $now = now()->format('Y-m-d H:m:s');
            foreach ($accessCodes as $accessCode) {
                $date = $accessCode->expire_date;
                $valid = $date > $now || $accessCode->forever;

                if ($valid && md5($accessCode->code) === $code) {
                    return $next($request, AppConfig::getLocale());
                }
            }
        }

        return redirect()->route('access.view', [
            AppConfig::getLocale(),
        ]);
    }
}
