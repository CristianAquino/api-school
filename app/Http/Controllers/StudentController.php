<?php

namespace App\Http\Controllers;

use App\DTOs\EnrollementDTO;
use App\DTOs\QualificationDTO;
use App\DTOs\ScheduleDTO;
use App\DTOs\StudentDTO;
use App\Models\Enrollement;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $code = strtolower($request->query('code'));
        $students = Student::query()
            ->when($code, function ($query) use ($code) {
                $query->whereHas('user', function ($query) use ($code) {
                    $query->whereRaw('LOWER(code) LIKE ?', "%$code%");
                });
            })
            ->paginate(10);

        $studentsDTO = StudentDTO::fromPagination($students);
        return response()->json($studentsDTO, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource remove soft.
     */
    public function softList(Request $request)
    {
        //
        $response = Gate::inspect('softList', Student::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $code = strtolower($request->query('code'));
        $deletedStudents = Student::onlyTrashed()
            ->when($code, function ($query) use ($code) {
                $query->whereHas('user', function ($query) use ($code) {
                    $query->whereRaw('LOWER(code) LIKE ?', "%$code%");
                });
            })
            ->paginate(10);

        $deletedStudentsDTO = StudentDTO::fromPagination($deletedStudents);
        return response()->json($deletedStudentsDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function me()
    {
        //
        $me = Auth::user()->userable_id;
        $user = Student::where('id', $me)->first();
        if (is_null($user)) {
            return response()->json([
                "message" => "You do not have the role allowed to perform this action"
            ], Response::HTTP_NOT_FOUND);
        }

        $enrolle = Enrollement::where("student_id", $user->id)->latest("id")->first();

        $studentDTO = StudentDTO::fromModelWithRelation($enrolle);
        return response()->json($studentDTO, Response::HTTP_OK);
    }

    /**
     * Print the specified resource.
     */
    public function printEnrollement()
    {
        //
        $me = Auth::user()->userable_id;
        $user = Student::where('id', $me)->first();
        if (is_null($user)) {
            return response()->json([
                "message" => "You do not have the role allowed to perform this action"
            ], Response::HTTP_NOT_FOUND);
        }

        $enrollement = Enrollement::where("student_id", $user->id)->latest("id")->first();

        $enrollementDTO = EnrollementDTO::fromBaseModel($enrollement);
        $pdf = PDF::loadView('pdf.enrollement', ['enrollement' => $enrollementDTO]);
        return $pdf->download('enrollement.pdf');
    }

    /**
     * Print the specified resource.
     */
    public function printSchedule()
    {
        //
        $me = Auth::user()->userable_id;
        $user = Student::where('id', $me)->first();
        if (is_null($user)) {
            return response()->json([
                "message" => "You do not have the role allowed to perform this action"
            ], Response::HTTP_NOT_FOUND);
        }

        $enrollement = Enrollement::where("student_id", $user->id)->latest("id")->first();

        $scheduleDTO = ScheduleDTO::fromPrintModel($enrollement);
        $pdf = PDF::loadView('pdf.schedule', ['schedule' => $scheduleDTO]);
        return $pdf->download('schedule.pdf');
    }

    /**
     * Print the specified resource.
     */
    public function printQualification()
    {
        //
        $me = Auth::user()->userable_id;
        $user = Student::where('id', $me)->first();
        if (is_null($user)) {
            return response()->json([
                "message" => "You do not have the role allowed to perform this action"
            ], Response::HTTP_NOT_FOUND);
        }

        $qualificationDTO = QualificationDTO::fromPrintModel($user);
        $pdf = PDF::loadView('pdf.qualification', ['qualification' => $qualificationDTO]);
        return $pdf->download('qualification.pdf');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
        $response = Gate::inspect('view', Student::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $enrolle = Enrollement::where("student_id", $student->id)->latest("id")->first();

        $studentDTO = StudentDTO::fromModelWithRelation($enrolle);
        return response()->json($studentDTO, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        //
        $response = Gate::inspect('update', Student::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $student->user()->update($request->validated_data);

        return response()->json(["message" => "The student with code " . $student->user->code . " has been successfully updated"], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove soft the specified resource from storage.
     */
    public function softDestroy(Student $student)
    {
        //
        $response = Gate::inspect('softDestroy', Student::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $student->delete();
        return response()->json([
            "message" => "the student " . $student->user->first_name . " " . $student->user->second_name . " " . $student->user->name . " has been successfully deleted"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        //
        $response = Gate::inspect('restore', Student::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $student = Student::onlyTrashed()->find($id);

        if (is_null($student)) {
            return response()->json([
                "message" => "the student year does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $student->restore();
        return response()->json([
            "message" => "the student " . $student->user->first_name . " " . $student->user->second_name . " " . $student->user->name . " has been successfully restored"
        ], Response::HTTP_OK);
    }

    /**
     * Remove permanently the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $response = Gate::inspect('destroy', Student::class);
        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $student = Student::onlyTrashed()->find($id);
        if (is_null($student)) {
            return response()->json([
                "message" => "the student does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }
        $code = $student->user->code;

        $student->forceDelete();
        User::where('userable_id', $student->id)->delete();
        return response()->json([
            "message" => "the student with code $code has been successfully deleted permanently"
        ], Response::HTTP_ACCEPTED);
    }
}
