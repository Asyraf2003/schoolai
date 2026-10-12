@if($paginator->hasPages())
    <nav class="public-pagination" aria-label="{{ __('public_content.pagination') }}">
        @if($paginator->previousPageUrl())<a href="{{ $paginator->previousPageUrl() }}" rel="prev">{{ __('public_content.previous') }}</a>@endif
        <span>{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
        @if($paginator->nextPageUrl())<a href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('public_content.next') }}</a>@endif
    </nav>
@endif
