@extends('portal.shell')
@section('title', __('pages.common.nav.galeri'))
@section('content')
    @include('pages.partials.navigation')
    <h1>{{ __('pages.common.nav.galeri') }}</h1>
    <div class="public-grid" data-public-gallery>
        @forelse($items as $item)
            @if(\App\Support\PublicUrl::isSafe($item->media_url))
                @php($thumbnail = \App\Support\PublicUrl::normalize((string) $item->thumbnail_url))
                <figure class="public-card">
                    @if($item->is_video)
                        <video controls playsinline preload="none" src="{{ $item->media_url }}" @if($thumbnail) poster="{{ $thumbnail }}" @endif aria-label="{{ $item->alt_text ?: __('pages.common.gallery_video_title') }}"></video>
                    @else
                        <a href="{{ $item->media_url }}" aria-label="{{ $item->alt_text ?: __('pages.common.media_photo') }}"><img src="{{ $thumbnail ?: $item->media_url }}" alt="{{ $item->alt_text ?? '' }}" width="1200" height="675" loading="lazy" decoding="async"></a>
                    @endif
                </figure>
            @endif
        @empty
            <p>{{ __('public_content.gallery_empty') }}</p>
        @endforelse
    </div>
    @include('pages.partials.pagination', ['paginator' => $items])
@endsection
