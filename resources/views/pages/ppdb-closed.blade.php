@extends('layouts.public', [
  'title' => __('runtime.ppdb.closed_title'),
  'description' => __('runtime.ppdb.closed_text'),
])

@section('content')
  <section class="public-hero" aria-labelledby="ppdb-closed-page-title">
    <div class="container public-hero__inner">
      <p class="public-hero__eyebrow">PPDB</p>
      <h1 id="ppdb-closed-page-title" class="public-hero__title">{{ __('runtime.ppdb.closed_title') }}</h1>
      <p class="public-hero__description">{{ __('runtime.ppdb.closed_text') }}</p>
      <a class="btn btn--primary" href="{{ route('home') }}">{{ __('app.auth.login.back_home') }}</a>
    </div>
  </section>
@endsection
