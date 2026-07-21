<?php

function translationStructure(array $value, string $prefix = ''): array
{
    $paths = [];

    foreach ($value as $key => $item) {
        $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;
        $paths[] = $path;

        if (is_array($item)) {
            $paths = [...$paths, ...translationStructure($item, $path)];
        }
    }

    sort($paths);

    return $paths;
}

test('Indonesian English and Arabic translation groups keep identical structures', function () {
    $groups = [
        'admin',
        'app',
        'home',
        'home_parity',
        'pages',
        'runtime',
        'validation',
    ];

    foreach ($groups as $group) {
        $indonesian = require lang_path("id/{$group}.php");
        $english = require lang_path("en/{$group}.php");
        $arabic = require lang_path("ar/{$group}.php");

        expect(translationStructure($english))
            ->toBe(translationStructure($indonesian), "English structure differs from Indonesian in {$group}.php");

        expect(translationStructure($arabic))
            ->toBe(translationStructure($indonesian), "Arabic structure differs from Indonesian in {$group}.php");
    }
});
