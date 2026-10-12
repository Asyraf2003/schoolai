@extends('admin.layout')
@section('title', 'Link PPDB')
@section('content')
    <p>Isi satu URL Google Form. Header dan Hero langsung membuka URL ini. Kosongkan untuk menyembunyikan link PPDB.</p>
    <form class="fields" method="POST" action="{{ route('admin.ppdb.update') }}">
        @csrf @method('PUT')
        <label>URL Google Form<input type="url" name="registration_url" value="{{ old('registration_url', $setting->registration_url) }}" maxlength="2048" placeholder="https://forms.gle/..."></label>
        <div><button type="submit">Simpan link</button></div>
    </form>
@endsection
