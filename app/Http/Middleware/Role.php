<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Get the currently authenticated user
        $user = $request->user();
        if (!$user || $user->role != $role) {
            return redirect()->route('/')->with('error', 'Unauthorized access.');; // Redirect to login if unauthorized
        }
        
        // If the user has the correct role, allow the request to continue
        return $next($request);
    }
}
