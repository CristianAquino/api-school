<?php

namespace App\Http\Controllers;

use App\DTOs\TeacherDTO;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;


class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $code = strtolower($request->query('code'));
        $teachers = Teacher::query()
            ->when($code, function ($query) use ($code) {
                $query->whereHas('user', function ($query) use ($code) {
                    $query->whereRaw('LOWER(code) LIKE ?', "%$code%");
                });
            })
            ->paginate(10);

        $teachersDTO = TeacherDTO::fromPagination($teachers);
        return response()->json($teachersDTO, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource remove soft.
     */
    public function softList(Request $request)
    {
        //
        $response = Gate::inspect('softList', Teacher::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $code = strtolower($request->query('code'));
        $deletedTeachers = Teacher::onlyTrashed()
            ->when($code, function ($query) use ($code) {
                $query->whereHas('user', function ($query) use ($code) {
                    $query->whereRaw('LOWER(code) LIKE ?', "%$code%");
                });
            })
            ->paginate(10);

        $deletedTeachersDTO = TeacherDTO::fromPagination($deletedTeachers);
        return response()->json($deletedTeachersDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $response = Gate::inspect('store', Teacher::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $i = Teacher::count();

        if ($i == 0) {
            $code = 'TE' . (int)date('Y') * 10000 + $i;
        } else {
            $c = User::where('userable_type', Teacher::class)
                ->latest("id")
                ->first()->code;
            $i = (int)substr($c, 2) + 1;
            $code = 'TE' . $i;
        }

        $teacher = Teacher::create([
            'role' => User::ROLE_TEACHER
        ]);

        $new_datos = $request->validated_data;
        $new_datos['code'] = $code;
        $new_datos['password'] = $code . $new_datos['dni'];

        $teacher->user()->create($new_datos);

        if (is_null($teacher->user)) {
            $teacher->delete();
            return response()->json([
                "message" => "The registration for teacher $request->first_name $request->second_name $request->name has not been created successfully"
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            "message" => "teacher $request->first_name $request->second_name $request->name has been added successfully with code $code"
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function me()
    {
        //
        $me = Auth::user()->userable_id;
        $user = Teacher::where('id', $me)->first();

        if (is_null($user)) {
            return response()->json([
                "message" => "You do not have the role allowed to perform this action"
            ], Response::HTTP_NOT_FOUND);
        }

        $teacherDTO = TeacherDTO::fromModelWithRelation($user);
        return response()->json($teacherDTO, Response::HTTP_OK);
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
        $response = Gate::inspect('assign', Teacher::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $course->teacher_id = $teacher->id;
        $course->save();

        return response()->json([
            "message" => "Professor " . $teacher->user->first_name . " " . $teacher->user->second_name . " has been successfully assigned course $course->course"
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        //
        $response = Gate::inspect('update', Teacher::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        if (is_null($teacher->user)) {
            $c = User::where('userable_type', Teacher::class)
                ->latest("id")
                ->first()->code;
            $i = (int)substr($c, 2) + 1;
            $code = 'TE' . $i;

            $new_datos = $request->validated_data;
            $new_datos['password'] = $code . $new_datos['dni'];
            $teacher->user()->create($new_datos);
        } else {
            $teacher->user()->update($request->validated_data);
        }

        return response()->json([
            "message" => "The teacher with code " . $teacher->user->code . " has been successfully updated"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove soft the specified resource from storage.
     */
    public function softDestroy(Teacher $teacher)
    {
        //
        $response = Gate::inspect('softDestroy', Teacher::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $teacher->delete();
        return response()->json([
            "message" => "the teacher with code " . $teacher->user->code . " has been successfully deleted"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        //
        $response = Gate::inspect('restore', Teacher::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $teacher = Teacher::onlyTrashed()->find($id);

        if (is_null($teacher)) {
            return response()->json([
                "message" => "the academic year does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $teacher->restore();
        return response()->json([
            "message" => "the teacher with code " . $teacher->user->code . " has been successfully restored"
        ], Response::HTTP_OK);
    }

    /**
     * Remove permanently the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $response = Gate::inspect('destroy', Teacher::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $teacher = Teacher::onlyTrashed()->find($id);
        if (is_null($teacher)) {
            return response()->json([
                "message" => "the teacher does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $code = $teacher->user->code;

        foreach ($teacher->courses as $course) {
            $course->teacher_id = null;
            $course->save();
        }

        $teacher->forceDelete();
        User::where('userable_id', $teacher->id)->delete();
        return response()->json([
            "message" => "the teacher with code $code has been successfully deleted permanently"
        ], Response::HTTP_ACCEPTED);
    }
}
