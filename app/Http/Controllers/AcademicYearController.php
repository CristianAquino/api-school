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
    public function index(Request $request)
    {
        //
        $year = $request->query('year');
        $academicYears = AcademicYear::query()
            ->when(
                $year,
                fn($query, $year) => $query->where('year', 'like',  "%$year%")
            )
            ->paginate(10);

        $academicYearsDTO = AcademicYearDTO::fromPagination($academicYears);
        return response()->json($academicYearsDTO, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource remove soft.
     */
    public function softList(Request $request)
    {
        //
        $response = Gate::inspect('softList', AcademicYear::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $year = $request->query('year');

        $deletedAcademicYears = AcademicYear::onlyTrashed()
            ->when(
                $year,
                fn($query, $year) => $query->where('year', 'like',  "%$year%")
            )
            ->paginate(10);;

        $deletedAcademicYearsDTO = AcademicYearDTO::fromPagination($deletedAcademicYears);
        return response()->json($deletedAcademicYearsDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $response = Gate::inspect('store', AcademicYear::class);

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
        $response = Gate::inspect('view', AcademicYear::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $academicYearDTO = AcademicYearDTO::fromBase($academicYear);
        return response()->json($academicYearDTO, Response::HTTP_OK);
    }

    /**
     * Display the last year resource.
     */
    public function lastYear()
    {
        // 
        $response = Gate::inspect('view', AcademicYear::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $lastYear = AcademicYear::latest('year')->first();
        $lastYearDTO = AcademicYearDTO::fromBase($lastYear);
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
    public function softDestroy(AcademicYear $academicYear)
    {
        //
        $response = Gate::inspect('softDestroy', AcademicYear::class);

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
        $response = Gate::inspect('destroy', AcademicYear::class);

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
