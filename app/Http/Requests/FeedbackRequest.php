<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated users are allowed to submit feedback
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|min:3|max:50',
            'email'    => 'required|email|max:100',
            'feedback' => 'required|string|min:10|max:1000',
        ];
    }

    /**
     * Custom messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'Nama wajib diisi.',
            'name.min'          => 'Nama minimal terdiri dari 3 karakter.',
            'name.max'          => 'Nama maksimal terdiri dari 50 karakter.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.max'         => 'Email maksimal terdiri dari 100 karakter.',
            'feedback.required' => 'Pesan feedback wajib diisi.',
            'feedback.min'      => 'Pesan feedback minimal terdiri dari 10 karakter.',
            'feedback.max'      => 'Pesan feedback maksimal terdiri dari 1000 karakter.',
        ];
    }
}
