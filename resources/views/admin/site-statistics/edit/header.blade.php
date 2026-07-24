  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>Statistik</h1>
    </div>

    <div class="admin-inline-actions">
      <span class="admin-counter">{{ $statistics->count() }}/{{ $maxItems }} aktif · {{ $archivedStatistics->count() }} arsip</span>

      <a href="{{ route('home') }}" target="_blank" rel="noopener" class="admin-primary-action admin-primary-action--ghost">
        Lihat Homepage
      </a>

      @if($canCreate)
        <button type="button" class="admin-primary-action" data-open-stat-create>Tambah Statistik</button>
      @endif
    </div>
  </header>

  @if(session('success'))
    <p class="flash-message" role="status">{{ session('success') }}</p>
  @endif

  @if($errors->any())
    <div class="admin-error-box" role="alert">
      @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
      @endforeach
    </div>
  @endif
