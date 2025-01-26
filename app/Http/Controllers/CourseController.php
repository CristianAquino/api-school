<?php

namespace App\Http\Controllers;

use App\DTOs\CourseDTO;
use App\Models\Course;
use App\Models\Grade;
use App\Models\GradeLevel;
use App\Models\Level;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $courses = Course::all();
        $coursesDTO = CourseDTO::fromCollection($courses);
        return response()->json($coursesDTO, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource remove soft.
     */
    public function softList()
    {
        //
        $response = Gate::inspect('softList', Course::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $deletedCourses = Course::onlyTrashed()->get();
        $deletedCoursesDTO = CourseDTO::fromCollection($deletedCourses);
        return response()->json($deletedCoursesDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Level $level, Grade $grade)
    {
        //
        $gradeLevel = GradeLevel::where('level_id', $level->id)->where('grade_id', $grade->id)->first();

        $request->validated_data->only(["course", "description"]);

        $course = new Course($request->only(["course", "description"]));
        $course->grade_level_id = $gradeLevel->id;
        $course->save();

        if (!is_null($request->schedule_id)) {
            $schedule = Schedule::where("id", $request->schedule_id)->first();
            $course->schedules()->attach(
                $schedule,
                ["day" => $request->day,]
            );
        }

        return response()->json([
            "message" => "The course $request->course has been successfully created"
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        //
        $coursesDTO = CourseDTO::fromModelWithRelation($course);
        return response()->json($coursesDTO, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        //
        $response = Gate::inspect('update', Course::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $course->update($request->only([
            'course',
            'description',
            'grade_level_id'
        ]));

        if (!is_null($request->schedule_id)) {
            if (is_null($request->day)) {
                return response()->json(["message" => "The day not exist"], Response::HTTP_BAD_REQUEST);
            }
            $schedule = Schedule::where("id", $request->schedule_id)->first();

            $course->schedules()->detach();
            $course->schedules()->attach($schedule, [
                "day" => strtolower($request->day),
            ]);
        }

        return response()->json([
            "message" => "The course $course->course has been successfully updated"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove soft the specified resource from storage.
     */
    public function softDestroy(Course $course)
    {
        //
        $response = Gate::inspect('softDestroy', Course::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $course->delete();
        return response()->json([
            "message" => "the course $course->course has been successfully deleted"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        //
        $response = Gate::inspect('restore', Course::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $course = Course::onlyTrashed()->find($id);

        if (is_null($course)) {
            return response()->json([
                "message" => "the course does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $course->restore();
        return response()->json([
            "message" => "the course $course->course has been successfully restored"
        ], Response::HTTP_OK);
    }

    /**
     * Remove permanently the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $response = Gate::inspect('destroy', Course::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $course = Course::onlyTrashed()->find($id);

        if (is_null($course)) {
            return response()->json([
                "message" => "the course does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $course->forceDelete();
        return response()->json([
            "message" => "the course $course->course has been successfully deleted permanently"
        ], Response::HTTP_ACCEPTED);
    }
}
