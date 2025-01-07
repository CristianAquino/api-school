<?php

namespace App\Http\Controllers;

use App\DTOs\AcademicYearDTO;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
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
        $academicYearsDTO = AcademicYearDTO::fromCollection($academicYears);
        return response()->json($academicYearsDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        AcademicYear::create($request->validated_data);
        return response()->json(["message" => "The academic year $request->year has been successfully created"], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear)
    {
        //
        $academicYearDTO = AcademicYearDTO::fromModel($academicYear);
        return response()->json($academicYearDTO, Response::HTTP_OK);
    }

    public function lastYear()
    {
        $lastYear = AcademicYear::latest('year')->first();
        $lastYearDTO = AcademicYearDTO::fromModel($lastYear);
        return response()->json($lastYearDTO, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        //
        $year = $academicYear->year;

        $academicYear->update($request->validated_data);

        if ($academicYear->year == $year) {
            return response()->json(["message" => "the academic year $year has been successfully updated"], Response::HTTP_ACCEPTED);
        } else {
            return response()->json(["message" => "the academic year $year has been successfully updated to $academicYear->year"], Response::HTTP_ACCEPTED);
        }
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
