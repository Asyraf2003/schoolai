@extends('layouts.admin', [
  'title' => 'Admin PPDB',
  'activeAdminPage' => 'ppdb',
])

@php
  $showcaseItems = collect($showcaseItems ?? []);
  $showcaseItemsByAudience = $showcaseItems->groupBy('audience');
  $archivedShowcaseItems = collect($archivedShowcaseItems ?? []);
  $showcaseReplacementCandidatesByArchivedId = collect($showcaseReplacementCandidatesByArchivedId ?? []);
  $showcaseFormMode = $showcaseFormMode ?? 'create';
  $showcaseItemForm = $showcaseItemForm ?? null;
  $showcaseFormIsEdit = $showcaseFormMode === 'edit' && $showcaseItemForm?->exists;
  $showcaseAudience = old('audience', $showcaseItemForm->audience ?? 'parents');
  $showcaseMediaType = old('media_type', $showcaseItemForm->media_type ?? 'photo');

  $showcaseLanguageCompletion = [
    'id' => filled(old('title_id', $showcaseItemForm->title_id ?? '')) && filled(old('description_id', $showcaseItemForm->description_id ?? '')),
    'en' => filled(old('title_en', $showcaseItemForm->title_en ?? '')) && filled(old('description_en', $showcaseItemForm->description_en ?? '')),
    'ar' => filled(old('title_ar', $showcaseItemForm->title_ar ?? '')) && filled(old('description_ar', $showcaseItemForm->description_ar ?? '')),
  ];

  $showcaseActiveLanguage = $errors->hasAny(['title_ar', 'description_ar'])
    ? 'ar'
    : ($errors->hasAny(['title_en', 'description_en']) ? 'en' : 'id');
@endphp

@section('content')

  @include('admin.ppdb.edit.styles')

  @include('admin.ppdb.edit.overview')

  @include('admin.ppdb.edit.showcase')

  @include('admin.ppdb.edit.behavior')

@endsection

