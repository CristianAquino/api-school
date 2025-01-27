<?php

namespace App\Http\Controllers;

use App\DTOs\StudentDTO;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $students = Student::all();
        $studentsDTO = StudentDTO::fromCollection($students);
        return response()->json($studentsDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
        $response = Gate::inspect('view', $student);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }
        // $studentsDTO = $student->enrollements()->latest("academic_year_id")->first(); //->where("academic_year_id", $a->id)->first();
        // $a = $studentsDTO->grade_level_id;
        // $g = GradeLevel::find($a)->courses[0]->schedules;
        $studentDTO = StudentDTO::fromModelWithRelation($student);
        return response()->json($studentDTO, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        //
        $response = Gate::inspect('update', Student::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $student->user()->update($request->validated_data);

        return response()->json(["message" => "The student with code " . $student->user->code . " has been successfully updated"], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove soft the specified resource from storage.
     */
    public function softDestroy(Student $student)
    {
        //
        $response = Gate::inspect('softDestroy', Student::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $student->delete();
        return response()->json([
            "message" => "the student " . $student->first_name . " " . $student->second_name . " " . $student->name . " has been successfully deleted"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        //
        $response = Gate::inspect('restore', Student::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $student = Student::onlyTrashed()->find($id);

        if (is_null($student)) {
            return response()->json([
                "message" => "the student year does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $student->restore();
        return response()->json([
            "message" => "the student " . $student->first_name . " " . $student->second_name . " " . $student->name . " has been successfully restored"
        ], Response::HTTP_OK);
    }

    /**
     * Remove permanently the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $response = Gate::inspect('destroy', Student::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $student = Student::onlyTrashed()->find($id);

        if (is_null($student)) {
            return response()->json([
                "message" => "the student does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $student->forceDelete();
        return response()->json([
            "message" => "the student " . $student->first_name . " " . $student->second_name . " " . $student->name . " has been successfully deleted permanently"
        ], Response::HTTP_ACCEPTED);
    }
}
