@php
  $createFailed = old('form_context') === 'create';

  $editingId = old('form_context') === 'update'
      ? (int) old('editing_id')
      : null;

  $archivedStatistics = collect($archivedStatistics ?? []);
  $replacementCandidatesByArchivedId = collect($replacementCandidatesByArchivedId ?? []);

  $createValues = [
    'value' => $createFailed ? old('value') : '',
    'label' => $createFailed ? old('label') : '',
    'value_en' => $createFailed ? old('value_en') : '',
    'label_en' => $createFailed ? old('label_en') : '',
    'value_ar' => $createFailed ? old('value_ar') : '',
    'label_ar' => $createFailed ? old('label_ar') : '',
  ];

  $createLanguageCompletion = [
    'id' => filled($createValues['value']) && filled($createValues['label']),
    'en' => filled($createValues['value_en']) && filled($createValues['label_en']),
    'ar' => filled($createValues['value_ar']) && filled($createValues['label_ar']),
  ];

  $createActiveLanguage = $createFailed && $errors->hasAny(['value_ar', 'label_ar'])
      ? 'ar'
      : ($createFailed && $errors->hasAny(['value_en', 'label_en']) ? 'en' : 'id');
@endphp

@extends('layouts.admin', [
  'title' => 'Admin Statistik Homepage',
  'activeAdminPage' => 'stats',
])

@section('content')

  @include('admin.site-statistics.edit.header')

  <div class="admin-stat-index">

    @include('admin.site-statistics.edit.create-form')

    @include('admin.site-statistics.edit.statistics-list')

  </div>

  @include('admin.site-statistics.edit.behavior')

@endsection

