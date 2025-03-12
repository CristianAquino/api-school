<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('api_token');

        if (!$token) {
            return response()->json(['message' => 'Authentication required'], Response::HTTP_UNAUTHORIZED);
        }

        $request->headers->set('Authorization', 'Bearer ' . $token);
        return $next($request);
    }
}
