<?php

it('keeps homepage education and facilities structurally aligned across ID EN and AR', function (): void {
    $expectedTitles = [
        'id' => ['Qur’ani', 'Inspiratif', 'Inovatif', 'Integritas'],
        'en' => ['Quranic', 'Inspiration', 'Innovation', 'Integrity'],
        'ar' => ['قرآني', 'الإلهام', 'الابتكار', 'النزاهة'],
    ];
    $expectedSubtitles = [
        'id' => 'Q-III adalah empat pondasi karakter Al-Mustaqbal: Qur’ani, inspiratif, inovatif, dan berintegritas.',
        'en' => 'Q-III represents four character foundations at Al-Mustaqbal: Quranic, Inspiration, Innovation, and Integrity.',
        'ar' => 'تمثل Q-III أربع ركائز للشخصية في مدرسة المستقبل: القرآن، والإلهام، والابتكار، والنزاهة.',
    ];
    $shapes = [];

    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);
        $base = __('home');
        $parity = __('home_parity');
        $program = __('home_program');
        $home = array_replace_recursive(
            is_array($base) ? $base : [],
            is_array($parity) ? $parity : [],
        );
        $program = is_array($program) ? $program : [];

        expect($home['visi_misi']['missions'])->toHaveCount(5)
            ->and($home['nilai_sekolah']['items'])->toHaveCount(4)
            ->and($home['galeri']['items'])->toHaveCount(9)
            ->and($program['items'])->toHaveCount(19)
            ->and(array_column($home['nilai_sekolah']['items'], 'title'))->toBe($expectedTitles[$locale])
            ->and($home['nilai_sekolah']['subtitle'])->toBe($expectedSubtitles[$locale]);

        $shapes[$locale] = [
            'missions' => array_map(
                static fn (array $item): array => [
                    'keys' => array_keys($item),
                    'text_parts' => count($item['text_parts'] ?? []),
                ],
                $home['visi_misi']['missions'],
            ),
            'values' => array_map(
                static fn (array $item): array => [
                    'keys' => array_keys($item),
                    'text_parts' => count($item['text_parts'] ?? []),
                ],
                $home['nilai_sekolah']['items'],
            ),
            'facilities' => array_map(
                static fn (array $item): array => array_keys($item),
                $home['galeri']['items'],
            ),
            'programs' => array_map(
                static fn (array $item): array => array_keys($item),
                $program['items'],
            ),
        ];
    }

    expect($shapes['id'])->toBe($shapes['en'])
        ->and($shapes['id'])->toBe($shapes['ar']);
});

it('hands testimonials off from the actual final gallery background', function (): void {
    $gallery = file_get_contents(resource_path('js/pages/welcome-depth-gallery.js'));
    $testimonials = file_get_contents(resource_path('css/pages/welcome-testimonial-wall.css'));

    expect($gallery)
        ->toContain("const finalBackground = items[items.length - 1]?.dataset.galleryBackground || ''")
        ->toContain("page.style.setProperty('--gallery-story-final-bg', finalBackground)")
        ->and($testimonials)
        ->toContain('var(--gallery-story-final-bg, var(--gallery-story-bg, #6f9b72))')
        ->not->toContain('linear-gradient(180deg, var(--gallery-story-bg, #6f9b72) 0%');
});

it('keeps Q-III card typography larger while preserving Arabic shaping', function (): void {
    $cards = file_get_contents(resource_path('css/surfaces/home/values/story-cards.css'));

    expect($cards)
        ->toContain('font-size: clamp(.94rem, 4.7cqi, 1.18rem)')
        ->toContain('font-size: clamp(1.9rem, 9.2cqi, 2.85rem)')
        ->toContain('font-size: clamp(.98rem, 4.55cqi, 1.15rem)')
        ->toContain('html[lang="ar"] .values-card__title[data-text-role="component-title"]')
        ->toContain('line-height: 1.12')
        ->not->toContain('font-size: clamp(1.6rem, 8cqi, 2.4rem)');
});
