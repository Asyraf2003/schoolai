<?php

namespace App\View\Composers;

use Illuminate\View\View;

final class AdminHeroFormComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $slide = $data['slide'];
        $isEdit = ($data['mode'] ?? 'create') === 'edit';

        $view->with([
            'isEdit' => $isEdit,
            'action' => $isEdit ? route('admin.hero.update', $slide) : route('admin.hero.store'),
            'currentType' => old('type', $slide->type ?: 'image'),
            'currentArticleId' => (int) old('article_id', $slide->article_id),
        ]);
    }
}
