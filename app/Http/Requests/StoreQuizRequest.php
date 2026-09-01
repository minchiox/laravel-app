<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La rotta e' gia' filtrata dal middleware isTeacher.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:255'],
            // Non e' una colonna: sceglie quale delle due risposte sotto e'
            // quella valida per questo quiz.
            'answer-type' => ['required', 'in:open,close'],
            'answer' => ['required_if:answer-type,close', 'boolean'],
            'answer_text' => ['required_if:answer-type,open', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'points' => ['required', 'integer', 'min:1'],
        ];
    }
}
