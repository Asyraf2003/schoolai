<?php

it('keeps Vision media frames at five sixths scale with visible directional shadows', function (): void {
    $base = file_get_contents(resource_path('css/pages/welcome-vision-waapi/base.css'));
    $enhanced = file_get_contents(resource_path('css/pages/welcome-vision-waapi/enhanced.css'));

    expect($base)
        ->toContain('width: 83.333333%;')
        ->toContain('height: clamp(15rem, 55vw, 28.333333rem);')
        ->toContain('margin-inline: auto;')
        ->toContain('var(--vision-media-shadow-x)')
        ->toContain('var(--vision-media-shadow-y)')
        ->toContain('rgb(0 0 0 / .24)')
        ->toContain('html[dir="rtl"] .vision-arch__visual')
        ->and($enhanced)
        ->toContain('inset-inline-start: 8.333333%;')
        ->toContain('inset-inline-end: auto;')
        ->toContain('width: 83.333333%;')
        ->toContain('height: min(56.666667svh, 35.833333rem);');
});
