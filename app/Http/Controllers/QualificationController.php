<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollement;
use App\Models\Qualification;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class QualificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Student $student, Course $course)
    {
        //
        $response = Gate::inspect('create', Qualification::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $enrollement = Enrollement::where('student_id', $student->id)
            ->latest()
            ->first();
        $cou = Course::where('id', $course->id)->first();

        if (is_null($enrollement) && is_null($cou)) {
            return response()->json(["message" => "The student or course not found"], Response::HTTP_FORBIDDEN);
        } elseif ($enrollement->grade_level_id != $cou->grade_level_id) {
            return response()->json(["message" => "The student is not enrolled in the course"], Response::HTTP_FORBIDDEN);
        } else {
            $me = Auth::user()->userable_id;
            $user = Teacher::where('id', $me)->first();
            if ($cou->teacher_id != $user->id) {
                return response()->json(["message" => "The professor does not teach this course"], Response::HTTP_FORBIDDEN);
            }
        }

        // dd($user->id);

        $prom = (int)config('app.avg_note');

        $new_datos = $request->validated_data;

        if (is_null($request->letter_note)) {
            $correspondencias = [
                'AD' => [18, 20],
                'A' => [16, 17],
                'B' => [10, 15],
                'C' => [0, 9],
            ];
            foreach ($correspondencias as $letter => [$min, $max]) {
                if ($new_datos['number_note'] >= $min && $new_datos['number_note'] <= $max) {
                    $new_datos['letter_note'] = $letter;
                    break;
                }
            }
        }

        $new_datos['letter_note'] = strtoupper($new_datos['letter_note']);
        $new_datos['avg'] = ($new_datos['number_note'] / $prom);
        dd($new_datos);

        $qualification = new Qualification($new_datos);
        $qualification->student_id = $student->id;
        $qualification->course_id = $course->id;
        $qualification->save();

        return response()->json(["message" => "Qualification $request->number_note has been successfully recorded for student $student->code in course $course->course"], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student, Course $course)
    {
        //
        $qualification = Qualification::where('student_id', $student->id)->where('course_id', $course->id)->get();
        if ($qualification) {
            return response()->json($qualification, Response::HTTP_OK);
        } else {
            return response()->json(["message" => "Qualification not found"], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Qualification $qualification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Qualification $qualification)
    {
        //
    }
}
