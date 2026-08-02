<?php

namespace App\Http\Controllers;

use App\Models\PpdbShowcaseItem;
use App\Services\PpdbAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

final class PpdbPageController extends Controller
{
    public function __invoke(PpdbAccess $access): View|Response
    {
        $setting = $access->current();

        if (! $setting->isRegistrationOpen()) {
            return response()->view('pages.ppdb-closed', status: 404);
        }

        return view('pages.ppdb', [
            'ppdbAdmission' => $setting,
            'ppdbShowcaseItems' => $this->showcaseItems(),
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
