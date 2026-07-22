<?php

use App\Support\HeroVideoUrl;

it('normalizes supported youtube hero urls as non-interactive background playback', function (): void {
    $youtube = HeroVideoUrl::normalize('https://www.youtube.com/watch?v=kb1dXcf3QQs');

    expect($youtube)
        ->toContain('youtube-nocookie.com/embed/kb1dXcf3QQs')
        ->toContain('autoplay=1')
        ->toContain('mute=1')
        ->toContain('controls=0')
        ->toContain('disablekb=1')
        ->toContain('fs=0')
        ->toContain('iv_load_policy=3')
        ->toContain('loop=0')
        ->not->toContain('playlist=');

    expect(HeroVideoUrl::normalize('https://youtu.be/kb1dXcf3QQs'))
        ->toContain('youtube-nocookie.com/embed/kb1dXcf3QQs')
        ->and(HeroVideoUrl::normalize('javascript:alert(1)'))
        ->toBeNull()
        ->and(HeroVideoUrl::normalize('http://example.com/video.mp4'))
        ->toBeNull();
});
