<?php

namespace App\View\Composers;

use App\Models\GalleryPageSection;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class HomeGalleryComposer
{
    public function __construct(private Translator $translator) {}

    public function compose(View $view): void
    {
        if ($view->name() === 'home.sections.gallery') {
            $view->with([
                'galleryHeading' => $this->translator->get('home_presentation.gallery_heading'),
                'galleryTeasers' => $this->galleryTeasers(),
            ]);

            return;
        }

        $view->with([
            'depthItems' => collect(),
            'depthCta' => [],
            'hasDepthCta' => false,
            'galleryMoreLabel' => $this->translator->get('home_presentation.gallery_more'),
            'depthEndSteps' => 0,
            'depthJourneyCount' => 1,
            'depthClosingMedia' => collect(),
            'depthClosingCopy' => '',
        ]);
    }

    /** @return array<int, array{title: string, href: string, index: string}> */
    private function galleryTeasers(): array
    {
        $page = $this->translator->get('pages.galeri');
        $page = is_array($page) ? $page : [];
        $mainTitle = trim((string) ($page['wall']['title'] ?? $page['title'] ?? ''));

        if ($mainTitle === '') {
            $mainTitle = (string) $this->translator->get('home_presentation.gallery_heading');
        }

        $teasers = [[
            'title' => $mainTitle,
            'href' => route('galeri').'#gallery-main',
            'index' => '01',
        ]];

        if (
            Schema::hasTable('gallery_page_sections')
            && Schema::hasTable('gallery_item_gallery_page_section')
            && Schema::hasTable('gallery_items')
        ) {
            $locale = $this->translator->getLocale();
            $sections = GalleryPageSection::query()
                ->where('is_published', true)
                ->whereHas('items', fn ($query) => $query
                    ->where('gallery_items.is_published', true)
                    ->where('gallery_item_gallery_page_section.is_published', true))
                ->orderBy('id')
                ->limit(2)
                ->get();

            foreach ($sections as $section) {
                $teasers[] = [
                    'title' => $section->titleForLocale($locale),
                    'href' => route('galeri').'#gallery-section-'.$section->id,
                    'index' => str_pad((string) count($teasers) + 1, 2, '0', STR_PAD_LEFT),
                ];
            }
        }

        return array_slice($teasers, 0, 3);
    }
}
