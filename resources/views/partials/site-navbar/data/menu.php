<?php
  $homeUrl = route('home');
  $homeAnchor = static fn (string $anchor): string => $isHomeNav
      ? $anchor
      : $homeUrl.$anchor;
  $menuItems = array_values(is_array($siteNavbar['items'] ?? null) ? $siteNavbar['items'] : []);

  $megaCopy = match ($currentLocale) {
      'ar' => [
          'gallery' => [
              'eyebrow' => 'حياة المدرسة',
              'title' => 'لحظات التعلّم والنمو والإنجاز.',
              'description' => 'استكشف صورًا مختارة من أنشطة الطلاب وتجاربهم في مدرسة المستقبل.',
              'links' => [
                  ['label' => 'الأنشطة', 'description' => 'أنشطة الطلاب والحياة المدرسية.', 'category' => 'Kegiatan'],
                  ['label' => 'الإنجازات', 'description' => 'إنجازات الطلاب والمدرسة.', 'category' => 'Prestasi'],
                  ['label' => 'التعلّم', 'description' => 'تجارب تعلّم ذات معنى.', 'category' => 'Pembelajaran'],
                  ['label' => 'الإبداع', 'description' => 'أعمال الطلاب ومشروعاتهم.', 'category' => 'Kreativitas'],
              ],
          ],
          'article' => [
              'eyebrow' => 'أفكار وقصص',
              'title' => 'قصص المدرسة التي تستحق القراءة.',
              'description' => 'مقالات حول الأنشطة والإنجازات والبرامج والتعليم في المستقبل.',
              'links' => [
                  ['label' => 'الأنشطة', 'description' => 'أخبار الأنشطة والحياة المدرسية.', 'category' => 'Kegiatan'],
                  ['label' => 'الإنجازات', 'description' => 'قصص إنجاز الطلاب والمدرسة.', 'category' => 'Prestasi'],
                  ['label' => 'البرامج', 'description' => 'البرامج المميزة وتطويرها.', 'category' => 'Program'],
                  ['label' => 'التعليم', 'description' => 'أفكار وممارسات تعليمية.', 'category' => 'Pendidikan'],
              ],
          ],
      ],
      'en' => [
          'gallery' => [
              'eyebrow' => 'School Life',
              'title' => 'Moments of learning, growth, and achievement.',
              'description' => 'Explore selected moments from student activities and learning at Al Mustaqbal.',
              'links' => [
                  ['label' => 'Activities', 'description' => 'Student activities and school life.', 'category' => 'Kegiatan'],
                  ['label' => 'Achievements', 'description' => 'Student and school achievements.', 'category' => 'Prestasi'],
                  ['label' => 'Learning', 'description' => 'Meaningful learning experiences.', 'category' => 'Pembelajaran'],
                  ['label' => 'Creativity', 'description' => 'Student work and creative projects.', 'category' => 'Kreativitas'],
              ],
          ],
          'article' => [
              'eyebrow' => 'Ideas & Stories',
              'title' => 'School stories worth reading.',
              'description' => 'Read about activities, achievements, programs, and education at Al Mustaqbal.',
              'links' => [
                  ['label' => 'Activities', 'description' => 'Activity news and school life.', 'category' => 'Kegiatan'],
                  ['label' => 'Achievements', 'description' => 'Student and school success stories.', 'category' => 'Prestasi'],
                  ['label' => 'Programs', 'description' => 'Signature programs and their development.', 'category' => 'Program'],
                  ['label' => 'Education', 'description' => 'Educational ideas and practice.', 'category' => 'Pendidikan'],
              ],
          ],
      ],
      default => [
          'gallery' => [
              'eyebrow' => 'Kehidupan Sekolah',
              'title' => 'Momen belajar, tumbuh, dan berprestasi.',
              'description' => 'Jelajahi pilihan momen kegiatan siswa dan pengalaman belajar di Al Mustaqbal.',
              'links' => [
                  ['label' => 'Kegiatan', 'description' => 'Aktivitas siswa dan kehidupan sekolah.', 'category' => 'Kegiatan'],
                  ['label' => 'Prestasi', 'description' => 'Pencapaian siswa dan sekolah.', 'category' => 'Prestasi'],
                  ['label' => 'Pembelajaran', 'description' => 'Pengalaman belajar yang bermakna.', 'category' => 'Pembelajaran'],
                  ['label' => 'Kreativitas', 'description' => 'Karya dan proyek kreatif siswa.', 'category' => 'Kreativitas'],
              ],
          ],
          'article' => [
              'eyebrow' => 'Gagasan & Cerita',
              'title' => 'Cerita sekolah yang layak dibaca.',
              'description' => 'Baca artikel kegiatan, prestasi, program, dan pendidikan Al Mustaqbal.',
              'links' => [
                  ['label' => 'Kegiatan', 'description' => 'Kabar kegiatan dan kehidupan sekolah.', 'category' => 'Kegiatan'],
                  ['label' => 'Prestasi', 'description' => 'Cerita pencapaian siswa dan sekolah.', 'category' => 'Prestasi'],
                  ['label' => 'Program', 'description' => 'Program unggulan dan pengembangannya.', 'category' => 'Program'],
                  ['label' => 'Pendidikan', 'description' => 'Gagasan dan praktik pendidikan.', 'category' => 'Pendidikan'],
              ],
          ],
      ],
  };

  foreach ($menuItems as $index => &$item) {
      if (($item['type'] ?? null) === 'language') {
          $item = array_replace($item, $languageItem);
          continue;
      }

      if ($index === 0) {
          $item['href'] = $isHomeNav ? '#beranda' : $homeUrl;
          $item['route_patterns'] = ['home'];
      } elseif ($index === 1) {
          $item['href'] = $homeAnchor('#program');
          $item['route_patterns'] = [];

          if (isset($item['mega']['links']) && is_array($item['mega']['links'])) {
              foreach ($item['mega']['links'] as &$link) {
                  $href = (string) ($link['href'] ?? '');
                  $link['href'] = str_starts_with($href, '#')
                      ? $homeAnchor($href)
                      : ($href === '/ppdb' ? route('ppdb') : $href);
              }
              unset($link);
          }
      } elseif ($index === 2) {
          $item['href'] = route('galeri');
          $item['route_patterns'] = ['galeri'];
          $item['mega'] = array_replace($megaCopy['gallery'], [
              'toggle_label' => $megaCopy['gallery']['eyebrow'],
              'media_url' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1400&q=82',
              'media_alt' => $megaCopy['gallery']['title'],
              'links' => array_map(
                  fn (array $link): array => array_merge($link, [
                      'href' => route('galeri', ['kategori' => $link['category']]),
                  ]),
                  $megaCopy['gallery']['links'],
              ),
          ]);
      } elseif ($index === 3) {
          $item['href'] = route('artikel');
          $item['route_patterns'] = ['artikel', 'artikel.detail', 'artikel.native'];
          $item['mega'] = array_replace($megaCopy['article'], [
              'toggle_label' => $megaCopy['article']['eyebrow'],
              'media_url' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=1400&q=82',
              'media_alt' => $megaCopy['article']['title'],
              'links' => array_map(
                  fn (array $link): array => array_merge($link, [
                      'href' => route('artikel', ['kategori' => $link['category']]),
                  ]),
                  $megaCopy['article']['links'],
              ),
          ]);
      } elseif ($index === 4) {
          $item['href'] = $homeAnchor('#kontak');
      }
  }
  unset($item);
