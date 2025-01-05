<?php

namespace App\Http\Controllers;

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
        $students = Student::with('user')->get();
        return response()->json($students, Response::HTTP_OK);
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
        $student->load('user');
        return response()->json($student, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        //
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:64',
            'first_name' => 'required|string|max:32',
            'second_name' => 'required|string|max:32',
            'phone' => 'required|string|max:32',
            'birth_date' => 'nullable|date',
            'address' => 'required|string|max:128',
            'dni' => 'required|string|max:8|unique:users,dni,' . $student->user->id,
            'email' => 'required|email|unique:users,email,' . $student->user->id,
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $student->user()->update($validate->validated());

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
