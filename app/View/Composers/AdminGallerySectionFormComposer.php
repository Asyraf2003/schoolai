<?php

namespace App\View\Composers;

use App\View\Presenters\AdminLanguageTabsPresenter;
use Illuminate\View\View;

final class AdminGallerySectionFormComposer
{
    public function __construct(private readonly AdminLanguageTabsPresenter $languageTabs) {}

    public function compose(View $view): void
    {
        $data = $view->getData();
        $section = $data['section'];
        $isEdit = ($data['mode'] ?? 'create') === 'edit';
        $continueToMedia = ! $isEdit && (bool) ($data['continueToMedia'] ?? false);

        $view->with([
            'isEdit' => $isEdit,
            'continueToMedia' => $continueToMedia,
            'action' => $isEdit ? route('admin.galeri.sections.update', $section) : route('admin.galeri.sections.store'),
            'formTitle' => $isEdit ? 'Edit Bagian Galeri' : ($continueToMedia ? 'Tambah Galeri' : 'Tambah Bagian Galeri'),
            'languageCompletion' => $this->languageTabs->completion([
                'id' => [old('title_id', $section->title_id)],
                'en' => [old('title_en', $section->title_en)],
                'ar' => [old('title_ar', $section->title_ar)],
            ]),
            'activeLanguage' => $this->languageTabs->activeLanguage(
                $data['errors'] ?? null,
                ['title_ar', 'description_ar'],
                ['title_en', 'description_en'],
            ),
        ]);
    }
}
