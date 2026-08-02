<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Support\AccountIdentity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateAccountRequest extends FormRequest
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
        /** @var User $account */
        $account = $this->route('account');

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                Rule::requiredIf($account->isAdmin() || $account->isGuru()),
                'nullable', 'email:rfc', 'max:255',
                Rule::unique('users', 'email_normalized')->ignore($account),
            ],
            'student_id' => [
                Rule::requiredIf($account->isMurid()), 'nullable', 'string',
                'regex:'.AccountIdentity::STUDENT_ID_PATTERN,
            ],
            'student_id_normalized' => [
                Rule::requiredIf($account->isMurid()), 'nullable',
                Rule::unique('users', 'student_id_normalized')->ignore($account),
            ],
        ];
    }
}
