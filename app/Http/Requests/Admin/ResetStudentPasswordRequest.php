<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class ResetStudentPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true
            && $this->route('account')?->isMurid() === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'password' => [
                'required', 'string', 'max:72', 'confirmed', Password::min(8),
            ],
        ];
    }
}
