<?php

namespace App\Http\Controllers;

use App\DTOs\EnrollementDTO;
use App\Models\AcademicYear;
use App\Models\Enrollement;
use App\Models\GradeLevel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class EnrollementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $enrollements = Enrollement::all();
        $enrollementsDTO = EnrollementDTO::fromCollection($enrollements);
        return response()->json($enrollementsDTO, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource remove soft.
     */
    public function softList()
    {
        //
        $response = Gate::inspect('softList', Enrollement::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $deletedEnrollements = Enrollement::onlyTrashed()->get();
        $deletedEnrollementsDTO = EnrollementDTO::fromCollection($deletedEnrollements);
        return response()->json($deletedEnrollementsDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, AcademicYear $academicYear, GradeLevel $gradeLevel)
    {
        //
        $response = Gate::inspect('store', Enrollement::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $i = Student::count();

        if ($i == 0) {
            $code = 'ST' . (int)date('Y') * 10000 + $i;
        } else {
            $c = Student::latest("id")->first()->user->code;
            $i = (int)substr($c, 2) + 1;
            $code = 'ST' . $i;
        }

        $student = Student::create([
            'role' => User::ROLE_STUDENT
        ]);

        $new_datos = $request->validated_data;
        $new_datos['code'] = $code;
        $new_datos['password'] = $code . $new_datos['dni'];

        $student->user()->create($new_datos);

        if (is_null($student->user)) {
            $student->delete();
            return response()->json([
                "message" => "The registration for student $request->first_name $request->second_name $request->name has not been created successfully"
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $enrollement = new Enrollement();
        $enrollement->student_id = $student->id;
        $enrollement->academic_year_id = $academicYear->id;
        $enrollement->grade_level_id = $gradeLevel->id;
        $enrollement->save();

        return response()->json([
            "message" => "The registration for student $request->first_name $request->second_name $request->name has been created successfully"
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollement $enrollement)
    {
        //
        $enrollementDTO = EnrollementDTO::fromModel($enrollement);
        return response()->json($enrollementDTO, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enrollement $enrollement)
    {
        //
    }

    /**
     * Remove soft the specified resource from storage.
     */
    public function softDestroy(Enrollement $enrollement)
    {
        //
        $response = Gate::inspect('softDestroy', Enrollement::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $enrollement->delete();
        return response()->json([
            "message" => "the enrollement with code $enrollement->id has been successfully deleted"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        //
        $response = Gate::inspect('restore', Enrollement::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $enrollement = Enrollement::onlyTrashed()->find($id);

        if (is_null($enrollement)) {
            return response()->json([
                "message" => "the enrollement does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $enrollement->restore();
        return response()->json([
            "message" => "the enrollement with code $enrollement->id has been successfully restored"
        ], Response::HTTP_OK);
    }

    /**
     * Remove permanently the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $response = Gate::inspect('destroy', Enrollement::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $enrollement = Enrollement::onlyTrashed()->find($id);

        if (is_null($enrollement)) {
            return response()->json([
                "message" => "the enrollement does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $enrollement->forceDelete();
        return response()->json([
            "message" => "the enrollement with code $enrollement->id has been successfully deleted permanently"
        ], Response::HTTP_ACCEPTED);
    }
}
