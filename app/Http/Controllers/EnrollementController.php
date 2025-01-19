<?php

namespace App\Http\Controllers;

use App\DTOs\EnrollementDTO;
use App\Models\AcademicYear;
use App\Models\Enrollement;
use App\Models\GradeLevel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
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
        $enrollementsDTO = EnrollementDTO::fromCollection($enrollements);
        return response()->json($enrollementsDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, AcademicYear $academicYear, GradeLevel $gradeLevel)
    {
        // pensar en que el estudainte ingrese por su codigo de estudiante
        // al igual que el teacher
        //
        // $validate = Validator::make($request->all(), [
        //     'name' => 'required|string|max:64',
        //     'first_name' => 'required|string|max:32',
        //     'second_name' => 'required|string|max:32',
        //     'phone' => 'nullable|string|max:32',
        //     'birth_date' => 'nullable|date',
        //     'address' => 'required|string|max:128',
        //     'dni' => 'required|string|max:8|unique:users,dni,',
        //     'email' => 'required|email|unique:users,email,',
        // ]);

        // if ($validate->fails()) {
        //     return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        // }

        // $i = Student::count();
        // $code = 'ST' . (int)date('Y') * 10000 + $i;
        // $student = Student::create([
        //     'code_student' => $code,
        //     'role' => User::ROLE_STUDENT
        // ]);

        $i = Student::count();

        if ($i == 0) {
            $code = 'ST' . (int)date('Y') * 10000 + $i;
        } else {
            $c = Student::latest("code_student")->first()->code_student;
            $i = (int)substr($c, 2) + 1;
            $code = 'ST' . $i;
        }

        $student = Student::create([
            'code_student' => $code,
            'role' => User::ROLE_STUDENT
        ]);

        $new_datos = $request->validated_data;
        $new_datos['password'] = $code . $new_datos['dni'];

        $student->user()->create($new_datos);

        if (is_null($student->user)) {
            $student->delete();
            return response()->json(["message" => "The registration for student $request->first_name $request->second_name $request->name has not been created successfully"], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $enrollement = new Enrollement();
        $enrollement->student_id = $student->id;
        $enrollement->academic_year_id = $academicYear->id;
        $enrollement->grade_level_id = $gradeLevel->id;
        $enrollement->save();

        return response()->json(["message" => "The registration for student $request->first_name $request->second_name $request->name has been created successfully"], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollement $enrollement)
    {
        //
        $enrollementDTO = EnrollementDTO::fromModel($enrollement);
        return response()->json($enrollementDTO, Response::HTTP_OK);
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
        $enrollement->delete();
        return response()->json(["message" => "The enrollement has been deleted successfully"], Response::HTTP_ACCEPTED);
    }
}
