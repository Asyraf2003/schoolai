@php
  $isEdit = $mode === 'edit';
  $action = $isEdit ? route('admin.galeri.section-media.update', $item) : route('admin.galeri.section-media.store', $section);
  $publishedAtValue = old('published_at', optional($item->published_at)->format('Y-m-d\TH:i'));
  $currentType = old('type', $item->type ?: 'photo');
  $isVideo = $currentType === 'video';
@endphp


  @include('admin.gallery.page-media.form.fields')

<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">

  @include('admin.gallery.page-media.form.scripts.inputs-and-photos')

  @include('admin.gallery.page-media.form.scripts.video-preview')

</script>

