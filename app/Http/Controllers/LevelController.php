<?php

namespace App\Http\Controllers;

use App\DTOs\LevelDTO;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $level = strtolower($request->query('level'));
        $levels = Level::query()
            ->when(
                $level,
                fn($query, $level) => $query->whereRaw('LOWER(level) LIKE ?',  "%$level%")
            )
            ->paginate(10);

        $levelsDTO = LevelDTO::fromPagination($levels);
        return response()->json($levelsDTO, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource remove soft.
     */
    public function softList(Request $request)
    {
        //
        $response = Gate::inspect('softList', Level::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $level = strtolower($request->query('level'));
        $deletedLevel = Level::onlyTrashed()
            ->when(
                $level,
                fn($query, $level) => $query->whereRaw('LOWER(level) LIKE ?',  "%$level%")
            )
            ->paginate(10);

        $deleteLevelsDTO = LevelDTO::fromPagination($deletedLevel);
        return response()->json($deleteLevelsDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 
        $response = Gate::inspect('store', Level::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        Level::create($request->validated_data);
        return response()->json([
            "message" => "The level $request->level has been successfully created"
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Level $level)
    {
        //
        $response = Gate::inspect('view', Level::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $levelDTO = LevelDTO::fromModelWithRelation($level);
        return response()->json($levelDTO, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Level $level)
    {
        //
        $response = Gate::inspect('update', Level::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $lev = $level->level;

        $level->update($request->validated_data);
        return response()->json([
            "message" => "The level $lev has been successfully updated to $request->level"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function softDestroy(Level $level)
    {
        //
        $response = Gate::inspect('softDestroy', Level::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $level->delete();
        return response()->json([
            "message" => "The level $level->level has been successfully deleted"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        //
        $response = Gate::inspect('restore', Level::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $level = Level::onlyTrashed()->find($id);

        if (is_null($level)) {
            return response()->json([
                "message" => "the level does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $level->restore();
        return response()->json([
            "message" => "the level $level->level has been successfully restored"
        ], Response::HTTP_OK);
    }

    /**
     * Remove permanently the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $response = Gate::inspect('destroy', Level::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $level = Level::onlyTrashed()->find($id);

        if (is_null($level)) {
            return response()->json([
                "message" => "the level does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $level->forceDelete();
        return response()->json([
            "message" => "the level $level->level has been successfully deleted permanently"
        ], Response::HTTP_ACCEPTED);
    }
}