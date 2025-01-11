<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Qualification;
use App\Models\Student;
use Illuminate\Http\Request;
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

        $qualification = new Qualification($new_datos);
        $qualification->student_id = $student->id;
        $qualification->course_id = $course->id;
        $qualification->save();

        return response()->json(["message" => "Qualification $request->number_note has been successfully recorded for student $student->code_student in course $course->course"], Response::HTTP_CREATED);
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
