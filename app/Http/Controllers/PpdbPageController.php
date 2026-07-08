<?php

namespace App\Http\Controllers;

use App\Models\PpdbSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

final class PpdbPageController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.ppdb', [
            'ppdbAdmission' => $this->currentSetting(),
        ]);
    }

    private function currentSetting(): PpdbSetting
    {
        if (! Schema::hasTable('ppdb_settings')) {
            return new PpdbSetting([
                'registration_url' => PpdbSetting::DEFAULT_REGISTRATION_URL,
                'is_active' => true,
            ]);
        }

        $setting = PpdbSetting::query()->first();

        if ($setting instanceof PpdbSetting) {
            return $setting;
        }

        return new PpdbSetting([
            'registration_url' => PpdbSetting::DEFAULT_REGISTRATION_URL,
            'is_active' => true,
        ]);
    }
}
