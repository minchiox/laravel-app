<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLibraryRequest extends FormRequest
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
            'library_name' => ['required', 'string', 'max:255'],
            'library_subject' => ['required', 'string', 'max:255'],
            'library_difficulty' => ['required', 'in:easy,medium,hard'],
        ];
    }
}
