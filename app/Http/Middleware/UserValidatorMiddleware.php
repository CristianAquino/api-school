<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserValidatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rules = [
            'name' => 'required|string|max:64',
            'first_name' => 'required|string|max:32',
            'second_name' => 'required|string|max:32',
            'phone' => 'sometimes|string|max:32',
            'birth_date' => 'sometimes|date',
            'address' => 'required|string|max:128',
            'dni' => ['required', 'string', 'max:8'],
            'email' => ['required', 'email'],
        ];

        if ($request->isMethod('post')) {
            $rules['dni'][] = 'unique:users,dni';
            $rules['email'][] = 'unique:users,email';
        } elseif ($request->isMethod('put') || $request->isMethod('patch')) {
            $user = $request->route('admin') ??
                ($request->route('teacher') ?? $request->route('student'));
            $userId = $user->user ? $user->user->id : null;
            $rules['dni'][] = 'unique:users,dni,' . $userId;
            $rules['email'][] = 'unique:users,email,' . $userId;
        }

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $request->merge(['validated_data' => $validate->validated()]);
        return $next($request);
    }
}
