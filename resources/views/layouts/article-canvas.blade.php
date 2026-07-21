<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Canvas Artikel' }}</title>
  <script type="application/json" data-article-canvas-arabic-data>{!! json_encode([
    'title' => $article->title_ar ?? '',
    'subtitle' => $article->subtitle_ar ?? '',
    'content' => $article->content_ar ?? '',
  ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
  @vite([
    'resources/css/pages/article-canvas.css',
    'resources/js/pages/article-canvas-arabic.js',
    'resources/js/pages/article-canvas.js',
    'resources/js/pages/article-canvas-context-ui.js',
  ])
</head>
<body class="article-canvas-page">
  @yield('content')
</body>
</html>
