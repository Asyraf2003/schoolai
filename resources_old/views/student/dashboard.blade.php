@extends('layouts.student', ['title' => 'Dashboard Murid'])

@section('content')
    <section class="student-panel" aria-labelledby="student-dashboard-title">
        <p class="student-kicker">Akun Murid Aktif</p>
        <h1 id="student-dashboard-title">Dashboard</h1>
        <p>Selamat datang. Dashboard ini hanya menampilkan identitas akun dasar.</p>

        <dl class="student-identity">
            <div>
                <dt>Nama</dt>
                <dd>{{ auth()->user()->name }}</dd>
            </div>
            <div>
                <dt>ID Siswa</dt>
                <dd>{{ auth()->user()->student_id }}</dd>
            </div>
            <div>
                <dt>Status</dt>
                <dd>Aktif</dd>
            </div>
        </dl>
    </section>
@endsection
