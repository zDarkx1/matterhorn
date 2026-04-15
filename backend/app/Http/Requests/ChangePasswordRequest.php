<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'new_password'     => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'         => 'Password lama wajib diisi.',
            'current_password.current_password'  => 'Password lama tidak cocok.',
            'new_password.required'              => 'Password baru wajib diisi.',
            'new_password.min'                   => 'Password baru minimal 8 karakter.',
            'new_password.confirmed'             => 'Konfirmasi password baru tidak cocok.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Validasi gagal.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
