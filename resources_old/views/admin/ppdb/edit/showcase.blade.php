  <section id="ppdb-showcase-admin" class="ppdb-showcase-admin">
    <div class="admin-topbar admin-topbar--compact" style="margin-bottom: 0;">
      <div>
        <h1>Konten PPDB</h1>
        <p>Item yang dihapus disimpan sebagai arsip, tidak muncul ke publik, dan tidak dapat diedit sampai dipulihkan.</p>
      </div>
      <span class="admin-counter">{{ $showcaseItems->count() }} aktif · {{ $archivedShowcaseItems->count() }} arsip</span>
    </div>

    <div class="ppdb-showcase-admin__grid">

      @include('admin.ppdb.edit.showcase-list')

      @include('admin.ppdb.edit.showcase-form')

    </div>
  </section>
