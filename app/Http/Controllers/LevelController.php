<?php

namespace App\Http\Controllers;

use App\DTOs\LevelDTO;
use App\Models\Level;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $levels = Level::all();
        $levelsDTO = LevelDTO::fromNotRelationCollection($levels);
        return response()->json($levelsDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 
        Level::create($request->validated_data);
        return response()->json(["message" => "The level $request->level has been successfully created"], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Level $level)
    {
        //
        return response()->json(LevelDTO::fromModel($level), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Level $level)
    {
        //
        $lev = $level->level;

        $level->update($request->validated_data);

        return response()->json(["message" => "The level $lev has been successfully updated to $request->level"], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Level $level)
    {
        //
        $level->delete();
        return response()->json(["message" => "The level $level->level has been successfully deleted"], Response::HTTP_ACCEPTED);
    }
}
