<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Grade $grade)
    {
        //
        $courses = $grade->courses;
        return response()->json($courses, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Grade $grade)
    {
        //
        $validate = Validator::make($request->all(), [
            'course' => 'required|string|max:64',
            'description' => 'nullable|string|max:128'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $grade->courses()->create($request->only(['course', 'description']));

        return response()->json(["message" => "The course $request->course has been successfully created"], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Grade $grade, Course $course)
    {
        //
        $course = $grade->courses()->find($course->id);
        return response()->json($course, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grade $grade, Course $course)
    {
        //
        $validate = Validator::make($request->all(), [
            'course' => 'required|string|max:64',
            'description' => 'nullable|string|max:128'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $grade->courses()->find($course->id)->update($request->only(['course', 'description']));

        return response()->json(["message" => "The course $course->course has been successfully updated"], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grade $grade, Course $course)
    {
        //
        $grade->courses()->find($course->id)->delete();

        return response()->json(["message" => "The course $course->course has been successfully deleted from grade $grade->grade"], Response::HTTP_ACCEPTED);
    }
}
