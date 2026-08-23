  <section class="admin-gallery-block" aria-label="Bagian halaman galeri">
    <div class="admin-gallery-block__head">
      <div>
        <h2>Galeri Halaman</h2>
        <p>Media pada halaman galeri tidak memakai batas enam item homepage.</p>
      </div>

      <span class="admin-counter">{{ $activePageSections->count() }} aktif · {{ ($archivedPageSections ?? collect())->count() }} arsip</span>
    </div>

    @if($activePageSections->isNotEmpty() || ($archivedPageSections ?? collect())->isNotEmpty())
      <div class="admin-section-grid">
        @foreach($activePageSections as $section)
          <article class="admin-section-card">
            <div class="admin-section-card__body">
              <span class="gallery-lite-status {{ $section->is_published ? 'is-active' : 'is-inactive' }}">
                {{ $section->is_published ? $page['published'] : $page['draft'] }}
              </span>

              <h3>{{ $section->admin_title }}</h3>
              <p>{{ $section->admin_description ?: 'Belum ada deskripsi.' }}</p>
              <small>{{ $section->media_items_count }} media aktif</small>
            </div>

            <div class="gallery-lite-actions admin-section-card__actions">
              <a href="{{ route('admin.galeri.section-media.create', $section) }}" class="admin-small-action">Tambah Media</a>
              <a href="{{ route('admin.galeri.sections.show', $section) }}" class="admin-small-action admin-small-action--ghost">Detail</a>

              <form method="POST" action="{{ route('admin.galeri.sections.toggle', $section) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action">
                  {{ $section->is_published ? $page['toggle_off'] : $page['toggle_on'] }}
                </button>
              </form>

              <form method="POST" action="{{ route('admin.galeri.sections.destroy', $section) }}" data-admin-delete-form data-admin-delete-message="Arsipkan bagian galeri ini? Semua media tetap tersimpan dan dapat muncul kembali saat dipulihkan.">
                @csrf
                @method('DELETE')
                <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--danger">{{ $page['delete_button'] }}</button>
              </form>
            </div>
          </article>
        @endforeach

        @foreach($archivedPageSectionRows as ['section' => $section, 'replacementCandidates' => $replacementCandidates])
          <article class="admin-section-card is-deleted">
            <div class="admin-section-card__body">
              <span class="gallery-lite-status is-deleted">Dihapus</span>
              <h3>{{ $section->admin_title }}</h3>
              <p>{{ $section->admin_description ?: 'Belum ada deskripsi.' }}</p>
              <small>
                {{ $section->media_items_count }} media tersimpan
                @if($section->deleted_at)
                  · dihapus {{ $section->deleted_at->translatedFormat('d M Y, H:i') }} WIB
                @endif
              </small>
            </div>

            <div class="gallery-lite-actions admin-section-card__actions">
              <form method="POST" action="{{ route('admin.galeri.sections.restore', $section->getKey()) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action admin-small-action--restore">Pulihkan</button>
              </form>

              @foreach($replacementCandidates as $candidate)
                <form
                  method="POST"
                  action="{{ route('admin.galeri.sections.restore', $section->getKey()) }}"
                  data-admin-delete-form
                  data-admin-delete-message="Pulihkan bagian lama dan pindahkan bagian aktif #{{ $candidate->getKey() }} ke arsip? Semua media pada kedua bagian tetap tersimpan."
                >
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="replacement_gallery_page_section_id" value="{{ $candidate->getKey() }}">
                  <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--restore-swap">
                    Pulihkan &amp; Gantikan #{{ $candidate->getKey() }}
                  </button>
                </form>
              @endforeach
            </div>
          </article>
        @endforeach
      </div>
    @else
      <div class="gallery-lite-empty">
        <h2>Belum ada galeri halaman.</h2>
        <p>Gunakan tombol Tambah Galeri untuk membuat galeri baru beserta medianya.</p>
      </div>
    @endif
  </section>
