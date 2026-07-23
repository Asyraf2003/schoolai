<?php

use App\Support\HeroVideoUrl;

it('accepts direct HTTPS hero videos and rejects YouTube or unsafe URLs', function (): void {
    expect(HeroVideoUrl::normalize('https://www.youtube.com/watch?v=kb1dXcf3QQs'))
        ->toBeNull()
        ->and(HeroVideoUrl::normalize('https://youtu.be/kb1dXcf3QQs'))
        ->toBeNull()
        ->and(HeroVideoUrl::normalize('https://8.8.8.8/video.mp4'))
        ->toBe('https://8.8.8.8/video.mp4')
        ->and(HeroVideoUrl::normalize('javascript:alert(1)'))
        ->toBeNull()
        ->and(HeroVideoUrl::normalize('http://example.com/video.mp4'))
        ->toBeNull();
});
