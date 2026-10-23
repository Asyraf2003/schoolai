@extends('admin.layout')
@section('title', 'Artikel')
@section('description', 'Buat dan kelola informasi sekolah dalam tiga bahasa.')
@section('content')
    <div class="admin-toolbar">
        <h2>Artikel sekolah</h2>
        <x-admin.button :href="route('admin.artikel.create')">+ Tambah artikel</x-admin.button>
    </div>
    <div class="admin-grid">
        @forelse($articles as $article)
            <x-admin.card>
                <div class="admin-card-heading">
                    <div>
                        <h2>{{ $article->admin_title }}</h2>
                        <p class="admin-card-subtitle">{{ $article->published_date?->format('d M Y') ?? 'Tanggal tidak diatur' }}</p>
                    </div>
                    <x-admin.badge :tone="$article->trashed() ? 'danger' : 'success'">{{ $article->trashed() ? 'Diarsipkan' : 'Aktif' }}</x-admin.badge>
                </div>
                <p class="admin-card-subtitle">{{ $article->descriptionForLocale('id') ?: 'Konten artikel tersedia untuk diedit.' }}</p>
                <div class="admin-form-actions">
                    @if($article->trashed())
                        <form method="POST" action="{{ route('admin.artikel.restore', $article) }}">
                            @csrf @method('PATCH')
                            <x-admin.button type="submit" variant="secondary">Pulihkan</x-admin.button>
                        </form>
                    @else
                        <x-admin.button :href="route('admin.artikel.edit', $article)" variant="secondary">Edit</x-admin.button>
                        <details class="admin-subdisclosure">
                            <summary>Arsipkan</summary>
                            <form method="POST" action="{{ route('admin.artikel.destroy', $article) }}">
                                @csrf @method('DELETE')
                                <x-admin.button type="submit" variant="danger">Konfirmasi arsip</x-admin.button>
                            </form>
                        </details>
                    @endif
                </div>
            </x-admin.card>
        @empty
            <x-admin.card class="admin-empty"><h2>Belum ada artikel</h2><p>Mulai dengan membuat artikel pertama sekolah.</p></x-admin.card>
        @endforelse
    </div>
    @include('admin.pagination', ['paginator' => $articles])
@endsection
