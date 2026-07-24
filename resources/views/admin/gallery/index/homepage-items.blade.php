  <section class="admin-gallery-block" aria-label="Galeri utama homepage">
    <div class="admin-gallery-block__head">
      <div>
        <h2>Galeri Homepage</h2>
        <p>Hanya item aktif yang dihitung ke batas maksimal dan ditampilkan di homepage.</p>
      </div>

      <span class="admin-counter">
        {{ $activeItems->count() }}/{{ $homepageLimit }} aktif · {{ $archivedItems->count() }} arsip
      </span>
    </div>

    @if($activeItems->isNotEmpty() || $archivedItems->isNotEmpty())
      <div class="gallery-lite-list">
        @foreach($activeItems as $item)
          <article class="gallery-lite-row">
            <span class="gallery-lite-row__order">{{ str_pad((string) $item->sort_order, 2, '0', STR_PAD_LEFT) }}</span>

            <span class="gallery-lite-row__body">
              <strong>{{ $item->admin_title }}</strong>
              <small>{{ $item->type_label }} · {{ $item->admin_category }} · {{ $item->media_label }}</small>
            </span>

            <span class="gallery-lite-status {{ $item->is_published ? 'is-active' : 'is-inactive' }}">
              {{ $item->is_published ? $page['published'] : $page['draft'] }}
            </span>

            <span class="gallery-lite-actions">
              <form method="POST" action="{{ route('admin.galeri.move-up', $item) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action" @disabled($loop->first)>{{ $page['move_up'] }}</button>
              </form>

              <form method="POST" action="{{ route('admin.galeri.move-down', $item) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action" @disabled($loop->last)>{{ $page['move_down'] }}</button>
              </form>

              <a href="{{ route('admin.galeri.show', $item) }}" class="admin-small-action admin-small-action--ghost">Detail</a>

              <form method="POST" action="{{ route('admin.galeri.toggle', $item) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action admin-small-action--ghost">
                  {{ $item->is_published ? $page['toggle_off'] : $page['toggle_on'] }}
                </button>
              </form>

              <form method="POST" action="{{ route('admin.galeri.destroy', $item) }}" data-admin-delete-form data-admin-delete-message="Hapus item galeri utama ini? Item dan medianya tetap disimpan sebagai arsip.">
                @csrf
                @method('DELETE')
                <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--danger">{{ $page['delete_button'] }}</button>
              </form>
            </span>
          </article>
        @endforeach

        @foreach($archivedItems as $item)
          @php($replacementCandidates = $replacementCandidatesByArchivedId->get($item->getKey(), collect()))

          <article class="gallery-lite-row is-deleted">
            <span class="gallery-lite-row__order">A{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>

            <span class="gallery-lite-row__body">
              <strong>{{ $item->admin_title }}</strong>
              <small>
                {{ $item->type_label }} · {{ $item->admin_category }} · {{ $item->media_label }}
                @if($item->deleted_at)
                  · dihapus {{ $item->deleted_at->translatedFormat('d M Y, H:i') }} WIB
                @endif
              </small>
            </span>

            <span class="gallery-lite-status is-deleted">Dihapus</span>

            <span class="gallery-lite-actions">
              @if($canRestoreWithoutReplacement)
                <form method="POST" action="{{ route('admin.galeri.restore', $item->getKey()) }}">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="admin-small-action admin-small-action--restore">Pulihkan</button>
                </form>
              @endif

              @foreach($replacementCandidates as $candidate)
                <form
                  method="POST"
                  action="{{ route('admin.galeri.restore', $item->getKey()) }}"
                  data-admin-delete-form
                  data-admin-delete-message="Pulihkan arsip ini dan pindahkan item aktif #{{ $candidate->getKey() }} ke arsip? Tidak ada media yang dihapus permanen."
                >
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="replacement_gallery_item_id" value="{{ $candidate->getKey() }}">
                  <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--restore-swap">
                    Pulihkan &amp; Gantikan #{{ $candidate->getKey() }}
                  </button>
                </form>
              @endforeach

              @if(! $canRestoreWithoutReplacement && $replacementCandidates->isEmpty())
                <span class="admin-archive-note">Slot aktif penuh dan tidak ada media identik.</span>
              @endif
            </span>
          </article>
        @endforeach
      </div>
    @else
      <div class="gallery-lite-empty">
        <h2>{{ $page['empty_title'] }}</h2>
        <p>{{ $page['empty_description'] }}</p>
      </div>
    @endif
  </section>
