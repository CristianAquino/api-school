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
    public function index(Request $request)
    {
        //
        $year = $request->query('year');
        $code = strtolower($request->query('code'));
        $level = strtolower($request->query('level'));

        $enrollements = Enrollement::query()
            ->when($year, function ($query) use ($year) {
                $query->whereHas('academic_year', function ($query) use ($year) {
                    $query->where('year', 'like', "%$year%");
                });
            })
            ->when($level, function ($query) use ($level) {
                $query->whereHas('gradeLevel', function ($query) use ($level) {
                    $query->whereHas('level', function ($query) use ($level) {
                        $query->whereRaw('Lower(level) like ?', "%$level%");
                    });
                });
            })
            ->when($code, function ($query) use ($code) {
                $query->whereHas('student', function ($query) use ($code) {
                    $query->whereHas('user', function ($query) use ($code) {
                        $query->whereRaw('Lower(code) like ?', "%$code%");
                    });
                });
            })
            ->paginate(10);

        $enrollementsDTO = EnrollementDTO::fromPagination($enrollements);
        return response()->json($enrollementsDTO, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource remove soft.
     */
    public function softList(Request $request)
    {
        //
        $response = Gate::inspect('softList', Enrollement::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $year = $request->query('year');
        $code = strtolower($request->query('code'));
        $level = strtolower($request->query('level'));

        $deletedEnrollements = Enrollement::onlyTrashed()
            ->when($year, function ($query) use ($year) {
                $query->whereHas('academic_year', function ($query) use ($year) {
                    $query->where('year', 'like', "%$year%");
                });
            })
            ->when($level, function ($query) use ($level) {
                $query->whereHas('gradeLevel', function ($query) use ($level) {
                    $query->whereHas('level', function ($query) use ($level) {
                        $query->whereRaw('Lower(level) like ?', "%$level%");
                    });
                });
            })
            ->when($code, function ($query) use ($code) {
                $query->whereHas('student', function ($query) use ($code) {
                    $query->whereHas('user', function ($query) use ($code) {
                        $query->whereRaw('Lower(code) like ?', "%$code%");
                    });
                });
            })
            ->paginate(10);

        $deletedEnrollementsDTO = EnrollementDTO::fromPagination($deletedEnrollements);
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
            $c = User::where('userable_type', Student::class)
                ->latest("id")
                ->first()->code;
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
    // imprimir el historial de matricula de un estudiante,
    // esto incluira todas las matriculas, notas, etc para cada estudiante

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

        $enrollement->academic_year_id = null;
        $enrollement->grade_level_id = null;
        $enrollement->student_id = null;
        $enrollement->save();

        $enrollement->forceDelete();
        return response()->json([
            "message" => "the enrollement with code $enrollement->id has been successfully deleted permanently"
        ], Response::HTTP_ACCEPTED);
    }
}
