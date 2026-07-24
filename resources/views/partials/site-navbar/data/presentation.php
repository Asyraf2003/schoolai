<?php
  if (! collect($menuItems)->contains(fn ($item): bool => ($item['type'] ?? null) === 'language')) {
      $menuItems[] = $languageItem;
  }

  $logo = $siteNavbar['logo'] ?? [];
  $logoImageUrl = $logo['image_url'] ?? null;

  if (empty($logoImageUrl) && ! empty($logo['image'])) {
      $logoImageUrl = asset(ltrim((string) $logo['image'], '/'));
  }

  $logoHref = $isHomeNav ? ($logo['href'] ?? '#beranda') : $homeUrl;
  $logoLabel = trim((string) (($logo['line_1'] ?? __('pages.common.school_name')) . ' ' . ($logo['line_2'] ?? '')));
  $showCta = ! empty($siteNavbar['cta']);
  $megaMediaUrl = $siteNavbar['mega_media_url'] ?? asset('media/home/hero-school.png');
  $megaMediaAlt = $siteNavbar['mega_media_alt'] ?? $logoLabel;
  $languageModalTitle = match ($currentLocale) {
      'ar' => 'اختر اللغة',
      'en' => 'Choose language',
      default => 'Pilih bahasa',
  };
  $languageModalClose = match ($currentLocale) {
      'ar' => 'إغلاق اختيار اللغة',
      'en' => 'Close language chooser',
      default => 'Tutup pilihan bahasa',
  };
