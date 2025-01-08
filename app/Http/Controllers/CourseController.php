<?php

namespace App\Http\Controllers;

use App\DTOs\CourseDTO;
use App\Models\Course;
use App\Models\Grade;
use App\Models\GradeLevel;
use App\Models\Level;
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
        $relation = GradeLevel::where('grade_id', $grade->id)->where('level_id', $level->id)->first();

        $course = new Course($request->validated_data);
        $course->grade_level_id = $relation->id;
        $course->save();

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
        $course->update($request->validated_data);

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
