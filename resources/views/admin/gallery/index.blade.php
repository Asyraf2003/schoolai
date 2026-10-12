@extends('admin.layout')
@section('title', 'Galeri')
@section('content')
    <div class="actions"><a class="action" href="{{ route('admin.galeri.create') }}">Tambah media</a></div>
    <div class="cards">
        @forelse($items as $item)
            <article class="card {{ $item->trashed() ? 'archived' : '' }}">
                @if($item->is_video)
                    <video controls preload="none" playsinline @if($item->thumbnail_url) poster="{{ $item->thumbnail_url }}" @endif src="{{ $item->media_url }}" aria-label="{{ $item->alt_text ?: 'Video galeri' }}"></video>
                @else
                    <img src="{{ $item->thumbnail_url ?: $item->media_url }}" alt="{{ $item->alt_text }}" loading="lazy" width="640" height="360">
                @endif
                <p>{{ $item->alt_text ?: ($item->is_video ? 'Video' : 'Foto') }}</p>
                <div class="actions">
                    @if($item->trashed())
                        <form method="POST" action="{{ route('admin.galeri.restore', $item) }}">@csrf @method('PATCH')<button type="submit">Pulihkan</button></form>
                    @else
                        <a class="action secondary" href="{{ route('admin.galeri.edit', $item) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.galeri.destroy', $item) }}">@csrf @method('DELETE')<button class="danger" type="submit">Hapus</button></form>
                    @endif
                </div>
            </article>
        @empty<p>Belum ada media.</p>@endforelse
    </div>
    @include('admin.pagination', ['paginator' => $items])
@endsection
