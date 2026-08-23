<?php

namespace App\View\Composers;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\View\View;

final class HomeVisionMissionComposer
{
    public function __construct(
        private Translator $translator,
        private UrlGenerator $urlGenerator,
    ) {}

    public function compose(View $view): void
    {
        $locale = $this->translator->getLocale();
        $viewData = $view->getData();
        $visiMisi = is_array($viewData['visiMisi'] ?? null)
            ? $viewData['visiMisi']
            : [];
        $visiMisi['missions'] = collect($visiMisi['missions'] ?? [])->map(
            static function (mixed $mission) use ($locale): array {
                $mission = is_array($mission) ? $mission : [];
                $mission['text_parts'] = collect($mission['text_parts'] ?? [])->map(
                    static function (mixed $part) use ($locale): array {
                        $part = is_array($part) ? $part : [];
                        $text = (string) ($part['text'] ?? '');
                        $part['text'] = $locale === 'ar'
                            ? str_replace('ﷺ', 'صلى الله عليه وسلم', $text)
                            : $text;

                        return $part;
                    }
                )->all();

                return $mission;
            }
        )->all();
        $aboutStory = $this->translator->get('home.about_stats_story');

        $view->with([
            'visiMisi' => $visiMisi,
            'aboutStory' => is_array($aboutStory) ? $aboutStory : [],
            'aboutLabel' => $this->translator->get('home_vision.labels.about'),
            'visionLabel' => $this->translator->get('home_vision.labels.vision'),
            'missionLabel' => $this->translator->get('home_vision.labels.mission'),
            'sectionLabel' => $this->translator->get('home_vision.section_label'),
            'schoolImages' => collect(range(1, 3))->map(
                fn (int $index): string => $this->urlGenerator->asset(
                    sprintf('media/home/vision-paper-%02d.webp', $index)
                )
            ),
        ]);
    }
}
