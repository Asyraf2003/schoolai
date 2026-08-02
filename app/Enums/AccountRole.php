<?php

namespace App\Enums;

enum AccountRole: string
{
    case Admin = 'admin';
    case Guru = 'guru';
    case Murid = 'murid';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Guru => 'Guru',
            self::Murid => 'Murid',
        };
    }
}
