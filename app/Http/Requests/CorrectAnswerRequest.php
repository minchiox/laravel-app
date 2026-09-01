<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorrectAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La rotta e' gia' filtrata dal middleware isTeacher.
        return true;
    }

    /**
     * Prima non c'era validazione: exam_id e user_id arrivavano grezzi da
     * $request->input() e un id inesistente produceva solo un
     * aggiornamento a zero righe, silenzioso invece che segnalato.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'exam_id' => ['required', 'integer', 'exists:exams,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
