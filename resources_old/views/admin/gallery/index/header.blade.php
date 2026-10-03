  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>{{ $page['heading'] }}</h1>
      <p>Item, bagian, dan media yang dihapus tetap disimpan sebagai arsip. Arsip tidak dapat diedit, tetapi dapat dipulihkan dengan aman.</p>
    </div>

    <div class="admin-inline-actions">
      <a href="{{ route('admin.galeri.sections.create', ['continue' => 'media']) }}" class="admin-primary-action">Tambah Galeri</a>

      @if($canCreate)
        <a href="{{ route('admin.galeri.create') }}" class="admin-primary-action admin-primary-action--ghost">Tambah Homepage</a>
      @else
        <span class="admin-counter">Homepage {{ $activeItems->count() }}/{{ $homepageLimit }} penuh</span>
      @endif
    </div>
  </header>

  @if(session('success'))
    <p class="flash-message" role="status">{{ session('success') }}</p>
  @endif

  @if(isset($errors) && $errors->any())
    <div class="admin-error-box" role="alert">
      @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
      @endforeach
    </div>
  @endif
