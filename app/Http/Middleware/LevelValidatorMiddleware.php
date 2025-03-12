<?php

namespace App\Http\Middleware;

use App\Models\Level;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class LevelValidatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rules = [
            'level' => [
                'required',
                'string',
                'max:64',
                function ($attribute, $value, $fail) {
                    $exists = Level::whereRaw('LOWER(level) = ?', strtolower($value))->exists();
                    if ($exists) {
                        $fail("The level $value already exists.");
                    }
                }
            ],
        ];

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $request->merge(['validated_data' => $validate->validated()]);

        return $next($request);
    }
}
