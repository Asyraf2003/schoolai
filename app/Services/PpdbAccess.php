<?php

namespace App\Services;

use App\Models\PpdbSetting;
use Illuminate\Support\Facades\Schema;

final class PpdbAccess
{
    public function current(): PpdbSetting
    {
        if (Schema::hasTable('ppdb_settings')) {
            $setting = PpdbSetting::query()->first();

            if ($setting instanceof PpdbSetting) {
                return $setting;
            }
        }

        return new PpdbSetting([
            'registration_url' => PpdbSetting::DEFAULT_REGISTRATION_URL,
            'is_active' => true,
        ]);
    }
}
