<?php

namespace App\Http\Controllers;

use App\DTOs\CourseDTO;
use App\Models\Course;
use App\Models\Grade;
use App\Models\GradeLevel;
use App\Models\Level;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $course = $request->query('course');
        $courses = Course::query()
            ->when(
                $course,
                fn($query, $course) => $query->where('course', 'like',  "%$course%")
            )
            ->paginate(10);

        $coursesDTO = CourseDTO::fromPagination($courses);
        return response()->json($coursesDTO, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource remove soft.
     */
    public function softList(Request $request)
    {
        //
        $response = Gate::inspect('softList', Course::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $course = $request->query('course');

        $deletedCourses = Course::onlyTrashed()
            ->when(
                $course,
                fn($query, $course) => $query->where('course', 'like',  "%$course%")
            )
            ->paginate(10);

        $deletedCoursesDTO = CourseDTO::fromPagination($deletedCourses);
        return response()->json($deletedCoursesDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Level $level, Grade $grade)
    {
        //
        $response = Gate::inspect('store', Course::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $gradeLevel = GradeLevel::where('level_id', $level->id)
            ->where('grade_id', $grade->id)
            ->first();

        $course = new Course($request->only(["course", "description"]));
        $course->grade_level_id = $gradeLevel->id;
        $course->save();

        if (!is_null($request->schedule_id)) {
            if (is_null($request->day)) {
                return response()->json(["message" => "The day not exist"], Response::HTTP_BAD_REQUEST);
            }
            $schedule = Schedule::where("id", $request->schedule_id)->first();
            $course->schedules()->attach(
                $schedule,
                ["day" => $request->day,]
            );
        }

        return response()->json([
            "message" => "The course $request->course has been successfully created"
        ], Response::HTTP_CREATED);
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
        $response = Gate::inspect('update', Course::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        if (is_null($request->level_id) && is_null($request->grade_id)) {
            $course->update($request->only(["course", "description"]));
        } else {
            $gradeLevel = GradeLevel::where('level_id', $request->level_id)
                ->where('grade_id', $request->grade_id)
                ->first();

            if (is_null($gradeLevel)) {
                return response()->json(["message" => "The grade level or grade does not exist"], Response::HTTP_BAD_REQUEST);
            }

            $course->update(
                [
                    'course' => $request->course,
                    'description' => $request->description ?? $course->description,
                    'grade_level_id' => $gradeLevel->id
                ]
            );
        }

        if (!is_null($request->schedule_id)) {
            if (is_null($request->day)) {
                return response()->json(["message" => "The day not exist"], Response::HTTP_BAD_REQUEST);
            }
            $schedule = Schedule::where("id", $request->schedule_id)->first();

            $course->schedules()->detach();
            $course->schedules()->attach($schedule, [
                "day" => strtolower($request->day),
            ]);
        }

        return response()->json([
            "message" => "The course $course->course has been successfully updated"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove soft the specified resource from storage.
     */
    public function softDestroy(Course $course)
    {
        //
        $response = Gate::inspect('softDestroy', Course::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $course->delete();
        return response()->json([
            "message" => "the course $course->course has been successfully deleted"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        //
        $response = Gate::inspect('restore', Course::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $course = Course::onlyTrashed()->find($id);

        if (is_null($course)) {
            return response()->json([
                "message" => "the course does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $course->restore();
        return response()->json([
            "message" => "the course $course->course has been successfully restored"
        ], Response::HTTP_OK);
    }

    /**
     * Remove permanently the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $response = Gate::inspect('destroy', Course::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $course = Course::onlyTrashed()->find($id);

        if (is_null($course)) {
            return response()->json([
                "message" => "the course does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $course->forceDelete();
        return response()->json([
            "message" => "the course $course->course has been successfully deleted permanently"
        ], Response::HTTP_ACCEPTED);
    }
}
