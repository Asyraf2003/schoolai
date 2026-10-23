@extends('admin.layout')
@section('title', 'Akun')
@section('description', 'Kelola akses guru dan murid tanpa membuka data kredensial.')
@section('content')
    <div class="admin-stack">
        <details class="admin-card admin-disclosure" @if($errors->any()) open @endif>
            <summary>Tambah akun Guru atau Murid</summary>
            <form class="admin-fields" method="POST" action="{{ route('admin.accounts.store') }}">
                @csrf
                <x-admin.field label="Role">
                    <select name="role" required>
                        <option value="guru" @selected(old('role') === 'guru')>Guru · login Google</option>
                        <option value="murid" @selected(old('role') === 'murid')>Murid · ID dan password</option>
                    </select>
                </x-admin.field>
                <x-admin.field label="Nama"><input name="name" value="{{ old('name') }}" maxlength="120" required></x-admin.field>
                <x-admin.field label="Email Google (Guru)"><input type="email" name="email" value="{{ old('email') }}" maxlength="255"></x-admin.field>
                <x-admin.field label="ID Murid"><input name="student_id" value="{{ old('student_id') }}" maxlength="50"></x-admin.field>
                <x-admin.field label="Password awal (Murid)"><input type="password" name="password" autocomplete="new-password"></x-admin.field>
                <x-admin.field label="Konfirmasi password"><input type="password" name="password_confirmation" autocomplete="new-password"></x-admin.field>
                <div><x-admin.button type="submit">Buat akun</x-admin.button></div>
            </form>
        </details>
        <x-admin.card>
            <div class="admin-toolbar"><h2>Daftar akun</h2><span class="admin-muted">{{ $accounts->count() }} akun ditampilkan</span></div>
            <form class="admin-filter" method="GET" action="{{ route('admin.accounts.index') }}">
                <x-admin.field label="Cari akun"><input name="search" value="{{ request('search') }}" maxlength="100" placeholder="Nama, email, atau ID"></x-admin.field>
                <x-admin.field label="Role">
                    <select name="role">
                        <option value="">Semua</option>
                        @foreach(['guru' => 'Guru', 'murid' => 'Murid'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-admin.field>
                <x-admin.button type="submit" variant="secondary">Cari</x-admin.button>
            </form>
        </x-admin.card>
        <div class="admin-grid">
            @forelse($accounts as $account)
                <x-admin.card>
                    <div class="admin-card-heading">
                        <div>
                            <h2>{{ $account->name }}</h2>
                            <p class="admin-card-subtitle">{{ $account->role?->label() ?? 'Tanpa role' }}</p>
                        </div>
                        <x-admin.badge :tone="$account->isActive() ? 'success' : 'danger'">{{ $account->isActive() ? 'Aktif' : 'Nonaktif' }}</x-admin.badge>
                    </div>
                    <p class="admin-card-subtitle">{{ $account->email ?: $account->student_id }}</p>
                    @if($account->isGuru() || $account->isMurid())
                        <details class="admin-subdisclosure">
                            <summary>Edit akun</summary>
                            <form class="admin-fields" method="POST" action="{{ route('admin.accounts.update', $account) }}">
                                @csrf @method('PUT')
                                <x-admin.field label="Nama"><input name="name" value="{{ $account->name }}" maxlength="120" required></x-admin.field>
                                @if($account->isGuru())
                                    <x-admin.field label="Email Google"><input type="email" name="email" value="{{ $account->email }}" required></x-admin.field>
                                @else
                                    <x-admin.field label="ID Murid"><input name="student_id" value="{{ $account->student_id }}" required></x-admin.field>
                                @endif
                                <div><x-admin.button type="submit">Simpan akun</x-admin.button></div>
                            </form>
                        </details>
                        <form class="admin-form-actions" method="POST" action="{{ route('admin.accounts.status', $account) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="active" value="{{ $account->isActive() ? '0' : '1' }}">
                            <x-admin.button variant="secondary" type="submit">{{ $account->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}</x-admin.button>
                        </form>
                        @if($account->isMurid())
                            <details class="admin-subdisclosure">
                                <summary>Reset password murid</summary>
                                <form class="admin-fields" method="POST" action="{{ route('admin.accounts.password.reset', $account) }}">
                                    @csrf @method('PUT')
                                    <x-admin.field label="Password baru"><input type="password" name="password" autocomplete="new-password" required></x-admin.field>
                                    <x-admin.field label="Konfirmasi password"><input type="password" name="password_confirmation" autocomplete="new-password" required></x-admin.field>
                                    <div><x-admin.button type="submit">Reset password</x-admin.button></div>
                                </form>
                            </details>
                        @endif
                        <details class="admin-subdisclosure">
                            <summary>Hapus akun</summary>
                            <p class="admin-muted">Akun ini akan dihapus dan tidak dapat login lagi.</p>
                            <form method="POST" action="{{ route('admin.accounts.destroy', $account) }}">
                                @csrf @method('DELETE')
                                <x-admin.button type="submit" variant="danger">Hapus akun ini</x-admin.button>
                            </form>
                        </details>
                    @endif
                </x-admin.card>
            @empty
                <x-admin.card class="admin-empty"><h2>Belum ada akun</h2><p>Tidak ada akun yang cocok dengan pencarian.</p></x-admin.card>
            @endforelse
        </div>
    </div>
@endsection
