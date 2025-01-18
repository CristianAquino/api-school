<?php

namespace App\Http\Controllers;

use App\DTOs\ScheduleDTO;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $schedules = Schedule::all();
        $schedulesDTO = ScheduleDTO::fromCollection($schedules);
        return response()->json($schedulesDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        // 'day'=>'required|string|in:' . implode(',', Schedule::DAYS),
        Schedule::create($request->validated_data);

        return response()->json(["message" => "Schedule created successfully"], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
        $courses = $schedule->courses;
        $scheduleDTO = ScheduleDTO::fromCollectionWithRelation($schedule, $courses);
        return response()->json($scheduleDTO, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        //
        $schedule->update($request->validated_data);

        return response()->json(["message" => "Schedule has been successfully updated"], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        //
        // $course->schedules()->find($schedule->id)->delete();

        $schedule->delete();

        return response()->json(["message" => "Schedule has been successfully deleted"], Response::HTTP_ACCEPTED);
    }
}
