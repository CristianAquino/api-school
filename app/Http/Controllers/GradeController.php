<?php

namespace App\Http\Controllers;

use App\DTOs\GradeDTO;
use App\Models\Grade;
use App\Models\GradeLevel;
use App\Models\Level;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $grades = Grade::all();
        $gradesDTO = GradeDTO::fromNotRelationCollection($grades);
        return response()->json($gradesDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Level $level)
    {
        //
        $query = Grade::whereRaw('Lower(grade) = ?', strtolower($request->validated_data["grade"]));

        $exists = $query->exists();

        if ($exists) {
            $g = $query->first();
            if ($g->levels->find($level) && $g->levels->find($level)->level == $level->level) {

                return response()->json(["message" => "Grade $g->grade for level $level->level already exists, please enter another grade or modify the existing grade"], Response::HTTP_CREATED);
            }

            $level->grades()->attach($g);

            return response()->json(["message" => "Grade $request->grade has been correctly assigned to level $level->level"], Response::HTTP_CREATED);
        }

        $g = Grade::create([
            'grade' => $request->validated_data["grade"]
        ]);

        $level->grades()->attach($g);

        return response()->json(["message" => "The grade $request->grade has been successfully created"], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Level $level, Grade $grade)
    {
        //
        // $gra = $grade->levels->find($level->id);
        // $courses = Course::where('grade_level_id', $gra->pivot->id)->get();
        $courses = GradeLevel::where('level_id', $level->id)->where('grade_id', $grade->id)->first()->course;

        $relation = GradeDTO::fromModel($grade, $level, $courses);
        return response()->json($relation, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grade $grade)
    {
        //
        $g = $grade->grade;
        $grade->update($request->validated_data);

        return response()->json(["message" => "The grade $g has been successfully updated to $request->grade"], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function detach(Level $level, Grade $grade)
    {
        //
        $level->grades()->detach($grade->id);

        return response()->json(["message" => "The grade $grade->grade has been successfully deleted from level $level->level"], Response::HTTP_ACCEPTED);
    }

    public function destroy(Grade $grade)
    {
        //
        $grade->delete();
        return response()->json(["message" => "The grade $grade->grade has been successfully deleted"], Response::HTTP_ACCEPTED);
    }
}
