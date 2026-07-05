{{-- REAL_GALLERY_CRUD_FORM_FINAL --}}
@php
  $page = __('admin.gallery');
  $form = $page['form'];
  $isEdit = $mode === 'edit';
  $action = $isEdit ? route('admin.galeri.update', $item) : route('admin.galeri.store');
  $publishedAtValue = old('published_at', optional($item->published_at)->format('Y-m-d\TH:i'));
@endphp

@extends('layouts.admin', [
  'title' => $isEdit ? $page['edit_title'] : $page['create_title'],
  'activeAdminPage' => 'galeri',
])

@section('content')
  <form method="POST" action="{{ $action }}" class="gallery-lite-form" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
      @method('PUT')
    @endif

    <header class="admin-topbar admin-topbar--compact">
      <div>
        <p class="admin-topbar__eyebrow">{{ $page['eyebrow'] }}</p>
        <h1>{{ $isEdit ? $page['edit_title'] : $page['create_title'] }}</h1>
      </div>

      <div class="admin-inline-actions">
        <a href="{{ $isEdit ? route('admin.galeri.show', $item) : route('admin.galeri') }}" class="admin-primary-action admin-primary-action--ghost">{{ $page['back_button'] }}</a>
        <button type="submit" class="admin-primary-action">{{ $isEdit ? $page['update_button'] : $page['save_button'] }}</button>
      </div>
    </header>

    @if(isset($errors) && $errors->any())
      <div class="admin-error-box">
        @foreach($errors->all() as $error)
          <p>{{ $error }}</p>
        @endforeach
      </div>
    @endif

    <section class="gallery-lite-form__panel">
      <div class="gallery-lite-form__grid">
        <div class="admin-field admin-field--wide">
          <label for="title">{{ $form['title'] }}</label>
          <input id="title" name="title" value="{{ old('title', $item->title) }}" maxlength="160" required>
          @error('title') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="type">{{ $form['type'] }}</label>
          <select id="type" name="type" required>
            @foreach($typeOptions as $value => $label)
              <option value="{{ $value }}" @selected(old('type', $item->type) === $value)>{{ $label }}</option>
            @endforeach
          </select>
          @error('type') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="category">{{ $form['category'] }}</label>
          <input id="category" name="category" value="{{ old('category', $item->category) }}" maxlength="80" required>
          @error('category') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="caption">{{ $form['caption'] }}</label>
          <textarea id="caption" name="caption" rows="3" maxlength="1000">{{ old('caption', $item->caption) }}</textarea>
          @error('caption') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="media_file">{{ $form['media_file'] }}</label>
          <input id="media_file" name="media_file" type="file" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/quicktime" @required(! $isEdit)>
          <em>
            {{ $form['media_hint'] }}
            @if($isEdit && $item->media_url)
              {{ $form['current_media'] }}: {{ $item->media_filename }}
            @endif
          </em>
          @error('media_file') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="sort_order">{{ $form['sort_order'] }}</label>
          <input id="sort_order" name="sort_order" type="number" min="1" max="{{ $limits['max_items'] }}" value="{{ old('sort_order', $item->sort_order) }}" required>
          @error('sort_order') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="published_at">{{ $form['published_at'] }}</label>
          <input id="published_at" name="published_at" type="datetime-local" value="{{ $publishedAtValue }}">
          @error('published_at') <small>{{ $message }}</small> @enderror
        </div>

        <label class="admin-check-field">
          <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published))>
          <span>{{ $form['is_published'] }}</span>
        </label>
      </div>
    </section>
  </form>
@endsection
