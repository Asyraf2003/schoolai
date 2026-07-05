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
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <p class="admin-topbar__eyebrow">{{ $page['eyebrow'] }}</p>
      <h1>{{ $isEdit ? $page['edit_title'] : $page['create_title'] }}</h1>
    </div>

    <a href="{{ $isEdit ? route('admin.galeri.show', $item) : route('admin.galeri') }}" class="admin-primary-action admin-primary-action--ghost">{{ $page['back_button'] }}</a>
  </header>

  @if(isset($errors) && $errors->any())
    <div class="admin-error-box">
      @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ $action }}" class="gallery-lite-form">
    @csrf
    @if($isEdit)
      @method('PUT')
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

        <div class="admin-field">
          <label for="thumbnail_url">{{ $form['thumbnail_url'] }}</label>
          <input id="thumbnail_url" name="thumbnail_url" value="{{ old('thumbnail_url', $item->thumbnail_url) }}" maxlength="255">
          @error('thumbnail_url') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="media_url">{{ $form['media_url'] }}</label>
          <input id="media_url" name="media_url" value="{{ old('media_url', $item->media_url) }}" maxlength="255">
          @error('media_url') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="duration_seconds">{{ $form['duration_seconds'] }}</label>
          <input id="duration_seconds" name="duration_seconds" type="number" min="1" max="{{ $limits['max_video_seconds'] }}" value="{{ old('duration_seconds', $item->duration_seconds) }}">
          @error('duration_seconds') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="sort_order">{{ $form['sort_order'] }}</label>
          <input id="sort_order" name="sort_order" type="number" min="1" max="{{ $limits['max_items'] }}" value="{{ old('sort_order', $item->sort_order) }}" required>
          @error('sort_order') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="fallback_icon">{{ $form['fallback_icon'] }}</label>
          <input id="fallback_icon" name="fallback_icon" value="{{ old('fallback_icon', $item->fallback_icon) }}" maxlength="16" required>
          @error('fallback_icon') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="accent">{{ $form['accent'] }}</label>
          <input id="accent" name="accent" type="color" value="{{ old('accent', $item->accent ?: '#19aee6') }}" required>
          @error('accent') <small>{{ $message }}</small> @enderror
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

    <div class="admin-actions">
      <button type="submit" class="admin-save-btn">{{ $isEdit ? $page['update_button'] : $page['save_button'] }}</button>
    </div>
  </form>
@endsection
