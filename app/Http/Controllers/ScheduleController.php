<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        //
        $schedules = $course->schedules;
        return response()->json($schedules, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course)
    {
        //
        $validate = Validator::make($request->all(), [
            // 'day'=>'required|string|in:' . implode(',', Schedule::DAYS),
            'day' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!in_array(strtolower($value), Schedule::DAYS)) {
                        $fail('The day must be a valid day of the week.');
                    }
                },
            ],
            'start_time' => 'required|date_format:H:i',
            'end_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    if (strtotime($value) <= strtotime($request->start_time)) {
                        $fail('The end time must be after the start time.');
                    }
                }
            ],
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $val = $validate->validated();
        $val['day'] = strtolower($val['day']);

        $course->schedules()->create($val);

        return response()->json(["message" => "Schedule created successfully for course $course->course"], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course, Schedule $schedule)
    {
        //
        $schedule = $course->schedules()->find($schedule->id);
        return response()->json($schedule, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course, Schedule $schedule)
    {
        //
        $validate = Validator::make($request->all(), [
            'day' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!in_array(strtolower($value), Schedule::DAYS)) {
                        $fail('The day must be a valid day of the week.');
                    }
                },
            ],
            'start_time' => 'required|date_format:H:i',
            'end_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    if (strtotime($value) <= strtotime($request->start_time)) {
                        $fail('The end time must be after the start time.');
                    }
                }
            ],
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $val = $validate->validated();
        $val['day'] = strtolower($val['day']);

        $course->schedules()->find($schedule->id)->update($val);

        return response()->json(["message" => "Schedule for course $course->course has been successfully updated"], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Schedule $schedule)
    {
        //
        $course->schedules()->find($schedule->id)->delete();

        return response()->json(["message" => "Schedule for course $course->course has been successfully deleted"], Response::HTTP_ACCEPTED);
    }
}
