<section class="gallery-section gallery-section--{{ $composition }}" id="{{ $sectionId }}" aria-labelledby="{{ $sectionId }}-title">
    <div class="gallery-wrap">
        <div class="gallery-section__heading"><h2 id="{{ $sectionId }}-title"><sup>{{ $number }}</sup>{{ $label }}</h2><p>{{ $description }}</p></div>
        <div class="gallery-composition gallery-composition--{{ $composition }}" @if($sectionId === 'all-posts') data-public-gallery @elseif($sectionId === 'instagram') data-public-instagram-gallery @endif>
            @forelse($paginator as $item)
                @include('pages.partials.gallery-card', ['item' => $item, 'position' => $loop->iteration, 'priority' => $sectionId === 'all-posts' && $loop->first])
            @empty
                <p class="gallery-empty">{{ __('public_content.gallery_empty') }}</p>
            @endforelse
        </div>
        @if($paginator->hasPages())
            <nav class="gallery-pagination" aria-label="{{ $label }} — {{ __('public_content.pagination') }}">
                @if($paginator->previousPageUrl())<a href="{{ $paginator->previousPageUrl() }}" rel="prev"><span aria-hidden="true">←</span> {{ __('public_content.previous') }}</a>@endif
                <span>{{ str_pad((string) $paginator->currentPage(), 2, '0', STR_PAD_LEFT) }} / {{ str_pad((string) $paginator->lastPage(), 2, '0', STR_PAD_LEFT) }}</span>
                @if($paginator->nextPageUrl())<a href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('public_content.next') }} <span aria-hidden="true">→</span></a>@endif
            </nav>
        @endif
    </div>
</section>
