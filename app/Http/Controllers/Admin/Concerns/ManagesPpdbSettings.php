<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\PpdbShowcaseItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Throwable;

trait ManagesPpdbSettings
{
    public function edit(): View
    {
        return $this->editView(new PpdbShowcaseItem([
            'audience' => PpdbShowcaseItem::AUDIENCE_PARENTS,
            'media_type' => PpdbShowcaseItem::MEDIA_PHOTO,
        ]), 'create');
    }

    public function update(Request $request): RedirectResponse
    {
        $validator = validator($request->all(), [
            'registration_url' => ['required', 'url', 'max:2048'],
            'information_url' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'registration_url.required' => 'Link formulir PPDB wajib diisi.',
            'registration_url.url' => 'Link formulir PPDB tidak valid.',
            'registration_url.max' => 'Link formulir PPDB terlalu panjang.',
            'information_url.url' => 'Link info, brosur, atau PDF PPDB tidak valid.',
            'information_url.max' => 'Link info, brosur, atau PDF PPDB terlalu panjang.',
        ]);

        $validator->after(function (Validator $validator) use ($request): void {
            foreach ([
                'registration_url' => 'Link formulir PPDB',
                'information_url' => 'Link info, brosur, atau PDF PPDB',
            ] as $field => $label) {
                $url = trim((string) $request->input($field));

                if ($url !== '' && ! $this->isPublicUrl($url)) {
                    $validator->errors()->add($field, $label.' harus URL publik, bukan localhost, IP lokal, login, atau halaman admin.');
                }
            }
        });

        $validated = $validator->validate();

        $setting = $this->currentSetting();
        $setting->update([
            'registration_url' => trim((string) $validated['registration_url']),
            'information_url' => $this->nullableText($validated['information_url'] ?? null),
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

    public function storeShowcaseItem(Request $request): RedirectResponse
    {
        $data = $this->validatedShowcaseData($request);
        [$data, $newPath] = $this->applyShowcaseMedia($request, $data);
        $data['sort_order'] = $this->nextShowcaseSortOrder($data['audience']);

        try {
            PpdbShowcaseItem::query()->create($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPublicPath($newPath);

            throw $exception;
        }

        $this->normalizeShowcaseSortOrders($data['audience']);

        return $this->redirectToShowcase()->with('success', 'Item konten PPDB berhasil ditambahkan.');
    }

    public function editShowcaseItem(PpdbShowcaseItem $ppdbShowcaseItem): View
    {
        return $this->editView($ppdbShowcaseItem, 'edit');
    }

    public function updateShowcaseItem(Request $request, PpdbShowcaseItem $ppdbShowcaseItem): RedirectResponse
    {
        $oldAudience = $ppdbShowcaseItem->audience;
        $oldMediaUrl = $ppdbShowcaseItem->media_url;
        $data = $this->validatedShowcaseData($request, $ppdbShowcaseItem);
        [$data, $newPath, $replacesStoredFile] = $this->applyShowcaseMedia($request, $data, $ppdbShowcaseItem);

        if ($oldAudience !== $data['audience']) {
            $data['sort_order'] = $this->nextShowcaseSortOrder($data['audience']);
        }

        try {
            $ppdbShowcaseItem->update($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPublicPath($newPath);

            throw $exception;
        }

        if ($replacesStoredFile) {
            $this->deleteStoredPublicFile($oldMediaUrl);
        }

        $this->normalizeShowcaseSortOrders($oldAudience);
        $this->normalizeShowcaseSortOrders($data['audience']);

        return $this->redirectToShowcase()->with('success', 'Item konten PPDB berhasil diperbarui.');
    }

    public function destroyShowcaseItem(PpdbShowcaseItem $ppdbShowcaseItem): RedirectResponse
    {
        $audience = $ppdbShowcaseItem->audience;
        $ppdbShowcaseItem->delete();

        $this->normalizeShowcaseSortOrders($audience);

        return $this->redirectToShowcase()->with('success', 'Item konten PPDB berhasil dihapus.');
    }
}
