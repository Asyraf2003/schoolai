<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PpdbSetting;
use App\Services\PpdbAccess;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PpdbSettingController extends Controller
{
    public function edit(PpdbAccess $access): View
    {
        return view('admin.ppdb.edit', ['setting' => $access->current()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'registration_url' => ['nullable', 'string', 'max:2048', function (string $attribute, mixed $value, Closure $fail): void {
                if (PpdbSetting::safeRegistrationUrl($value) === null) {
                    $fail('Gunakan URL HTTPS Google Form dari forms.gle atau docs.google.com/forms.');
                }
            }],
        ]);
        PpdbSetting::query()->updateOrCreate(['id' => 1], ['registration_url' => $data['registration_url'] ?? null]);

        return redirect()->route('admin.ppdb')->with('success', 'Link PPDB berhasil disimpan.');
    }
}
