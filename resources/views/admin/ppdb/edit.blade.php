@extends('admin.layout')
@section('title', 'Link PPDB')
@section('description', 'Atur tautan pendaftaran yang ditampilkan di halaman publik.')
@section('content')
    <x-admin.card title="Pengaturan pendaftaran">
        <p class="admin-muted">Masukkan URL Google Form. Kosongkan untuk menyembunyikan link PPDB di header dan Hero.</p>
        <form class="admin-fields" method="POST" action="{{ route('admin.ppdb.update') }}">
            @csrf @method('PUT')
            <x-admin.field label="URL Google Form" hint="Contoh: https://forms.gle/...">
                <input type="url" name="registration_url" value="{{ old('registration_url', $setting->registration_url) }}" maxlength="2048" placeholder="https://forms.gle/...">
            </x-admin.field>
            <div><x-admin.button type="submit">Simpan link</x-admin.button></div>
        </form>
    </x-admin.card>
@endsection
