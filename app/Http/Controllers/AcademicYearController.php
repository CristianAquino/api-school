<?php

namespace App\Http\Controllers;

use App\DTOs\AcademicYearDTO;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
     * Display a listing of the resource remove soft.
     */
    public function soft_list()
    {
        //
        $response = Gate::inspect('soft_view', AcademicYear::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $deletedAcademicYears = AcademicYear::onlyTrashed()->get();
        $deletedAcademicYearsDTO = AcademicYearDTO::fromCollection($deletedAcademicYears);
        return response()->json($deletedAcademicYearsDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $response = Gate::inspect('create', AcademicYear::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        AcademicYear::create($request->validated_data);
        return response()->json([
            "message" => "The academic year $request->year has been successfully created"
        ], Response::HTTP_CREATED);
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

    /**
     * Display the last year resource.
     */
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
        $response = Gate::inspect('update', AcademicYear::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $year = $academicYear->year;

        $academicYear->update($request->validated_data);

        if ($academicYear->year == $year) {
            return response()->json([
                "message" => "the academic year $year has been successfully updated"
            ], Response::HTTP_ACCEPTED);
        } else {
            return response()->json([
                "message" => "the academic year $year has been successfully updated to $academicYear->year"
            ], Response::HTTP_ACCEPTED);
        }
    }

    /**
     * Remove soft the specified resource from storage.
     */
    public function soft_destroy(AcademicYear $academicYear)
    {
        //
        $response = Gate::inspect('soft_delete', AcademicYear::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $academicYear->delete();
        return response()->json([
            "message" => "the academic year $academicYear->year has been successfully deleted"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        //
        $response = Gate::inspect('restore', AcademicYear::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $academicYear = AcademicYear::onlyTrashed()->find($id);

        if (is_null($academicYear)) {
            return response()->json([
                "message" => "the academic year does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $academicYear->restore();
        return response()->json([
            "message" => "the academic year $academicYear->year has been successfully restored"
        ], Response::HTTP_OK);
    }

    /**
     * Remove permanently the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $response = Gate::inspect('forceDelete', AcademicYear::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }
        // if ($academicYear->enrollements->count() > 0) {
        //     return response()->json([
        //         "message" => "the academic year $academicYear->year cannot be deleted because it has enrollements"
        //     ], Response::HTTP_BAD_REQUEST);
        // }
        $academicYear = AcademicYear::onlyTrashed()->find($id);

        if (is_null($academicYear)) {
            return response()->json([
                "message" => "the academic year does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $academicYear->forceDelete();
        return response()->json([
            "message" => "the academic year $academicYear->year has been successfully deleted permanently"
        ], Response::HTTP_ACCEPTED);
    }
}