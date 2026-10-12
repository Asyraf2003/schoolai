@extends('admin.layout')
@section('title', 'Artikel')
@section('content')
    <div class="actions"><a class="action" href="{{ route('admin.artikel.create') }}">Tambah artikel</a></div>
    <div class="cards">
        @forelse($articles as $article)
            <article class="card {{ $article->trashed() ? 'archived' : '' }}">
                <h2>{{ $article->admin_title }}</h2>
                @if($article->published_date)<p>{{ $article->published_date->format('d/m/Y') }}</p>@endif
                <p>{{ $article->descriptionForLocale('id') }}</p>
                <div class="actions">
                    @if($article->trashed())
                        <form method="POST" action="{{ route('admin.artikel.restore', $article) }}">@csrf @method('PATCH')<button type="submit">Pulihkan</button></form>
                    @else
                        <a class="action secondary" href="{{ route('admin.artikel.edit', $article) }}">Edit</a>
                        <a href="{{ $article->linkForLocale('id') }}">Lihat</a>
                        <form method="POST" action="{{ route('admin.artikel.destroy', $article) }}">@csrf @method('DELETE')<button class="danger" type="submit">Hapus</button></form>
                    @endif
                </div>
            </article>
        @empty<p>Belum ada artikel.</p>@endforelse
    </div>
    @include('admin.pagination', ['paginator' => $articles])
@endsection
