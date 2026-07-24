    @if($canCreate || $createFailed)
      <details id="stats-create" class="admin-stat-create" @if($createFailed) open @endif>
        <summary>Tambah statistik baru</summary>

        <div class="admin-stat-create__body">
          @if($canCreate)
            <form method="POST" action="{{ route('admin.stats.store') }}" class="stats-manager-form">
              @csrf
              <input type="hidden" name="form_context" value="create">

              <div data-language-tabs>
                <div class="admin-inline-actions" role="tablist" aria-label="Bahasa statistik baru">
                  <button type="button" class="admin-primary-action {{ $createActiveLanguage === 'id' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $createActiveLanguage === 'id' ? 'true' : 'false' }}" data-language-tab="id">
                    Indonesia · Utama {{ $createLanguageCompletion['id'] ? '✓' : '' }}
                  </button>
                  <button type="button" class="admin-primary-action {{ $createActiveLanguage === 'en' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $createActiveLanguage === 'en' ? 'true' : 'false' }}" data-language-tab="en">
                    English {{ $createLanguageCompletion['en'] ? '✓' : '' }}
                  </button>
                  <button type="button" class="admin-primary-action {{ $createActiveLanguage === 'ar' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $createActiveLanguage === 'ar' ? 'true' : 'false' }}" data-language-tab="ar">
                    العربية {{ $createLanguageCompletion['ar'] ? '✓' : '' }}
                  </button>
                </div>

                <section class="admin-stat-language" data-language-panel="id" @if($createActiveLanguage !== 'id') hidden @endif>
                  <div class="admin-stat-fields">
                    <div class="admin-field">
                      <label for="stats-create-value">Nilai</label>
                      <input id="stats-create-value" name="value" maxlength="80" value="{{ $createValues['value'] }}" placeholder="Contoh: 320+" required>
                      @error('value')<small>{{ $message }}</small>@enderror
                    </div>

                    <div class="admin-field">
                      <label for="stats-create-label">Label</label>
                      <input id="stats-create-label" name="label" maxlength="120" value="{{ $createValues['label'] }}" placeholder="Contoh: Siswa" required>
                      @error('label')<small>{{ $message }}</small>@enderror
                    </div>
                  </div>
                </section>

                <section class="admin-stat-language" data-language-panel="en" @if($createActiveLanguage !== 'en') hidden @endif>
                  <div class="admin-stat-fields">
                    <div class="admin-field">
                      <label for="stats-create-value-en">Value</label>
                      <input id="stats-create-value-en" name="value_en" maxlength="80" value="{{ $createValues['value_en'] }}" lang="en">
                      @error('value_en')<small>{{ $message }}</small>@enderror
                    </div>

                    <div class="admin-field">
                      <label for="stats-create-label-en">Label</label>
                      <input id="stats-create-label-en" name="label_en" maxlength="120" value="{{ $createValues['label_en'] }}" lang="en">
                      @error('label_en')<small>{{ $message }}</small>@enderror
                    </div>
                  </div>
                </section>

                <section class="admin-stat-language" data-language-panel="ar" @if($createActiveLanguage !== 'ar') hidden @endif>
                  <div class="admin-stat-fields">
                    <div class="admin-field">
                      <label for="stats-create-value-ar">القيمة</label>
                      <input id="stats-create-value-ar" name="value_ar" maxlength="80" value="{{ $createValues['value_ar'] }}" lang="ar" dir="rtl">
                      @error('value_ar')<small>{{ $message }}</small>@enderror
                    </div>

                    <div class="admin-field">
                      <label for="stats-create-label-ar">التسمية</label>
                      <input id="stats-create-label-ar" name="label_ar" maxlength="120" value="{{ $createValues['label_ar'] }}" lang="ar" dir="rtl">
                      @error('label_ar')<small>{{ $message }}</small>@enderror
                    </div>
                  </div>
                </section>
              </div>

              <div class="stats-manager-form-actions">
                <button type="submit" class="admin-primary-action">Tambah Statistik</button>
              </div>
            </form>
          @else
            <p class="admin-notice">
              Batas {{ $maxItems }} statistik aktif sudah tercapai. Arsipkan satu item aktif sebelum menambah data baru.
            </p>
          @endif
        </div>
      </details>
    @endif
