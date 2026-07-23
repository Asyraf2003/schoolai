@extends('layouts.admin', [
  'title' => 'Dashboard Admin',
  'activeAdminPage' => 'dashboard',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>Dashboard</h1>
    </div>

    <div class="admin-inline-actions">
      <span class="admin-counter">{{ $totalArchived }} total arsip</span>
      <a href="{{ route('home') }}" class="admin-primary-action admin-primary-action--ghost" target="_blank" rel="noopener">Lihat Website</a>
    </div>
  </header>

  <div class="admin-dashboard">
    <section class="admin-dashboard__metrics" aria-label="Ringkasan data website">
      <a href="{{ route('admin.artikel') }}" class="admin-dashboard-metric">
        <span class="admin-dashboard-metric__label">Artikel publik</span>
        <strong>{{ number_format($articles['active']) }}</strong>
        <small>{{ $articles['inactive'] }} draft / belum tayang · {{ $articles['archived'] }} arsip</small>
      </a>

      <a href="{{ route('admin.galeri') }}" class="admin-dashboard-metric">
        <span class="admin-dashboard-metric__label">Galeri aktif</span>
        <strong>{{ number_format($galleryMain['active']) }}</strong>
        <small>{{ $gallerySections['active'] }} bagian · {{ $galleryMedia['active'] }} media bagian</small>
      </a>

      <a href="{{ route('admin.testimoni.index') }}" class="admin-dashboard-metric">
        <span class="admin-dashboard-metric__label">Testimoni aktif</span>
        <strong>{{ number_format($testimonials['active']) }}</strong>
        <small>{{ $testimonials['inactive'] }} nonaktif · {{ $testimonials['archived'] }} arsip</small>
      </a>

      <a href="{{ route('admin.stats.edit') }}" class="admin-dashboard-metric">
        <span class="admin-dashboard-metric__label">Statistik homepage</span>
        <strong>{{ $statistics['active'] }}/{{ \App\Models\SiteStatistic::MAX_ITEMS }}</strong>
        <small>{{ $statistics['archived'] }} arsip tersimpan</small>
      </a>

      <a href="{{ route('admin.ppdb') }}" class="admin-dashboard-metric {{ $ppdbOpen ? 'is-positive' : 'is-muted' }}">
        <span class="admin-dashboard-metric__label">PPDB</span>
        <strong>{{ $ppdbOpen ? 'Aktif' : 'Nonaktif' }}</strong>
        <small>{{ $ppdbShowcase['active'] }} konten aktif · {{ $ppdbShowcase['archived'] }} arsip</small>
      </a>
    </section>

    <div class="admin-dashboard__grid">
      <section class="admin-dashboard-panel" aria-labelledby="dashboard-content-title">
        <div class="admin-dashboard-panel__head">
          <div>
            <h2 id="dashboard-content-title">Status Konten</h2>
          </div>
          <span class="admin-counter">Data database saat ini</span>
        </div>

        <div class="admin-dashboard-table" role="table" aria-label="Status konten website">
          <div class="admin-dashboard-table__head" role="row">
            <span role="columnheader">Konten</span>
            <span role="columnheader">Aktif</span>
            <span role="columnheader">Draft / Nonaktif</span>
            <span role="columnheader">Arsip</span>
            <span role="columnheader"></span>
          </div>

          @foreach($contentRows as $row)
            <div class="admin-dashboard-table__row" role="row">
              <strong role="cell">{{ $row['label'] }}</strong>
              <span role="cell" class="admin-dashboard-number is-active">{{ $row['active'] }}</span>
              <span role="cell" class="admin-dashboard-number">{{ $row['inactive'] }}</span>
              <span role="cell" class="admin-dashboard-number is-archive">{{ $row['archived'] }}</span>
              <a role="cell" href="{{ route($row['route']) }}" class="admin-small-action admin-small-action--ghost">Kelola</a>
            </div>
          @endforeach
        </div>
      </section>

      <section class="admin-dashboard-panel" aria-labelledby="dashboard-activity-title">
        <div class="admin-dashboard-panel__head">
          <div>
            <h2 id="dashboard-activity-title">Aktivitas Terbaru</h2>
          </div>
        </div>

        @if($recentActivity->isNotEmpty())
          <div class="admin-dashboard-activity">
            @foreach($recentActivity as $activity)
              <div class="admin-dashboard-activity__item">
                <div class="admin-dashboard-activity__body">
                  <strong>{{ $activity['label'] }}</strong>
                  <small>
                    {{ $activity['actor'] }}
                    @if($activity['subject_id'])
                      · ID {{ $activity['subject_id'] }}
                    @endif
                  </small>
                </div>
                <time title="{{ $activity['title'] }}">{{ $activity['time'] }}</time>
              </div>
            @endforeach
          </div>
        @else
          <div class="gallery-lite-empty">
            <h2>Belum ada aktivitas konten tercatat.</h2>
          </div>
        @endif
      </section>
    </div>

    <section class="admin-dashboard-panel" aria-labelledby="dashboard-actions-title">
      <div class="admin-dashboard-panel__head">
        <div>
          <h2 id="dashboard-actions-title">Aksi Cepat</h2>
        </div>
      </div>

      <div class="admin-dashboard-actions">
        <form method="POST" action="{{ route('admin.artikel.canvas.start') }}">
          @csrf
          <button type="submit" class="admin-primary-action">Buat Artikel</button>
        </form>
        <a href="{{ route('admin.galeri.create') }}" class="admin-primary-action admin-primary-action--ghost">Tambah Galeri</a>
        <a href="{{ route('admin.testimoni.create') }}" class="admin-primary-action admin-primary-action--ghost">Tambah Testimoni</a>
        <a href="{{ route('admin.ppdb') }}" class="admin-primary-action admin-primary-action--ghost">Atur PPDB</a>
        <a href="{{ route('admin.stats.edit') }}" class="admin-primary-action admin-primary-action--ghost">Atur Statistik</a>
      </div>
    </section>
  </div>
@endsection
