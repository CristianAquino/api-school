<?php

namespace App\Http\Controllers;

use App\DTOs\ScheduleDTO;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
     * Display a listing of the resource remove soft.
     */
    public function softList()
    {
        //
        $response = Gate::inspect('softList', Schedule::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $deletedSchedules = Schedule::onlyTrashed()->paginate(10);
        $deletedSchedulesDTO = ScheduleDTO::fromPagination($deletedSchedules);
        return response()->json($deletedSchedulesDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        // 'day'=>'required|string|in:' . implode(',', Schedule::DAYS),
        $response = Gate::inspect('store', Schedule::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        Schedule::create($request->validated_data);
        return response()->json([
            "message" => "Schedule created successfully"
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
        $response = Gate::inspect('view', Schedule::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $scheduleDTO = ScheduleDTO::fromCollectionWithRelation($schedule);
        return response()->json($scheduleDTO, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        //
        $response = Gate::inspect('update', Schedule::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $schedule->update($request->validated_data);
        return response()->json([
            "message" => "Schedule has been successfully updated"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove soft the specified resource from storage.
     */
    public function softDestroy(Schedule $schedule)
    {
        //
        $response = Gate::inspect('softDestroy', Schedule::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $schedule->delete();
        return response()->json([
            "message" => "the schedule has been successfully deleted"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        //
        $response = Gate::inspect('restore', Schedule::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $schedule = Schedule::onlyTrashed()->find($id);

        if (is_null($schedule)) {
            return response()->json([
                "message" => "the schedule does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $schedule->restore();
        return response()->json([
            "message" => "the schedule has been successfully restored"
        ], Response::HTTP_OK);
    }

    /**
     * Remove permanently the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $response = Gate::inspect('destroy', Schedule::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $schedule = Schedule::onlyTrashed()->find($id);

        if (is_null($schedule)) {
            return response()->json([
                "message" => "the schedule does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $schedule->forceDelete();
        return response()->json([
            "message" => "the scheduler has been successfully deleted permanently"
        ], Response::HTTP_ACCEPTED);
    }
}
