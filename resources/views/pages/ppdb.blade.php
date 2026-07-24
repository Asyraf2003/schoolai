@extends('layouts.public', ['title' => __('pages.ppdb.title'), 'description' => __('pages.ppdb.description')])

@php
  $page = __('pages.ppdb');
  $ppdbAdmission = $ppdbAdmission ?? null;
  $ppdbFormUrl = $ppdbAdmission?->publicRegistrationUrl();
  $ppdbInfoUrl = $ppdbAdmission?->publicInformationUrl();
  $ppdbIsOpen = (bool) ($ppdbAdmission?->isRegistrationOpen() ?? false);
  $ppdbRegisterButtonLabel = __('runtime.ppdb.register_button');
  $ppdbGuideButtonLabel = __('runtime.ppdb.guide_button');
  $ppdbFinalButtonLabel = __('runtime.ppdb.final_button');
  $ppdbClosedTitle = __('runtime.ppdb.closed_title');
  $ppdbClosedText = __('runtime.ppdb.closed_text');
  $ppdbClosedButton = __('runtime.ppdb.closed_button');
  $ppdbAudienceAriaLabel = __('runtime.ppdb.audience_aria_label');
  $ppdbSchoolTask = __('runtime.ppdb.school_task');
  $ppdbFamilyNote = __('runtime.ppdb.family_note');
  $ppdbClearFollowUp = __('runtime.ppdb.clear_follow_up');
  $ppdbShowcaseItems = collect($ppdbShowcaseItems ?? []);
  $ppdbShowcaseByAudience = [
      'parents' => $ppdbShowcaseItems->where('audience', 'parents')->values(),
      'school' => $ppdbShowcaseItems->where('audience', 'school')->values(),
  ];
  $ppdbAudienceLabels = [
      'parents' => __('runtime.ppdb.audience_parents'),
      'school' => __('runtime.ppdb.audience_school'),
  ];
  $ppdbAvailableAudiences = collect(array_keys($ppdbShowcaseByAudience))
      ->filter(fn (string $audience): bool => $ppdbShowcaseByAudience[$audience]->isNotEmpty())
      ->values();
  $ppdbInitialAudience = $ppdbAvailableAudiences->first() ?? 'parents';
@endphp

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
