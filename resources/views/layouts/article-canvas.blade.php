<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Canvas Artikel' }}</title>
  @vite(['resources/css/pages/article-canvas.css', 'resources/js/pages/article-canvas.js'])
</head>
<body class="article-canvas-page">
  @yield('content')
</body>
</html>
