@extends('admin.layout')
@section('title', 'Akun')
@section('content')
    <details class="card">
        <summary>Tambah akun Guru atau Murid</summary>
        <form class="fields" method="POST" action="{{ route('admin.accounts.store') }}">
            @csrf
            <label>Role<select name="role"><option value="guru">Guru · login Google</option><option value="murid" @selected(old('role') === 'murid')>Murid · ID dan password</option></select></label>
            <label>Nama<input name="name" value="{{ old('name') }}" maxlength="120" required></label>
            <label>Email Google (Guru)<input type="email" name="email" value="{{ old('email') }}" maxlength="255"></label>
            <label>ID Murid<input name="student_id" value="{{ old('student_id') }}" maxlength="50"></label>
            <label>Password awal (Murid)<input type="password" name="password" autocomplete="new-password"></label>
            <label>Konfirmasi password<input type="password" name="password_confirmation" autocomplete="new-password"></label>
            <div><button type="submit">Buat akun</button></div>
        </form>
    </details>
    <form class="actions" method="GET" action="{{ route('admin.accounts.index') }}">
        <label>Cari akun<input name="search" value="{{ request('search') }}" maxlength="100"></label>
        <label>Role<select name="role"><option value="">Semua</option>@foreach(['guru' => 'Guru', 'murid' => 'Murid'] as $value => $label)<option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>@endforeach</select></label>
        <button type="submit">Cari</button>
    </form>
    <div class="cards">
        @forelse($accounts as $account)
            <article class="card">
                <h2>{{ $account->name }}</h2>
                <p>{{ $account->role?->label() ?? 'Tanpa role' }} · {{ $account->isActive() ? 'Aktif' : 'Nonaktif' }}</p>
                <p>{{ $account->email ?: $account->student_id }}</p>
                @if($account->isGuru() || $account->isMurid())
                    <details><summary>Edit akun</summary>
                        <form class="fields" method="POST" action="{{ route('admin.accounts.update', $account) }}">
                            @csrf @method('PUT')
                            <label>Nama<input name="name" value="{{ $account->name }}" maxlength="120" required></label>
                            @if($account->isGuru())<label>Email Google<input type="email" name="email" value="{{ $account->email }}" required></label>
                            @else<label>ID Murid<input name="student_id" value="{{ $account->student_id }}" required></label>@endif
                            <div><button type="submit">Simpan akun</button></div>
                        </form>
                    </details>
                    <form class="actions" method="POST" action="{{ route('admin.accounts.status', $account) }}">
                        @csrf @method('PATCH')<input type="hidden" name="active" value="{{ $account->isActive() ? '0' : '1' }}">
                        <button class="secondary" type="submit">{{ $account->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                    </form>
                    @if($account->isMurid())
                        <details><summary>Reset password murid</summary>
                            <form class="fields" method="POST" action="{{ route('admin.accounts.password.reset', $account) }}">
                                @csrf @method('PUT')
                                <label>Password baru<input type="password" name="password" autocomplete="new-password" required></label>
                                <label>Konfirmasi password<input type="password" name="password_confirmation" autocomplete="new-password" required></label>
                                <div><button type="submit">Reset password</button></div>
                            </form>
                        </details>
                    @endif
                    <details><summary>Hapus akun</summary><p>Akun ini akan dihapus dan tidak bisa login lagi.</p>
                        <form method="POST" action="{{ route('admin.accounts.destroy', $account) }}">@csrf @method('DELETE')<button class="danger" type="submit">Hapus akun ini</button></form>
                    </details>
                @endif
            </article>
        @empty<p>Tidak ada akun yang cocok.</p>@endforelse
    </div>
@endsection
