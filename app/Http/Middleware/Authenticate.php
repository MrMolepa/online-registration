<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    // protected function redirectTo($request)
    // {
    //     if (! $request->expectsJson()) {
    //         return route('login');
    //     }


    // }


    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {

            if($request->routeIs('admin.*')){
                return route('admin.login');
            }
            if($request->routeIs('center.*')){
                return route('center.login');
            }
            if($request->routeIs('candidate.*')) {
                return route('candidate.login');
            }
            if($request->routeIs('sponsor.*')) {
                return route('sponsor.login');
            }
            return route('home');

        }
    }



}
