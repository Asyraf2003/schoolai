<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AccountResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'student_id' => $this->student_id,
            'role' => $this->role?->value,
            'role_label' => $this->role?->label() ?? 'Inert',
            'active' => $this->isActive(),
            'status_label' => $this->isActive() ? 'Aktif' : 'Nonaktif',
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'last_login_label' => $this->last_login_at
                ? $this->last_login_at->translatedFormat('d M Y, H:i')
                : 'Belum pernah',
        ];
    }
}
