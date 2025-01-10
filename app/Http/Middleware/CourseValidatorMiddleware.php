<?php

namespace App\Http\Middleware;

use App\Models\Schedule;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CourseValidatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rules = [
            'course' => 'required|string|max:64',
            'description' => 'sometimes|string|max:128',
            'schedule_id' => 'sometimes|integer|exists:schedules,id',
            'day' => [
                'sometimes',
                'string',
                function ($attribute, $value, $fail) {
                    if (!in_array(strtolower($value), Schedule::DAYS)) {
                        $fail('The day must be a valid day of the week.');
                    }
                },
            ],
        ];


        if ($request->isMethod('put') || $request->isMethod('patch')) {
            $rules['grade_level_id'] = 'sometimes|integer|exists:grade_level,id';
        }

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // $request->merge(['validated_data' => $validate->validated()]);

        return $next($request);
    }
}
