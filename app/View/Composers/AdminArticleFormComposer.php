<?php

namespace App\View\Composers;

use App\View\Presenters\AdminLanguageTabsPresenter;
use Illuminate\View\View;

final class AdminArticleFormComposer
{
    public function __construct(private readonly AdminLanguageTabsPresenter $languageTabs) {}

    public function compose(View $view): void
    {
        $data = $view->getData();
        $article = $data['article'];
        $isEdit = ($data['mode'] ?? 'create') === 'edit';

        $view->with([
            'isEdit' => $isEdit,
            'action' => $isEdit ? route('admin.artikel.update', $article) : route('admin.artikel.store'),
            'publishedAtValue' => old('published_at', optional($article->published_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')),
            'languageCompletion' => $this->languageTabs->completion([
                'id' => [old('title_id', $article->title_id), old('link_id', $article->link_id)],
                'en' => [old('title_en', $article->title_en), old('link_en', $article->link_en)],
                'ar' => [old('title_ar', $article->title_ar), old('link_ar', $article->link_ar)],
            ]),
            'activeLanguage' => $this->languageTabs->activeLanguage(
                $data['errors'] ?? null,
                ['title_ar', 'description_ar', 'link_ar'],
                ['title_en', 'description_en', 'link_en'],
            ),
        ]);
    }
}
