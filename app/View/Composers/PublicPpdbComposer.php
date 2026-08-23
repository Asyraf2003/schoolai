<?php

namespace App\View\Composers;

use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

final class PublicPpdbComposer
{
    public function __construct(private Translator $translator) {}

    public function compose(View $view): void
    {
        $viewData = $view->getData();
        $admission = $viewData['ppdbAdmission'] ?? null;
        $admission = $admission instanceof PpdbSetting ? $admission : null;
        $showcaseItems = collect($viewData['ppdbShowcaseItems'] ?? []);
        $audienceLabels = [
            'parents' => $this->translator->get('runtime.ppdb.audience_parents'),
            'school' => $this->translator->get('runtime.ppdb.audience_school'),
        ];
        $showcaseByAudience = collect(array_keys($audienceLabels))->mapWithKeys(
            fn (string $audience): array => [
                $audience => $this->showcaseItems($showcaseItems, $audience),
            ]
        );
        $availableAudiences = $showcaseByAudience
            ->filter(fn (Collection $items): bool => $items->isNotEmpty())
            ->keys()
            ->values();
        $page = $this->translator->get('pages.ppdb');

        $view->with([
            'page' => is_array($page) ? $page : [],
            'ppdbAdmission' => $admission,
            'ppdbFormUrl' => $admission?->publicRegistrationUrl(),
            'ppdbInfoUrl' => $admission?->publicInformationUrl(),
            'ppdbIsOpen' => $admission?->isRegistrationOpen() ?? false,
            'ppdbRegisterButtonLabel' => $this->translator->get('runtime.ppdb.register_button'),
            'ppdbGuideButtonLabel' => $this->translator->get('runtime.ppdb.guide_button'),
            'ppdbFinalButtonLabel' => $this->translator->get('runtime.ppdb.final_button'),
            'ppdbClosedTitle' => $this->translator->get('runtime.ppdb.closed_title'),
            'ppdbClosedText' => $this->translator->get('runtime.ppdb.closed_text'),
            'ppdbClosedButton' => $this->translator->get('runtime.ppdb.closed_button'),
            'ppdbAudienceAriaLabel' => $this->translator->get('runtime.ppdb.audience_aria_label'),
            'ppdbSchoolTask' => $this->translator->get('runtime.ppdb.school_task'),
            'ppdbFamilyNote' => $this->translator->get('runtime.ppdb.family_note'),
            'ppdbClearFollowUp' => $this->translator->get('runtime.ppdb.clear_follow_up'),
            'ppdbShowcaseByAudience' => $showcaseByAudience,
            'ppdbAudienceLabels' => $audienceLabels,
            'ppdbAvailableAudiences' => $availableAudiences,
            'ppdbInitialAudience' => $availableAudiences->first() ?? 'parents',
        ]);
    }

    /**
     * @param  Collection<int, mixed>  $items
     * @return Collection<int, array<string, mixed>>
     */
    private function showcaseItems(Collection $items, string $audience): Collection
    {
        $locale = $this->translator->getLocale();

        return $items
            ->filter(
                fn (mixed $item): bool => $item instanceof PpdbShowcaseItem
                    && $item->audience === $audience
            )
            ->values()
            ->map(
                static fn (PpdbShowcaseItem $item, int $index): array => [
                    'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'title' => $item->titleForLocale($locale),
                    'description' => $item->descriptionForLocale($locale),
                    'media_url' => $item->media_url,
                    'is_video' => (bool) $item->is_video,
                ]
            );
    }
}
