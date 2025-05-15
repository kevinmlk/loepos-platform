<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the token from the request headers
        $token = $request->header('Authorization');

        // Get the universal token from the .env file
        $universalToken = env('API_EMAIL_KEY');

        // Check if the tokens match
        if (!$token || $token !== $universalToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
