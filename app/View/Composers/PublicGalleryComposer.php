<?php

namespace App\View\Composers;

use App\View\Presenters\GalleryWallCardPresenter;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\View\View;

final class PublicGalleryComposer
{
    public function __construct(
        private Translator $translator,
        private GalleryWallCardPresenter $cardPresenter,
    ) {}

    public function compose(View $view): void
    {
        $viewData = $view->getData();
        $page = $viewData['page'] ?? $this->translator->get('pages.galeri');
        $page = is_array($page) ? $page : [];
        $galleryItems = $viewData['galleryItems'] ?? [];
        $galleryItems = is_array($galleryItems) ? $galleryItems : [];
        $gallerySections = $viewData['gallerySections'] ?? [];
        $gallerySections = is_array($gallerySections) ? $gallerySections : [];
        $items = $galleryItems !== []
            ? $galleryItems
            : (is_array($page['items'] ?? null) ? $page['items'] : []);

        $view->with([
            'page' => $page,
            'items' => $items,
            'sections' => $gallerySections,
            'galleryCategories' => $this->galleryCategories($items, $gallerySections),
        ]);
    }

    private function galleryCategories(array $items, array $sections): array
    {
        $allItems = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $allItems[$this->itemKey($item)] = $this->presentItem($item);
        }

        $sectionCategories = [];
        foreach ($sections as $index => $section) {
            if (! is_array($section)) {
                continue;
            }

            $sectionItems = is_array($section['items'] ?? null) ? $section['items'] : [];
            if ($sectionItems === []) {
                continue;
            }

            $presentedItems = [];
            foreach ($sectionItems as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $presented = $this->presentItem($item);
                $presentedItems[] = $presented;
                $allItems[$this->itemKey($item)] = $presented;
            }

            if ($presentedItems === []) {
                continue;
            }

            $title = trim((string) ($section['title'] ?? ''));
            $sectionCategories[] = [
                'anchor' => (string) ($section['anchor'] ?? ('gallery-section-'.($section['id'] ?? $index))),
                'title' => $title !== '' ? $title : 'Index',
                'items' => $presentedItems,
            ];
        }

        return array_merge([[
            'anchor' => 'gallery-all',
            'title' => 'All',
            'items' => array_values($allItems),
        ]], $sectionCategories);
    }

    private function presentItem(array $item): array
    {
        $presented = $this->cardPresenter->present($item);

        return [
            'title' => trim((string) ($presented['title'] ?: $presented['label'])),
            'mediaUrl' => (string) $presented['mediaUrl'],
            'thumbnailUrl' => (string) ($presented['thumbnailUrl'] ?: $presented['mediaUrl']),
            'isVideo' => (bool) $presented['isVideo'],
            'isDirectVideo' => (bool) $presented['isDirectVideo'],
            'emoji' => (string) $presented['emoji'],
        ];
    }

    private function itemKey(array $item): string
    {
        $key = trim((string) ($item['media_url'] ?? $item['thumbnail_url'] ?? ''));

        return $key !== ''
            ? $key
            : (string) ($item['type'] ?? 'photo').'|'.(string) ($item['title'] ?? $item['label'] ?? '');
    }
}
