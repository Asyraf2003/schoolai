@extends('layouts.student', ['title' => 'Pengaturan Akun Murid'])

@section('content')
    <section class="student-panel" aria-labelledby="student-account-title">
        <p class="student-kicker">Keamanan Akun</p>
        <h1 id="student-account-title">Pengaturan Akun</h1>
        <p>Perbarui password dengan memasukkan password saat ini.</p>

        <div class="student-status" data-student-password-status aria-live="polite">
            @if(session('success')){{ session('success') }}@endif
        </div>

        <form
            class="student-password-form"
            method="POST"
            action="{{ route('murid.password.update') }}"
            data-student-password-form
        >
            @csrf
            @method('PUT')

            <label>
                <span>Password saat ini</span>
                <input type="password" name="current_password" required autocomplete="current-password">
            </label>
            <label>
                <span>Password baru</span>
                <input type="password" name="password" required minlength="8" maxlength="72" autocomplete="new-password">
            </label>
            <label>
                <span>Konfirmasi password baru</span>
                <input type="password" name="password_confirmation" required minlength="8" maxlength="72" autocomplete="new-password">
            </label>

            <div class="student-errors" data-student-password-errors role="alert"></div>
            <button type="submit" data-idle-label="Simpan Password" data-loading-label="Menyimpan…">
                Simpan Password
            </button>
        </form>
    </section>
@endsection
