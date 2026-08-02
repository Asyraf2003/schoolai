<?php

namespace App\Http\Requests\Auth;

use App\Support\AccountIdentity;
use Illuminate\Foundation\Http\FormRequest;

final class StudentLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'string', 'regex:'.AccountIdentity::STUDENT_ID_PATTERN],
            'password' => ['required', 'string', 'max:255'],
        ];
    }
}
