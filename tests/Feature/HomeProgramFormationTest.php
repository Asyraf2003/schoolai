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
        ->toContain("prefers-reduced-motion: reduce")
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
        ->toContain('#2038ff var(--program-values-morph-pct)');
});
