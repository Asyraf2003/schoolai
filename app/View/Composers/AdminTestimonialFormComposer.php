<?php

namespace App\View\Composers;

use Illuminate\View\View;

final class AdminTestimonialFormComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $item = $data['item'];
        $isEdit = ($data['mode'] ?? 'create') === 'edit';
        $currentType = old('type', $item->type ?: 'photo');

        $view->with([
            'isEdit' => $isEdit,
            'action' => $isEdit ? route('admin.testimoni.update', $item) : route('admin.testimoni.store'),
            'currentType' => $currentType,
            'currentSource' => $currentType === 'photo' ? 'upload' : old('source', $item->source ?: 'upload'),
        ]);
    }
}
