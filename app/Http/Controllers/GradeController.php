<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Level $level)
    {
        //
        $grades = $level->grades;
        return response()->json($grades, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Level $level)
    {
        //
        $validate = Validator::make($request->all(), [
            'grade' => 'required|string|max:32',
            // 'level_id' => 'required|exists:levels,id',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Grade::create($request->all());
        $level->grades()->create($request->only['grade']);

        return response()->json(["message" => "The grade $request->grade has been successfully created"], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Level $level, Grade $grade)
    {
        //
        // $grade->load('level');
        $grade = $level->grades()->find($grade->id);
        return response()->json($grade, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Level $level, Grade $grade)
    {
        //
        $validate = Validator::make($request->all(), [
            'grade' => 'required|string|max:32',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $level->grades()->find($grade->id)->update($request->only(['grade']));

        return response()->json(["message" => "The grade $grade->grade has been successfully updated to $request->grade"], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Level $level, Grade $grade)
    {
        //
        $level->grades()->find($grade->id)->delete();

        return response()->json(["message" => "The grade $grade->grade has been successfully deleted from level " . $level->level], Response::HTTP_ACCEPTED);
    }
}
