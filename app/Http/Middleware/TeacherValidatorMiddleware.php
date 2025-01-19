<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TeacherValidatorMiddleware
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
            'phone' => 'required|string|max:32',
            'birth_date' => 'sometimes|date',
            'address' => 'required|string|max:128',
            'dni' => 'required|string|max:8|unique:users,dni',
            'email' => 'required|email|unique:users,email',
        ];

        if ($request->isMethod('put') || $request->isMethod('patch')) {
            $teacher = $request->route('teacher');
            $userId = $teacher->user ? $teacher->user->id : null;
            $rules['dni'] = 'required|string|max:8|unique:users,dni,' . $userId;
            $rules['email'] = 'required|email|unique:users,email,' . $userId;
        }

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $request->merge(['validated_data' => $validate->validated()]);
        return $next($request);
    }
}
