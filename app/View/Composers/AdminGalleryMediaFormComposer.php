<?php

namespace App\View\Composers;

use Illuminate\View\View;

final class AdminGalleryMediaFormComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $item = $data['item'];
        $isEdit = ($data['mode'] ?? 'create') === 'edit';
        $currentType = old('type', $item->type ?: 'photo');

        $view->with([
            'isEdit' => $isEdit,
            'action' => $isEdit
                ? route('admin.galeri.section-media.update', $item)
                : route('admin.galeri.section-media.store', $data['section']),
            'publishedAtValue' => old('published_at', optional($item->published_at)->format('Y-m-d\TH:i')),
            'currentType' => $currentType,
            'isVideo' => $currentType === 'video',
        ]);
    }
}
