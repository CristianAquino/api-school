<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Enrollement;
use App\Models\Grade;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class EnrollementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $enrollements = Enrollement::all();
        return response()->json($enrollements, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, AcademicYear $academicYear, Grade $grade)
    {
        // pensar en que el estudainte ingrese por su codigo de estudiante
        // al igual que el teacher
        //
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:64',
            'first_name' => 'required|string|max:32',
            'second_name' => 'required|string|max:32',
            'phone' => 'nullable|string|max:32',
            'birth_date' => 'nullable|date',
            'address' => 'required|string|max:128',
            'dni' => 'required|string|max:8|unique:users,dni,',
            'email' => 'required|email|unique:users,email,',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $i = Student::count();
        $code = 'ST' . (int)date('Y') * 10000 + $i;

        $student = Student::create([
            'code_student' => $code,
            'role' => User::ROLE_STUDENT
        ]);

        $new_datos = $validate->validated();
        $new_datos['password'] = $code . '1234';

        $student->user()->create($new_datos);

        if (is_null($student->user)) {
            $student->delete();
        }

        $enrollement = new Enrollement();
        $enrollement->student_id = $student->id;
        $enrollement->academic_year_id = $academicYear->id;
        $enrollement->grade_id = $grade->id;
        $enrollement->save();

        return response()->json(["message" => "The registration for student $request->first_name $request->second_name $request->name has been created successfully"], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollement $enrollement)
    {
        //
        $enrollement->load('student.user', 'academic_year', 'grade.level');
        return response()->json($enrollement, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enrollement $enrollement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollement $enrollement)
    {
        //
    }
}
