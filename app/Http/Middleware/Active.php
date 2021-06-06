<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ResponseHelper;

class Active
{
    private $message = "Kindly Complete your profile to have full access - Apply to Tasks, Post Tasks...";
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! Auth::user()->isActive()) 
        {
            return ResponseHelper::unAuthorised();
        }
        return $next($request);    }
}
