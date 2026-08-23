@extends('layouts.admin', [
  'title' => $adminPage['title'],
  'activeAdminPage' => $adminPageKey,
])

@section('content')
  <section class="admin-simple-page" aria-labelledby="admin-simple-title">
    <h1 id="admin-simple-title">{{ $adminPage['heading'] }}</h1>
    <p>{{ $simpleText }}</p>
  </section>
@endsection
