<?php

namespace App\Http\Controllers;

use App\DTOs\StudentDTO;
use App\Models\AcademicYear;
use App\Models\GradeLevel;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // pensar si poner level y grade
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
        // $validate = Validator::make($request->all(), [
        //     'name' => 'required|string|max:64',
        //     'first_name' => 'required|string|max:32',
        //     'second_name' => 'required|string|max:32',
        //     'phone' => 'required|string|max:32',
        //     'birth_date' => 'nullable|date',
        //     'address' => 'required|string|max:128',
        //     'dni' => 'required|string|max:8|unique:users,dni,' . $student->user->id,
        //     'email' => 'required|email|unique:users,email,' . $student->user->id,
        // ]);

        // if ($validate->fails()) {
        //     return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        // }

        $student->user()->update($request->validated_data);

        return response()->json(["message" => "The student with code $student->code_student has been successfully updated"], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //
    }
}