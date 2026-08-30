<?php

it('keeps Vision media frames at five sixths scale with an unclipped Apple-style cast shadow', function (): void {
    $base = file_get_contents(resource_path('css/pages/welcome-vision-waapi/base.css'));
    $enhanced = file_get_contents(resource_path('css/pages/welcome-vision-waapi/enhanced.css'));

    expect($base)
        ->toContain('width: 83.333333%;')
        ->toContain('height: clamp(15rem, 55vw, 28.333333rem);')
        ->toContain('margin-inline: auto;')
        ->toContain('--vision-media-shadow-x: clamp(18px, 2vw, 32px);')
        ->toContain('--vision-media-shadow-y: clamp(22px, 3vw, 46px);')
        ->toContain('rgb(0 0 0 / .3)')
        ->toContain('html[dir="rtl"] .vision-arch__visual')
        ->and($enhanced)
        ->toContain('.vision-arch.is-enhanced .vision-arch__visuals::before')
        ->toContain('overflow: visible;')
        ->toContain('isolation: isolate;')
        ->toContain('inset-inline-start: 8.333333%;')
        ->toContain('width: 83.333333%;')
        ->toContain('height: min(56.666667svh, 35.833333rem);')
        ->toContain('rgb(0 0 0 / .32)')
        ->toContain('rgb(0 0 0 / .2)')
        ->toContain('box-shadow: none;')
        ->toContain('html[dir="rtl"] .vision-arch.is-enhanced .vision-arch__visuals');
});
