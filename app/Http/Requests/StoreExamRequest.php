<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
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
            'exam_name' => ['required', 'string', 'max:255'],
            'startAt' => ['required', 'date'],
            'dueAt' => ['required', 'date', 'after:startAt'],
        ];
    }
}
