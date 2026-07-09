<?php

namespace App\Http\Controllers;

use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

final class PpdbPageController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.ppdb', [
            'ppdbAdmission' => $this->currentSetting(),
            'ppdbShowcaseItems' => $this->showcaseItems(),
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

    private function showcaseItems(): Collection
    {
        if (! Schema::hasTable('ppdb_showcase_items')) {
            return collect();
        }

        return PpdbShowcaseItem::query()
            ->ordered()
            ->get();
    }
}
