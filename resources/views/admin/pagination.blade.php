@if($paginator->hasPages())
    <nav class="admin-pagination" aria-label="Halaman">
        @if($paginator->previousPageUrl())<a href="{{ $paginator->previousPageUrl() }}">← Sebelumnya</a>@endif
        <span>Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
        @if($paginator->nextPageUrl())<a href="{{ $paginator->nextPageUrl() }}">Berikutnya →</a>@endif
    </nav>
@endif
