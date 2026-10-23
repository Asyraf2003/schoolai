@extends('admin.layout')
@section('title', 'Galeri')
@section('description', 'Kelola foto dan video sekolah yang disimpan melalui Cloudflare R2.')
@section('content')
    <div class="admin-toolbar">
        <h2>Media sekolah</h2>
        <x-admin.button :href="route('admin.galeri.create')">+ Tambah media</x-admin.button>
    </div>
    <div class="admin-grid">
        @forelse($items as $item)
            <x-admin.card>
                @if($item->thumbnail_url || $item->type === 'photo')
                    <div class="admin-media-preview">
                        <img src="{{ $item->thumbnail_url ?: $item->media_url }}" alt="{{ $item->alt_text ?: 'Pratinjau media galeri' }}" loading="lazy" decoding="async">
                    </div>
                @else
                    <div class="admin-media-preview admin-media-fallback"><x-admin.icon name="images"/><span>Video</span></div>
                @endif
                <div class="admin-card-heading">
                    <div>
                        <h2>{{ $item->alt_text ?: 'Media #'.$item->id }}</h2>
                        <p class="admin-card-subtitle">{{ $item->type === 'video' ? 'Video' : 'Foto' }} · {{ $item->created_at?->format('d M Y') }}</p>
                    </div>
                    <x-admin.badge :tone="$item->trashed() ? 'danger' : 'success'">{{ $item->trashed() ? 'Diarsipkan' : 'Aktif' }}</x-admin.badge>
                </div>
                <div class="admin-form-actions">
                    @if($item->trashed())
                        <form method="POST" action="{{ route('admin.galeri.restore', $item) }}">
                            @csrf @method('PATCH')
                            <x-admin.button type="submit" variant="secondary">Pulihkan</x-admin.button>
                        </form>
                    @else
                        <x-admin.button :href="route('admin.galeri.edit', $item)" variant="secondary">Edit</x-admin.button>
                        <details class="admin-subdisclosure">
                            <summary>Arsipkan</summary>
                            <form method="POST" action="{{ route('admin.galeri.destroy', $item) }}">
                                @csrf @method('DELETE')
                                <x-admin.button type="submit" variant="danger">Konfirmasi arsip</x-admin.button>
                            </form>
                        </details>
                    @endif
                </div>
            </x-admin.card>
        @empty
            <x-admin.card class="admin-empty"><h2>Galeri masih kosong</h2><p>Unggah foto atau video pertama sekolah.</p></x-admin.card>
        @endforelse
    </div>
    @include('admin.pagination', ['paginator' => $items])
@endsection
