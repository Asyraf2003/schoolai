<label class="admin-check-field">
  <input type="checkbox" name="show_on_homepage" value="1" @checked(old('show_on_homepage', $item->show_on_homepage))>
  <span>Tampilkan di landing page (maksimal 6)</span>
</label>

<label class="admin-check-field">
  <input type="checkbox" name="show_on_gallery_page" value="1" @checked(old('show_on_gallery_page', $item->show_on_gallery_page))>
  <span>Tampilkan di koleksi utama halaman Galeri</span>
</label>

@foreach($pageSections as $section)
  <label class="admin-check-field">
    <input type="checkbox" name="section_ids[]" value="{{ $section->getKey() }}" @checked(in_array($section->getKey(), $selectedSectionIds, true))>
    <span>Bagian: {{ $section->admin_title }}</span>
  </label>
@endforeach
