<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;

class ProjectAdminRight
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user->isAdmin() || $user->ownsProject($request->project_id)) {
            return $next($request);
        }

        return ResponseHelper::unAuthorised("You do not own this project");
    }
}
