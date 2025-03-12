<?php

namespace App\Http\Middleware;

use Closure;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AcademicYearValidatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rules = [
            'year' => ['required', 'string', 'max:4'],
            'start_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $date = DateTime::createFromFormat('Y/m/d', $value);
                    $year = $request->year;

                    if (!$date || $date->format('Y/m/d') != $value) {
                        $fail("The start date $value is not in Y/m/d format or is an invalid date");
                    }
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
        ];

        if ($request->isMethod('post')) {
            $rules['year'][] = 'unique:academic_years,year';
        } elseif ($request->isMethod('put') || $request->isMethod('patch')) {
            $academicYear = $request->route('academicYear');
            $rules['year'][] = 'unique:academic_years,year,' . $academicYear->id;
        }

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $request->merge(['validated_data' => $validate->validated()]);
        return $next($request);
    }
}
