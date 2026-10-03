      <div class="gallery-lite-panel ppdb-showcase-admin__panel">
        <div class="ppdb-showcase-admin__head">
          <div>
            <h2>Daftar item</h2>
          </div>
        </div>

        <div class="ppdb-showcase-list">
          @foreach ($showcaseAudienceGroups as ['audienceLabel' => $audienceLabel, 'items' => $items])
            <div class="ppdb-showcase-group">
              <div class="ppdb-showcase-group__title">
                <span>{{ $audienceLabel }}</span>
                <small>{{ $items->count() }} item aktif</small>
              </div>

              @forelse ($items as $item)
                <article class="ppdb-showcase-row">
                  <span class="ppdb-showcase-row__order">{{ $loop->iteration }}</span>
                  <div class="ppdb-showcase-row__body">
                    <strong>{{ $item->admin_title }}</strong>
                    <small>{{ $item->description_id }}</small>
                  </div>
                  <span class="ppdb-showcase-media-pill">{{ $item->media_type_label }}</span>

                  <div class="gallery-lite-actions">
                    <form method="POST" action="{{ route('admin.ppdb.showcase.move-up', $item) }}">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="admin-small-action admin-small-action--ghost" @disabled($loop->first)>Naik</button>
                    </form>

                    <form method="POST" action="{{ route('admin.ppdb.showcase.move-down', $item) }}">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="admin-small-action admin-small-action--ghost" @disabled($loop->last)>Turun</button>
                    </form>

                    <a href="{{ route('admin.ppdb.showcase.edit', $item) }}#ppdb-showcase-admin" class="admin-small-action">Edit</a>

                    <form method="POST" action="{{ route('admin.ppdb.showcase.destroy', $item) }}" data-admin-delete-form data-admin-delete-message="Hapus item PPDB ini? Item dan medianya tetap disimpan sebagai arsip.">
                      @csrf
                      @method('DELETE')
                      <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--danger">Hapus</button>
                    </form>
                  </div>
                </article>
              @empty
                <div class="gallery-lite-empty">
                  <h2>Belum ada item.</h2>
                  <p>Tambah item di form sebelah kanan.</p>
                </div>
              @endforelse
            </div>
          @endforeach

          @if ($archivedShowcaseItems->isNotEmpty())
            <div class="ppdb-showcase-group">
              <div class="ppdb-showcase-group__title">
                <span>Arsip item PPDB</span>
                <small>{{ $archivedShowcaseItems->count() }} item</small>
              </div>

              @foreach ($archivedShowcaseRows as ['item' => $item, 'replacementCandidates' => $replacementCandidates])
                <article class="ppdb-showcase-row is-deleted">
                  <span class="ppdb-showcase-row__order">A{{ $loop->iteration }}</span>
                  <div class="ppdb-showcase-row__body">
                    <strong>{{ $item->admin_title }}</strong>
                    <small>{{ $item->audience_label }} · {{ $item->media_type_label }} · dihapus {{ optional($item->deleted_at)->translatedFormat('d M Y, H:i') }} WIB</small>
                  </div>
                  <span class="ppdb-showcase-media-pill is-deleted">Dihapus</span>

                  <div class="gallery-lite-actions">
                    <form method="POST" action="{{ route('admin.ppdb.showcase.restore', $item->getKey()) }}">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="admin-small-action admin-small-action--restore">Pulihkan</button>
                    </form>

                    @foreach ($replacementCandidates as $candidate)
                      <form method="POST" action="{{ route('admin.ppdb.showcase.restore', $item->getKey()) }}" data-admin-delete-form data-admin-delete-message="Pulihkan arsip ini dan pindahkan item PPDB aktif #{{ $candidate->getKey() }} ke arsip?">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="replacement_ppdb_showcase_item_id" value="{{ $candidate->getKey() }}">
                        <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--restore-swap">Pulihkan &amp; Gantikan #{{ $candidate->getKey() }}</button>
                      </form>
                    @endforeach
                  </div>
                </article>
              @endforeach
            </div>
          @endif
        </div>
      </div>
