<?php

it('mirrors Program detail with logical RTL grid flow instead of physical column overrides', function (): void {
    $wide = file_get_contents(resource_path('css/pages/welcome/program-journey/wide.css'));
    $hud = file_get_contents(resource_path('css/pages/welcome/program-journey/hud.css'));

    expect($wide)
        ->toContain('grid-template-columns: 1.5rem 30% 1fr 1.5rem')
        ->toContain(".program-kinetic__back {\n        grid-column: 2;")
        ->toContain(".program-kinetic__detail-media {\n        position: relative;\n        grid-column: 3;")
        ->toContain('html[dir="rtl"] .program-kinetic__detail-copy h3,')
        ->toContain('html[dir="rtl"] .program-kinetic__detail-description')
        ->toContain('text-align: right')
        ->not->toContain('html[dir="rtl"] .program-kinetic__detail-media')
        ->not->toContain('html[dir="rtl"] .program-kinetic__back {')
        ->not->toContain('grid-column: 1 / 3')
        ->not->toContain('padding-inline: 2rem 0')
        ->and($hud)
        ->toContain('html[dir="rtl"] .program-kinetic__back')
        ->toContain('flex-direction: row-reverse')
        ->toContain('html[dir="rtl"] .program-kinetic__back-mark')
        ->toContain('transform: scaleX(-1)');
});

it('bounds localized Program detail titles on tablet and mobile using the existing title scale', function (): void {
    $compact = file_get_contents(resource_path('css/pages/welcome/program-journey/compact.css'));

    expect($compact)
        ->toContain('@media (max-width: 1023px)')
        ->toContain('.program-kinetic__detail-copy h3[data-title-scale="short"]')
        ->toContain('font-size: clamp(2.4rem, 8vw, 5rem)')
        ->toContain('.program-kinetic__detail-copy h3[data-title-scale="medium"]')
        ->toContain('font-size: clamp(2.05rem, 6.5vw, 4rem)')
        ->toContain('.program-kinetic__detail-copy h3[data-title-scale="long"]')
        ->toContain('font-size: clamp(1.75rem, 5.5vw, 3.4rem)')
        ->toContain('text-wrap: balance')
        ->toContain('overflow-wrap: anywhere')
        ->toContain('grid-template-columns: minmax(0, 1fr)')
        ->toContain('width: min(78vw, 31rem)')
        ->toContain('@media (max-width: 639px)')
        ->toContain('width: min(82vw, 25rem)');
});
