<?php

namespace App\Models;

use App\Models\Concerns\AuditsAdminChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class PpdbSetting extends Model
{
    use AuditsAdminChanges, HasFactory;

    protected $fillable = ['registration_url'];

    public function isRegistrationOpen(): bool
    {
        return $this->publicRegistrationUrl() !== null;
    }

    public function publicRegistrationUrl(): ?string
    {
        return self::safeRegistrationUrl($this->registration_url);
    }

    public static function safeRegistrationUrl(mixed $value): ?string
    {
        if (! is_string($value) || ! filter_var($value, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($value);
        if (($parts['scheme'] ?? '') !== 'https' || isset($parts['user'], $parts['pass'])
            || isset($parts['user']) || isset($parts['port']) || preg_match('/[\x00-\x20\x7f]/', $value)) {
            return null;
        }

        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';
        $valid = ($host === 'forms.gle' && preg_match('~^/[A-Za-z0-9_-]+/?$~D', $path))
            || ($host === 'docs.google.com' && preg_match('~^/forms/(?:u/\d+/)?d/(?:e/)?[A-Za-z0-9_-]+/viewform/?$~D', $path));

        return $valid ? $value : null;
    }
}
