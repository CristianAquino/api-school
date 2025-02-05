<?php

namespace App\Http\Controllers;

use App\DTOs\GradeDTO;
use App\Models\Grade;
use App\Models\GradeLevel;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $grade = strtolower($request->query('grade'));
        $grades = Grade::query()
            ->when(
                $grade,
                fn($query, $grade) => $query->whereRaw('LOWER(grade) LIKE ?',  "%$grade%")
            )
            ->paginate(10);

        $gradesDTO = GradeDTO::fromPagination($grades);
        return response()->json($gradesDTO, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource remove soft.
     */
    public function softList(Request $request)
    {
        //
        $response = Gate::inspect('softList', Grade::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $grade = strtolower($request->query('grade'));
        $deletedGrades = Grade::onlyTrashed()
            ->when(
                $grade,
                fn($query, $grade) => $query->whereRaw('LOWER(grade) LIKE ?',  "%$grade%")
            )
            ->paginate(10);

        $deleteGradesDTO = GradeDTO::fromPagination($deletedGrades);
        return response()->json($deleteGradesDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Level $level)
    {
        //
        $response = Gate::inspect('store', Grade::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $query = Grade::whereRaw('LOWER(grade) = ?', strtolower($request->validated_data["grade"]));

        $exists = $query->exists();

        if ($exists) {
            $g = $query->first();
            if ($g->levels->find($level) && $g->levels->find($level)->level == $level->level) {
                return response()->json([
                    "message" => "Grade $g->grade for level $level->level already exists, please enter another grade or modify the existing grade"
                ], Response::HTTP_BAD_REQUEST);
            }

            $level->grades()->attach($g);
            return response()->json([
                "message" => "Grade $request->grade has been correctly assigned to level $level->level"
            ], Response::HTTP_CREATED);
        }

        $g = Grade::create([
            'grade' => $request->validated_data["grade"]
        ]);

        $level->grades()->attach($g);
        return response()->json([
            "message" => "Grade $request->grade has been successfully created"
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Level $level, Grade $grade)
    {
        //
        $response = Gate::inspect('view', Grade::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $query = GradeLevel::where('level_id', $level->id)
            ->where('grade_id', $grade->id)
            ->first();

        if (is_null($query)) {
            return response()->json([
                "message" => "the grade or level does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $gradeDTO = GradeDTO::fromModelWithRelation($query);
        return response()->json($gradeDTO, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grade $grade)
    {
        //
        $response = Gate::inspect('update', Grade::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $g = $grade->grade;

        $grade->update($request->validated_data);

        if (!is_null($request->level_id)) {
            $grade->levels()->detach($grade->levels);
            $level = Level::find($request->validated_data["level_id"]);
            $level->grades()->attach($grade);
        }

        return response()->json([
            "message" => "The grade $g has been successfully updated to $request->grade"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function softDestroy(Grade $grade)
    {
        //
        $response = Gate::inspect('softDestroy', Grade::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $grade->delete();
        return response()->json([
            "message" => "The grade $grade->grade has been successfully deleted"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        //
        $response = Gate::inspect('restore', Grade::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $grade = Grade::onlyTrashed()->find($id);

        if (is_null($grade)) {
            return response()->json([
                "message" => "the grade does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $grade->restore();
        return response()->json([
            "message" => "the grade $grade->grade has been successfully restored"
        ], Response::HTTP_OK);
    }

    /**
     * Remove permanently the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $response = Gate::inspect('destroy', Grade::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $grade = Grade::onlyTrashed()->find($id);

        if (is_null($grade)) {
            return response()->json([
                "message" => "the grade does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $grade->forceDelete();
        return response()->json([
            "message" => "the grade $grade->grade has been successfully deleted permanently"
        ], Response::HTTP_ACCEPTED);
    }
}
