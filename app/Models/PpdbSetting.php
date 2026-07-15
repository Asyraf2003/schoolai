<?php

namespace App\Models;

use App\Models\Concerns\AuditsAdminChanges;
use App\Support\PublicUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class PpdbSetting extends Model
{
    use AuditsAdminChanges, HasFactory;

    public const DEFAULT_REGISTRATION_URL = 'https://forms.gle/1huqPo24Et6pgUNh6';

    protected $fillable = [
        'registration_url',
        'information_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function isRegistrationOpen(): bool
    {
        return $this->is_active && $this->publicRegistrationUrl() !== null;
    }

    public function publicRegistrationUrl(): ?string
    {
        return $this->publicUrl($this->registration_url);
    }

    public function publicInformationUrl(): ?string
    {
        return $this->publicUrl($this->information_url);
    }

    private function publicUrl(mixed $value): ?string
    {
        return PublicUrl::normalize(
            (string) $value,
            ['/admin', '/login', '/auth']
        );
    }
}
