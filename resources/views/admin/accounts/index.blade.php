@extends('layouts.admin', [
  'title' => 'Kelola Akun',
  'activeAdminPage' => 'accounts',
])

@section('content')
  @vite(['resources/css/pages/admin-accounts.css', 'resources/js/pages/admin-accounts.js'])

  <div
    class="account-manager"
    data-account-manager
    data-list-url="{{ route('admin.accounts.data') }}"
    data-store-url="{{ route('admin.accounts.store') }}"
    data-update-url="{{ route('admin.accounts.update', '__ACCOUNT__') }}"
    data-status-url="{{ route('admin.accounts.status', '__ACCOUNT__') }}"
    data-reset-url="{{ route('admin.accounts.password.reset', '__ACCOUNT__') }}"
  >
    <header class="admin-topbar admin-topbar--compact">
      <div>
        <h1>Akun</h1>
        <p>Kelola akses admin, guru, dan murid tanpa menampilkan kredensial.</p>
      </div>
      <button type="button" class="admin-primary-action" data-open-create>Tambah Akun</button>
    </header>

    <p class="account-manager__notice" data-account-notice role="status" aria-live="polite"></p>

    <form class="account-filters" data-account-filters role="search">
      <label><span>Cari akun</span><input name="search" type="search" maxlength="100" placeholder="Nama, email, atau ID siswa"></label>
      <label><span>Role</span><select name="role">
        <option value="">Semua role</option><option value="admin">Admin</option>
        <option value="guru">Guru</option><option value="murid">Murid</option>
        <option value="inert">Inert</option>
      </select></label>
      <label><span>Status</span><select name="status">
        <option value="">Semua status</option><option value="active">Aktif</option>
        <option value="inactive">Nonaktif</option>
      </select></label>
    </form>

    <section class="account-table-panel" aria-labelledby="account-list-title" aria-busy="false" data-account-panel>
      <div class="account-table-panel__head">
        <h2 id="account-list-title">Daftar Akun</h2><span data-account-sync>Data server</span>
      </div>
      <div class="account-table-scroll">
        <table class="account-table">
          <thead><tr><th>Identitas</th><th>Role</th><th>Status</th><th>Login terakhir</th><th>Aksi</th></tr></thead>
          <tbody data-account-rows>@include('admin.accounts.partials.rows', ['accounts' => $accounts])</tbody>
        </table>
      </div>
      <p class="account-empty" data-account-empty @if($accounts->isNotEmpty()) hidden @endif>Belum ada akun yang sesuai.</p>
    </section>

    @include('admin.accounts.partials.create-dialog')
    @include('admin.accounts.partials.edit-dialog')
    @include('admin.accounts.partials.reset-dialog')
  </div>
@endsection
