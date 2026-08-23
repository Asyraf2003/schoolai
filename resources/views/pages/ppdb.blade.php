@extends('layouts.public', ['title' => __('pages.ppdb.title'), 'description' => __('pages.ppdb.description')])

@section('content')

  @include("pages.ppdb.styles")

  @include("pages.ppdb.hero")

  @if ($ppdbAvailableAudiences->isNotEmpty())
    @include("pages.ppdb.showcase")
  @endif

  @include("pages.ppdb.steps")

  @include("pages.ppdb.programs")

  @include("pages.ppdb.information")

  @include("pages.ppdb.faq")

  @include("pages.ppdb.final-cta")

  @include("pages.ppdb.closed-modal")

@endsection
