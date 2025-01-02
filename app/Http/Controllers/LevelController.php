<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        return response()->json($levels, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'level' => [
                'required',
                'string',
                'max:64',
                function ($attribute, $value, $fail) {
                    $exists = Level::whereRaw('LOWER(level) = ?', strtolower($value))->exists();
                    if ($exists) {
                        $fail("The $value level already exists.");
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        Level::create($request->all());

        return response()->json(["message" => "The $request->level level has been successfully created"], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Level $level)
    {
        //
        $level->load('grades');
        return response()->json($level, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Level $level)
    {
        //
        $validator = Validator::make($request->all(), [
            'level' => [
                'required',
                'string',
                'max:64',
                function ($attribute, $value, $fail) {
                    $exists = Level::whereRaw('LOWER(level) = ?', strtolower($value))->exists();
                    if ($exists) {
                        $fail("The $value level already exists.");
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $level->update($request->all());

        return response()->json(["message" => "The $request->level level has been successfully updated"], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Level $level)
    {
        //
        $level->delete();
        return response()->json(["message" => "The $level->level level has been successfully deleted"], Response::HTTP_ACCEPTED);
    }
}