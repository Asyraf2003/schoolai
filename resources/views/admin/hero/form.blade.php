@extends('layouts.admin', [
  'title' => $isEdit ? 'Edit Hero Slide' : 'Tambah Hero Slide',
  'activeAdminPage' => 'hero',
])

@section('content')

  @include('admin.hero.partials.fields')

  @include('admin.hero.partials.behavior')

@endsection
