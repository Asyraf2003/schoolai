<?php

$home = require __DIR__.'/home.php';
$parity = require __DIR__.'/home_parity.php';
$labels = require __DIR__.'/home_vision.php';

return [
    'section_label' => $labels['section_label'],
    'open_video' => 'Putar video :story',
    'close_video' => 'Tutup video',
    'stories' => [
        'about' => [
            'label' => $labels['labels']['about'],
            'headline' => [$home['about_stats_story']['headline_line_one'], $home['about_stats_story']['headline_line_two']],
            'body' => $home['about_stats_story']['description'],
        ],
        'vision' => [
            'label' => $labels['labels']['vision'],
            'headline' => [$home['visi_misi']['vision']['title']],
            'body' => implode('', array_column($home['visi_misi']['vision']['text_parts'], 'text')),
        ],
        'mission' => [
            'label' => $labels['labels']['mission'],
            'headline' => [$parity['visi_misi']['missions_intro']['title']],
            'items' => array_map(static fn (array $item): array => [
                'title' => $item['title'],
                'body' => implode('', array_column($item['text_parts'], 'text')),
            ], $parity['visi_misi']['missions']),
        ],
    ],
];
