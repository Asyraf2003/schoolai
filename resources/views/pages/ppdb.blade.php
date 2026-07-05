<!doctype html>
@php($page = __('pages.ppdb'))
<html lang="{ app()->getLocale() }">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{ $page['title'] }</title>
    <meta name="description" content="{ $page['description'] }" />
    @vite(['resources/css/pages/welcome.css', 'resources/js/pages/welcome.js'])
  </head>
  <body>
    <main id="main-content" class="placeholder-page">
      <section class="section">
        <div class="container">
          <a href="{ route('home') }" class="link-arrow">{ __('pages.common.back_home') }</a>
          <h1 class="section-title">{ $page['heading'] }</h1>
          <p class="section-subtitle">{ $page['subtitle'] }</p>
        </div>
      </section>
    </main>
  </body>
</html>
