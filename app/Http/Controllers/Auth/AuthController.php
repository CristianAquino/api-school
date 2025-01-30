<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {

        try {
            //code...
            $credentials = $request->validated_data;
            // $credentials = $validated->only(['code', 'password']);;

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(
                    ['error' => 'Invalid credentials'],
                    Response::HTTP_UNAUTHORIZED
                );
            }

            $cookie = cookie('api_token', $token, 60 * 24);

            return response()->json([
                'token' => $token,
                'user' => Auth::user(),
            ], Response::HTTP_OK)->cookie($cookie);
        } catch (JWTException $e) {
            //throw $th;
            return response()->json(
                ['error' => 'Could not create token'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function logout()
    {
        Auth::logout();
        $cookie = Cookie::forget('api_token');
        return response()->json(['message' => 'Successfully logged out'])->cookie($cookie);
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh(true, true));
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
}
