<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PpdbSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

final class PpdbSettingController extends Controller
{
    public function __construct()
    {
        app()->setLocale('id');
    }

    public function edit(): View
    {
        return view('admin.ppdb.edit', [
            'adminPageKey' => 'ppdb',
            'setting' => $this->currentSetting(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validator = validator($request->all(), [
            'registration_url' => ['required', 'url', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'registration_url.required' => 'Link formulir PPDB wajib diisi.',
            'registration_url.url' => 'Link formulir PPDB tidak valid.',
            'registration_url.max' => 'Link formulir PPDB terlalu panjang.',
        ]);

        $validator->after(function (Validator $validator) use ($request): void {
            $url = trim((string) $request->input('registration_url'));

            if ($url !== '' && ! $this->isPublicUrl($url)) {
                $validator->errors()->add('registration_url', 'Link formulir PPDB harus URL publik, bukan localhost, IP lokal, login, atau halaman admin.');
            }
        });

        $validated = $validator->validate();

        $setting = $this->currentSetting();
        $setting->update([
            'registration_url' => trim((string) $validated['registration_url']),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.ppdb')
            ->with('success', 'Pengaturan PPDB berhasil disimpan.');
    }

    public function toggle(): RedirectResponse
    {
        $setting = $this->currentSetting();
        $setting->update([
            'is_active' => ! $setting->is_active,
        ]);

        return redirect()
            ->route('admin.ppdb')
            ->with('success', $setting->fresh()?->is_active ? 'PPDB berhasil diaktifkan.' : 'PPDB berhasil dinonaktifkan.');
    }

    private function currentSetting(): PpdbSetting
    {
        return PpdbSetting::query()->firstOrCreate([], [
            'registration_url' => PpdbSetting::DEFAULT_REGISTRATION_URL,
            'is_active' => true,
        ]);
    }

    private function isPublicUrl(string $url): bool
    {
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = '/' . ltrim((string) parse_url($url, PHP_URL_PATH), '/');

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return false;
        }

        if (
            $host === 'localhost' ||
            $host === '127.0.0.1' ||
            $host === '::1' ||
            str_ends_with($host, '.local') ||
            str_starts_with($host, '10.') ||
            str_starts_with($host, '192.168.') ||
            preg_match('/^172\.(1[6-9]|2\d|3[0-1])\./', $host) === 1
        ) {
            return false;
        }

        return ! (
            $path === '/admin' ||
            str_starts_with($path, '/admin/') ||
            $path === '/login' ||
            str_starts_with($path, '/auth/')
        );
    }
}
