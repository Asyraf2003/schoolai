<?php

namespace App\View\Composers;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\View\View;

final class HomeVisionMissionComposer
{
    public function __construct(
        private Translator $translator,
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
        $aboutLabel = $this->translator->get('home_vision.labels.about');
        $visionLabel = $this->translator->get('home_vision.labels.vision');
        $missionLabel = $this->translator->get('home_vision.labels.mission');

        $view->with([
            'visiMisi' => $visiMisi,
            'aboutStory' => is_array($aboutStory) ? $aboutStory : [],
            'aboutLabel' => $aboutLabel,
            'visionLabel' => $visionLabel,
            'missionLabel' => $missionLabel,
            'sectionLabel' => $this->translator->get('home_vision.section_label'),
            'schoolImages' => collect(config('media.static.vision', []))
                ->filter(
                    static fn (mixed $url): bool =>
                        is_string($url) && trim($url) !== ''
                )
                ->values(),
            'visionVideoSources' => [
                0 => [
                    'url' => (string) config('media.homepage_about_video_url'),
                    'label' => $aboutLabel,
                    'interactive' => true,
                ],
                1 => [
                    'url' => (string) config('media.homepage_vision_video_url'),
                    'label' => $visionLabel,
                    'interactive' => false,
                ],
                2 => [
                    'url' => (string) config('media.homepage_mission_video_url'),
                    'label' => $missionLabel,
                    'interactive' => true,
                ],
            ],
        ]);
    }
}
