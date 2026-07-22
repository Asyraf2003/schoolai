<?php

use App\Support\HeroVideoUrl;

it('normalizes supported youtube hero urls and rejects unsafe schemes', function (): void {
    expect(HeroVideoUrl::normalize('https://youtu.be/kb1dXcf3QQs'))
        ->toContain('youtube-nocookie.com/embed/kb1dXcf3QQs')
        ->and(HeroVideoUrl::normalize('https://www.youtube.com/watch?v=kb1dXcf3QQs'))
        ->toContain('playlist=kb1dXcf3QQs')
        ->and(HeroVideoUrl::normalize('javascript:alert(1)'))
        ->toBeNull()
        ->and(HeroVideoUrl::normalize('http://example.com/video.mp4'))
        ->toBeNull();
});
