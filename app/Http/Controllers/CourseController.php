<?php

namespace App\Http\Controllers;

use App\DTOs\CourseDTO;
use App\Models\Course;
use App\Models\Grade;
use App\Models\GradeLevel;
use App\Models\Level;
use App\Models\Schedule;
use Illuminate\Http\Request;
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Level $level, Grade $grade)
    {
        //
        $gradeLevel = GradeLevel::where('grade_id', $grade->id)->where('level_id', $level->id)->first();

        $course = new Course($request->only(["course", "description"]));
        $course->grade_level_id = $gradeLevel->id;
        $course->save();

        if ($request->schedule_id ?? null) {
            $schedule = Schedule::where("id", $request->schedule_id)->first();
            $course->schedules()->attach($schedule, [
                "day" => $request->day,
            ]);
        }

        return response()->json(["message" => "The course $request->course has been successfully created"], Response::HTTP_CREATED);
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
        $course->update($request->only(['course', 'description', 'grade_level_id']));

        if ($request->schedule_id ?? null) {
            if ($request->day ?? null) {
                $schedule = Schedule::where("id", $request->schedule_id)->first();

                $course->schedules()->detach();
                $course->schedules()->attach($schedule, [
                    "day" => strtolower($request->day),
                ]);
            } else {
                return response()->json(["message" => "The day not exist"], Response::HTTP_BAD_REQUEST);
            }
        }

        return response()->json(["message" => "The course $course->course has been successfully updated"], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
        // $grade->courses()->find($course->id)->delete();
        $course->delete();
        return response()->json(["message" => "The course $course->course has been successfully deleted"], Response::HTTP_ACCEPTED);
    }
}
