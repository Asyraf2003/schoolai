@php
    $pool = array_keys($gallery['items']);
    $passThree = [7, 2, 9, 5, 0, 8, 4, 6, 3, 1];
    $pickRandom = static function (array $avoid) use ($pool): int {
        $candidates = array_values(array_diff($pool, $avoid));
        if ($candidates === []) {
            $candidates = $pool;
        }

        return $candidates[random_int(0, count($candidates) - 1)];
    };
    $leftRow9 = $pickRandom([$passThree[3], $passThree[4]]);
    $rightRow9 = $pickRandom([$passThree[9], $passThree[4], $leftRow9]);
    $leftRow10 = $pickRandom([$leftRow9, $passThree[5]]);
    $rightRow10 = $pickRandom([$rightRow9, $passThree[5], $leftRow10]);
    $mediaSequences = [
        $pool,
        [8, 4, 1, 6, 9, 3, 7, 2, 0, 5],
        $passThree,
        [$leftRow9, $rightRow9, $leftRow10, $rightRow10],
    ];
@endphp
<section class="gallery" id="galeri" data-gallery aria-labelledby="gallery-title">
    <header class="gallery__header">
        <h2 id="gallery-title" data-gallery-heading aria-label="{{ $gallery['title'] }}">
            <span class="gallery__heading-clip"><span data-gallery-heading-line aria-hidden="true">{{ $gallery['title'] }}</span></span>
        </h2>
    </header>
    <div class="gallery__stage" data-gallery-stage>
        <div class="gallery__composition" data-gallery-composition>
            @foreach ($mediaSequences as $sequence)
                @foreach ($sequence as $index)
                    @php($item = $gallery['items'][$index])
                    <figure class="gallery__item" data-gallery-item data-media-type="{{ $item['type'] }}"
                            @unless ($loop->parent->first) aria-hidden="true" @endunless>
                        <img src="{{ $item['poster'] }}" alt="{{ $loop->parent->first ? $item['title'] : '' }}" width="1600" height="900" loading="lazy" decoding="async" data-gallery-poster>
                        @if ($item['type'] === 'video')
                            <video data-gallery-video data-src="{{ $item['url'] }}" preload="none" muted loop playsinline aria-hidden="true"></video>
                        @endif
                        @if ($loop->parent->first)
                            <figcaption class="sr-only">{{ $item['title'] }}</figcaption>
                        @endif
                    </figure>
                @endforeach
            @endforeach
        </div>
        <p class="gallery__statement" data-gallery-statement>
            <span class="sr-only">{{ implode(' ', __('gallery_v2.statement_lines')) }}</span>
            @foreach (__('gallery_v2.statement_lines') as $line)
                <span class="gallery__statement-line" aria-hidden="true">@foreach (explode(' ', $line) as $word)<span data-gallery-word>{{ $word }}</span>{{ $loop->last ? '' : ' ' }}@endforeach</span>
            @endforeach
        </p>
    </div>
</section>
