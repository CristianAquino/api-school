<?php

namespace App\Http\Middleware;

use App\Models\Qualification;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class QualificationValidatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rules = [
            'number_note' => 'required|numeric|between:0,20',
            'letter_note' => [
                'sometimes',
                'string',
                function ($attribute, $value, $fail) use ($request) {

                    if (!in_array(strtoupper($value), Qualification::LETTER_NOTES)) {
                        $fail('The letter note must be a valid letter note.');
                    }

                    $numberNote = $request->number_note;
                    $correspondencias = [
                        'AD' => [18, 20],
                        'A' => [16, 17],
                        'B' => [10, 15],
                        'C' => [0, 9],
                    ];

                    $letterNoteValid = null;

                    foreach ($correspondencias as $letter => [$min, $max]) {
                        if ($numberNote >= $min && $numberNote <= $max) {
                            $letterNoteValid = $letter;
                            break;
                        }
                    }

                    if ($letterNoteValid && strtoupper($value) !== $letterNoteValid) {
                        $fail("The letter note must correspond to the number note. Expected: $letterNoteValid.");
                    }
                },
            ],
        ];

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $request->merge([
            'validated_data' => $validate->validated()
        ]);

        return $next($request);
    }
}
