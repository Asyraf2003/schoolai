@extends('portal.shell')

@section('title', __('portal.ppdb_closed.title'))

@section('content')
    <section data-ppdb-closed>
        <h1>{{ __('portal.ppdb_closed.title') }}</h1>
        <p>{{ __('portal.ppdb_closed.description') }}</p>
        <a class="action secondary" href="{{ route('home') }}">{{ __('app.auth.login.back_home') }}</a>
    </section>
@endsection
