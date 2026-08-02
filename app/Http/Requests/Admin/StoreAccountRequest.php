<?php

namespace App\Http\Requests\Admin;

use App\Enums\AccountRole;
use App\Support\AccountIdentity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

final class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => AccountIdentity::email($this->input('email')),
            'student_id_normalized' => AccountIdentity::studentId($this->input('student_id')),
        ]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $guru = $this->input('role') === AccountRole::Guru->value;
        $murid = $this->input('role') === AccountRole::Murid->value;

        return [
            'role' => ['required', Rule::enum(AccountRole::class)->only([
                AccountRole::Guru, AccountRole::Murid,
            ])],
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                Rule::requiredIf($guru), 'nullable', 'email:rfc', 'max:255',
                Rule::unique('users', 'email_normalized'),
            ],
            'student_id' => [
                Rule::requiredIf($murid), 'nullable', 'string',
                'regex:'.AccountIdentity::STUDENT_ID_PATTERN,
            ],
            'student_id_normalized' => [
                Rule::requiredIf($murid), 'nullable',
                Rule::unique('users', 'student_id_normalized'),
            ],
            'password' => [
                Rule::requiredIf($murid), 'nullable', 'string', 'max:72',
                'confirmed', Password::min(8),
            ],
        ];
    }
}
