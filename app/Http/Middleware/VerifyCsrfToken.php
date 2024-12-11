<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\RedirectResponse;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    protected function addCookieToResponse($request, $response)
    {
        $response = parent::addCookieToResponse($request, $response);
        
        if ($response instanceof RedirectResponse && $request->ajax()) {
            return $response->withHeaders([
                'X-CSRF-TOKEN' => csrf_token(),
            ]);
        }

        return $response;
    }
} 