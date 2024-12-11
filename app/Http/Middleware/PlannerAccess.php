<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PlannerAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'planner') {
            abort(403, 'Access denied. Only event planners can access this area.');
        }

        return $next($request);
    }
}
