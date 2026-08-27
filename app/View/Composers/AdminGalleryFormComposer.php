<?php

namespace App\View\Composers;

use App\View\Presenters\AdminLanguageTabsPresenter;
use Illuminate\View\View;

final class AdminGalleryFormComposer
{
    public function __construct(private readonly AdminLanguageTabsPresenter $languageTabs) {}

    public function compose(View $view): void
    {
        $data = $view->getData();
        $item = $data['item'];
        $page = __('admin.gallery');
        $isEdit = ($data['mode'] ?? 'create') === 'edit';
        $currentType = old('type', $item->type ?: 'photo');
        $selectedSectionIds = old(
            'section_ids',
            $item->exists ? $item->sections->modelKeys() : [],
        );

        $view->with([
            'page' => $page,
            'form' => $page['form'],
            'isEdit' => $isEdit,
            'action' => $isEdit ? route('admin.galeri.update', $item) : route('admin.galeri.store'),
            'publishedAtValue' => old('published_at', optional($item->published_at)->format('Y-m-d\TH:i')),
            'currentType' => $currentType,
            'isVideo' => $currentType === 'video',
            'selectedSectionIds' => array_map('intval', $selectedSectionIds),
            'languageCompletion' => $this->languageTabs->completion([
                'id' => [old('title_id', $item->title_id ?: $item->title), old('category_id', $item->category_id ?: $item->category)],
                'en' => [old('title_en', $item->title_en), old('category_en', $item->category_en)],
                'ar' => [old('title_ar', $item->title_ar), old('category_ar', $item->category_ar)],
            ]),
            'activeLanguage' => $this->languageTabs->activeLanguage(
                $data['errors'] ?? null,
                ['title_ar', 'category_ar', 'caption_ar'],
                ['title_en', 'category_en', 'caption_en'],
            ),
        ]);
    }
}
