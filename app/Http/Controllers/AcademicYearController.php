<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $academicYears = AcademicYear::all();
        return response()->json($academicYears, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validate = Validator::make($request->all(), [
            'year' => 'required|string|unique:academic_years',
            'start_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $year = $request->year;
                    if (date('Y', strtotime($value)) != $year) {
                        $fail("The start date must start in the year $year");
                    }
                },
            ],
            'end_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $year = $request->year;
                    if ((int)date('Y', strtotime($value)) < (int)$year) {
                        $fail("The end date must end int the year $year or a later year");
                    }
                },
            ],
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        AcademicYear::create($request->all());

        return response()->json(["message" => "The $request->year academic year has been successfully created"], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear)
    {
        //
        return response()->json($academicYear, 200);
    }

    public function lastYear()
    {
        $lastYear = AcademicYear::latest('year')->first();
        if ($lastYear) {
            return response()->json($lastYear, 200);
        } else {
            return response()->json(['message' => 'Academic year not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        //
        $validate = Validator::make($request->all(), [
            'year' => 'required|string|unique:academic_years,year,' . $academicYear->id,
            'start_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $year = $request->year;
                    if (date('Y', strtotime($value)) != $year) {
                        $fail("The start date must start in the year $year");
                    }
                },
            ],
            'end_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $year = $request->year;
                    if ((int)date('Y', strtotime($value)) < (int)$year) {
                        $fail("The end date must end int the year $year or a later year");
                    }
                },
            ],
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $academicYear->update($request->all());

        return response()->json(["message" => "the $academicYear->year academic year has been successfully updated"], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear)
    {
        //
        $academicYear->delete();
        return response()->json(["message" => "the $academicYear->year academic year has been successfully deleted"], 200);
    }
}
