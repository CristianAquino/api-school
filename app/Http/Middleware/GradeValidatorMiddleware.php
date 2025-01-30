<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class GradeValidatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rules = [
            'grade' => ['required', 'string', 'max:8'],
        ];

        if ($request->isMethod('put') || $request->isMethod('patch')) {
            $grade = $request->route('grade');
            $rules['grade'] = 'unique:grades,grade,' . $grade->id;
            $rules['level_id'] = 'sometimes|integer|exists:levels,id';
        }

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $request->merge(['validated_data' => $validate->validated()]);

        return $next($request);
    }
}
