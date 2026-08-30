<?php

it('keeps Vision media frames at five sixths scale with a crisp directional cast shadow', function (): void {
    $base = file_get_contents(resource_path('css/pages/welcome-vision-waapi/base.css'));
    $enhanced = file_get_contents(resource_path('css/pages/welcome-vision-waapi/enhanced.css'));
    $compact = file_get_contents(resource_path('css/pages/welcome-vision-waapi/compact.css'));
    $responsive = file_get_contents(resource_path('css/pages/welcome-vision-waapi/responsive.css'));

    expect($base)
        ->toContain('width: 83.333333%;')
        ->toContain('height: clamp(15rem, 55vw, 28.333333rem);')
        ->toContain('margin-inline: auto;')
        ->toContain('--vision-media-shadow-x: clamp(14px, 1.25vw, 20px);')
        ->toContain('--vision-media-shadow-y: clamp(18px, 1.7vw, 28px);')
        ->toContain('clamp(-.5rem, -.4vw, -.25rem)')
        ->toContain('rgb(255 255 255 / .9) 0%')
        ->toContain('rgb(22 22 22 / .84) 100%')
        ->toContain('html[dir="rtl"] .vision-arch__visual')
        ->and($enhanced)
        ->toContain('.vision-arch.is-enhanced .vision-arch__visuals::before')
        ->toContain('overflow: visible;')
        ->toContain('isolation: isolate;')
        ->toContain('inset-inline-start: 8.333333%;')
        ->toContain('width: 83.333333%;')
        ->toContain('height: min(56.666667svh, 35.833333rem);')
        ->toContain('clamp(12px, 1.1vw, 18px)')
        ->toContain('clamp(-8px, -.5vw, -5px)')
        ->toContain('clamp(30px, 3vw, 46px)')
        ->not->toContain('clamp(4.5rem, 9vw, 9rem)')
        ->toContain('box-shadow: none;')
        ->toContain('html[dir="rtl"] .vision-arch.is-enhanced .vision-arch__visuals')
        ->and($compact)
        ->toContain('height: min(60svh, 23.333333rem);')
        ->toContain('height: min(51.666667svh, 28.333333rem);')
        ->toContain('border-radius: 0;')
        ->and($responsive)
        ->toContain('.vision-arch.is-enhanced .vision-arch__visuals::before')
        ->toContain('height: min(51.666667svh, 30rem);');
});
