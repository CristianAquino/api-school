<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenInvalidException $e) {
            $cookie = Cookie::forget('api_token');
            return response()->json(
                ['message' => 'Invalid Token, please log in again'],
                Response::HTTP_UNAUTHORIZED
            )->cookie($cookie);
        } catch (TokenExpiredException $e) {
            $cookie = Cookie::forget('api_token');
            return response()->json(
                ['message' => 'Token Expired, please log in again'],
                Response::HTTP_UNAUTHORIZED
            )->cookie($cookie);
        }

        return $next($request);
    }
}
