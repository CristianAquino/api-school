<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ScheduleValidatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rules = [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    $startTime = strtotime($request->start_time);
                    $endTime = strtotime($value);
                    $difference = ($endTime - $startTime) / 60;

                    if ($endTime <= $startTime) {
                        $fail('The end time must be after the start time.');
                    }
                    if ($difference < 30) {
                        $fail('The difference between start time and end time must be at least 30 minutes.');
                    }
                }
            ],
        ];

        if ($request->isMethod('post')) {
            $rules['start_time'][] = 'unique:schedules,start_time';
        } elseif ($request->isMethod('put') || $request->isMethod('patch')) {
            $schedule = $request->route('schedule');
            $rules['start_time'][] = 'unique:schedules,start_time,' . $schedule->id;
        }

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $request->merge(["validated_data" => $validate->validated()]);

        return $next($request);
    }
}
