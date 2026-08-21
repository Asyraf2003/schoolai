<?php

it('forms Program cards before the white to blue Character handoff', function (): void {
    $entry = file_get_contents(resource_path('js/pages/welcome/program-cards.js'));
    $formation = file_get_contents(
        resource_path('js/surfaces/home/program-journey/formation.js'),
    );
    $formationCss = file_get_contents(
        resource_path('css/pages/welcome/program-journey/formation.css'),
    );
    $world = file_get_contents(
        resource_path('js/surfaces/home/program-values-world.js'),
    );
    $worldCss = file_get_contents(
        resource_path('css/surfaces/home/values/story-kinetic.css'),
    );
    $valuesShell = file_get_contents(
        resource_path('css/surfaces/home/values/story-shell.css'),
    );
    $handoff = file_get_contents(
        resource_path('css/pages/welcome-values-gallery-handoff.css'),
    );

    expect($entry)
        ->toContain("from '../../surfaces/home/program-journey/formation.js'")
        ->toContain('mountProgramFormation(root)')
        ->and($formation)
        ->toContain('CARD_START_RATIO')
        ->toContain('CARD_STEP_RATIO')
        ->toContain('REVEAL_DISTANCE_RATIO')
        ->toContain("querySelectorAll('[data-program-card]')")
        ->toContain("querySelectorAll('[data-program-open]')")
        ->toContain('getBoundingClientRect().top')
        ->toContain("window.addEventListener('scroll', sample, { passive: true })")
        ->toContain('window.requestAnimationFrame(render)')
        ->toContain('prefers-reduced-motion: reduce')
        ->not->toContain('scrollTo(')
        ->not->toContain('wheel')
        ->and($formationCss)
        ->toContain('--program-formation-hold')
        ->toContain('.program-kinetic.is-program-formation')
        ->toContain('.program-kinetic__trigger:focus-visible')
        ->and($world)
        ->toContain("querySelector('[data-program-kinetic]')")
        ->toContain('getBoundingClientRect().bottom')
        ->toContain('MORPH_START_BOTTOM_RATIO')
        ->toContain('MORPH_END_BOTTOM_RATIO')
        ->and($worldCss)
        ->toContain('#fff calc(100% - var(--program-values-morph-pct))')
        ->toContain('--program-values-final-color: #2038ff')
        ->toContain('var(--program-values-final-color) var(--program-values-morph-pct)')
        ->and($valuesShell)
        ->toContain('--values-blue: var(--program-values-final-color)')
        ->toContain('background: var(--values-blue)')
        ->toContain('opacity: var(--values-surface-detail)')
        ->and($handoff)
        ->toContain('--values-gallery-resting-color: #fffaf0')
        ->toContain('var(--program-values-final-color) 0%');
});

it('uses bounded desktop geometry for the closer Program and Values composition', function (): void {
    $wide = file_get_contents(
        resource_path('css/pages/welcome/program-journey/wide.css'),
    );
    $formation = file_get_contents(
        resource_path('css/pages/welcome/program-journey/formation.css'),
    );
    $valuesLayout = file_get_contents(
        resource_path('js/surfaces/home/values/desktop-layout.js'),
    );
    $valuesResponsive = file_get_contents(
        resource_path('css/surfaces/home/values/story-responsive.css'),
    );
    $valuesHeading = file_get_contents(
        resource_path('css/surfaces/home/values/story-heading.css'),
    );

    expect($wide)
        ->toContain('margin-block-start: clamp(-4.5rem, -5svh, -2.5rem)')
        ->toContain('--program-values-clearance: clamp(4.5rem, 11svh, 7.5rem)')
        ->and($formation)
        ->toContain('--program-formation-hold: clamp(5.5rem, 14svh, 9.5rem)')
        ->and($valuesLayout)
        ->toContain('CENTER_COLLISION_PROGRESS')
        ->toContain('SPLIT_REVEAL_PROGRESS')
        ->toContain('FAN_SETTLE_PROGRESS')
        ->toContain('const CARD_SCALE = 1')
        ->toContain('function splitPose')
        ->toContain('center.y + fanArc(index, geometry.cardHeight)')
        ->toContain('phase(progress, SPLIT_REVEAL_PROGRESS, FAN_SETTLE_PROGRESS)')
        ->not->toContain('ENTRY_DECK_LIFT_RATIO')
        ->not->toContain('stackNudge(')
        ->not->toContain('deckAngle(')
        ->not->toContain('window.scrollY');

    expect($valuesResponsive)
        ->toContain('transform: translate3d(0, 0, 0)')
        ->not->toContain('translate3d(0, clamp(-10rem, -16svh, -7rem), 0)')
        ->and($valuesHeading)
        ->toContain('margin-block-start: clamp(-10rem, -15svh, -7rem)');
});
