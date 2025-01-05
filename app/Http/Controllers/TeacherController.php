<?php

namespace App\Http\Controllers;

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
        $teachers = Teacher::with('user')->get();
        return response()->json($teachers, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:64',
            'first_name' => 'required|string|max:32',
            'second_name' => 'required|string|max:32',
            'phone' => 'required|string|max:32',
            'birth_date' => 'nullable|date',
            'address' => 'required|string|max:128',
            'dni' => 'required|string|max:8|unique:users,dni',
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $i = Teacher::count();
        $code = 'TE' . (int)date('Y') * 10000 + $i;

        $teacher = Teacher::create([
            'code_teacher' => $code,
            'role' => User::ROLE_TEACHER
        ]);

        $new_datos = $validate->validated();
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
        $teacher->load('user');
        return response()->json($teacher, Response::HTTP_OK);
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
        $userId = $teacher->user ? $teacher->user->id : null;

        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:64',
            'first_name' => 'required|string|max:32',
            'second_name' => 'required|string|max:32',
            'phone' => 'required|string|max:32',
            'birth_date' => 'nullable|date',
            'address' => 'required|string|max:128',
            'dni' => 'required|string|max:8|unique:users,dni,' . $userId,
            'email' => 'required|email|unique:users,email,' . $userId,
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (is_null($teacher->user)) {
            $new_datos = $validate->validated();
            $new_datos['password'] = $validate->validated()['first_name'] . '1234';
            $teacher->user()->create($new_datos);
        } else {
            $teacher->user()->update($validate->validated());
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
