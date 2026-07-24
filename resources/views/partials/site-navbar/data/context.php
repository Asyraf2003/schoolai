<?php
  $siteNavbar = $siteNavbar ?? ($navbar ?? __('home.navbar'));
  $siteNavbar = is_array($siteNavbar) ? $siteNavbar : [];

  $siteNavMode = $siteNavMode ?? (request()->routeIs('home') ? 'home' : 'public');
  $isHomeNav = $siteNavMode === 'home';
  $currentLocale = app()->getLocale();

  $languageItem = collect($siteNavbar['items'] ?? [])
    ->first(fn ($item) => ($item['type'] ?? null) === 'language');

  if (! is_array($languageItem)) {
      $languageItem = [
          'label' => __('pages.common.nav.language'),
          'type' => 'language',
          'options' => [],
      ];
  }

  $languageLabels = match ($currentLocale) {
      'ar' => [
          'id' => 'الإندونيسية',
          'en' => 'الإنجليزية',
          'ar' => 'العربية',
      ],
      'en' => [
          'id' => 'Indonesian',
          'en' => 'English',
          'ar' => 'Arabic',
      ],
      default => [
          'id' => 'Indonesia',
          'en' => 'English',
          'ar' => 'Arab',
      ],
  };

  $languageItem['options'] = [
      ['locale' => 'id', 'label' => $languageLabels['id'], 'short' => 'ID'],
      ['locale' => 'en', 'label' => $languageLabels['en'], 'short' => 'EN'],
      ['locale' => 'ar', 'label' => $languageLabels['ar'], 'short' => 'AR'],
  ];
