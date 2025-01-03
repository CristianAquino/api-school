<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $academicYears = AcademicYear::all();
        return response()->json($academicYears, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validate = Validator::make($request->all(), [
            'year' => 'required|string|unique:academic_years,year',
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
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        AcademicYear::create($request->all());

        return response()->json(["message" => "The academic year $request->year has been successfully created"], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear)
    {
        //
        return response()->json($academicYear, Response::HTTP_OK);
    }

    public function lastYear()
    {
        $lastYear = AcademicYear::latest('year')->first();
        if ($lastYear) {
            return response()->json($lastYear, Response::HTTP_OK);
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
            'year' => 'required|string|unique:academic_years,year' . $academicYear->id,
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

        return response()->json(["message" => "the academic year $academicYear->year has been successfully updated"], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear)
    {
        //
        $academicYear->delete();
        return response()->json(["message" => "the academic year $academicYear->year has been successfully deleted"], Response::HTTP_ACCEPTED);
    }
}
