<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

// New By Jyl
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    /*protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }*/

    // New By Jyl
    protected function unauthenticated($request, array $guards)
    {
        abort(response()->json([
            'message' => 'Non authentifié.'
        ], Response::HTTP_UNAUTHORIZED));
    }

    protected function redirectTo(Request $request): ?string
    {
        return null;
    }

}
