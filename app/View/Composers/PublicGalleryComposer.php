<?php

namespace App\View\Composers;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\View\View;

final class PublicGalleryComposer
{
    public function __construct(private Translator $translator) {}

    public function compose(View $view): void
    {
        $viewData = $view->getData();
        $page = $viewData['page'] ?? $this->translator->get('pages.galeri');
        $page = is_array($page) ? $page : [];
        $galleryItems = $viewData['galleryItems'] ?? [];
        $galleryItems = is_array($galleryItems) ? $galleryItems : [];
        $gallerySections = $viewData['gallerySections'] ?? [];

        $view->with([
            'page' => $page,
            'items' => $galleryItems !== []
                ? $galleryItems
                : (is_array($page['items'] ?? null) ? $page['items'] : []),
            'sections' => is_array($gallerySections) ? $gallerySections : [],
        ]);
    }
}
