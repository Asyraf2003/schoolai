<?php

it('keeps Values from intercepting Program controls during shared-world overlap', function (): void {
    $world = file_get_contents(resource_path('css/surfaces/home/values/story-kinetic.css'));

    expect($world)
        ->toContain(".program-values-world > .values-story {\n    pointer-events: none;")
        ->toContain('.program-values-world > .program-kinetic.is-transitioning,')
        ->toContain('.program-values-world > .program-kinetic.is-detail-open')
        ->toContain('z-index: 3;');
});
