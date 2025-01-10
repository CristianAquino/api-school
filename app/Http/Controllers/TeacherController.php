<?php

namespace App\Http\Controllers;

use App\DTOs\TeacherDTO;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

use function PHPUnit\Framework\isNull;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $teachers = Teacher::all();
        $teachersDTO = TeacherDTO::fromCollection($teachers);
        return response()->json($teachersDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        // $validate = Validator::make($request->all(), [
        //     'name' => 'required|string|max:64',
        //     'first_name' => 'required|string|max:32',
        //     'second_name' => 'required|string|max:32',
        //     'phone' => 'required|string|max:32',
        //     'birth_date' => 'nullable|date',
        //     'address' => 'required|string|max:128',
        //     'dni' => 'required|string|max:8|unique:users,dni',
        //     'email' => 'required|email|unique:users,email',
        // ]);

        // if ($validate->fails()) {
        //     return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        // }

        $i = Teacher::count();

        if ($i == 0) {
            $code = 'TE' . (int)date('Y') * 10000 + $i;
        } else {
            $c = Teacher::latest("code_teacher")->first()->code_teacher;
            $i = (int)substr($c, 2) + 1;
            $code = 'TE' . $i;
        }

        $teacher = Teacher::create([
            'code_teacher' => $code,
            'role' => User::ROLE_TEACHER
        ]);

        $new_datos = $request->validated_data;
        $new_datos['password'] = $code . '1234';

        $teacher->user()->create($new_datos);

        if (is_null($teacher->user)) {
            $teacher->delete();
        }

        return response()->json(["message" => "teacher $request->fisrt_name $request->second_name $request->name has been added successfully with code $teacher->code_teacher"], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
        $teacherDTO = TeacherDTO::fromModelWithRelation($teacher);
        return response()->json($teacherDTO, Response::HTTP_OK);
    }

    public function assignCourse(Teacher $teacher, Course $course)
    {
        $course->teacher_id = $teacher->id;
        $course->save();

        return response()->json(["message" => "Professor" . $teacher->user->first_name . $teacher->user->second_name . "has been successfully assigned course $course->course"], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        //

        if (is_null($teacher->user)) {
            $new_datos = $request->validated_data;
            $new_datos['password'] = $request->validated_data['code_teacher'] . '1234';
            $teacher->user()->create($new_datos);
        } else {
            $teacher->user()->update($request->validated_data);
        }

        return response()->json(["message" => "The teacher with code $teacher->code_teacher has been successfully updated"], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        //
        $teacher->user()->delete();

        return response()->json(["message" => "The teacher with code $teacher->code_teacher has been successfully deleted"], Response::HTTP_ACCEPTED);
    }
}
