<?php
namespace App\Http\Middleware;

use Closure;

class LocalizationMiddleware
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
        // Check header request and determine localizaton
        //$local = 'en';//($request->hasHeader(‘X-localization’)) ? $request->header(‘X-localization’) : ‘en’;//設定本地語言

        // set laravel localization
        //app('translator')->setLocale($local);

        // continue request
        return $next($request);
    }
}
