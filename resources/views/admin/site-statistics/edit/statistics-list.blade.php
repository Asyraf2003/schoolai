    <section class="admin-gallery-block" aria-label="Daftar statistik homepage">
      <div class="admin-gallery-block__head">
        <div>
          <h2>Daftar Statistik</h2>
        </div>
        <span class="admin-counter">{{ $statistics->count() }} aktif</span>
      </div>

      <div class="gallery-lite-list">
        @foreach($statistics as $index => $statistic)
          @php
            $isCurrentEdit = $editingId === $statistic->id;

            $editValues = [
              'value' => $isCurrentEdit ? old('value') : $statistic->value,
              'label' => $isCurrentEdit ? old('label') : $statistic->label,
              'value_en' => $isCurrentEdit ? old('value_en') : $statistic->value_en,
              'label_en' => $isCurrentEdit ? old('label_en') : $statistic->label_en,
              'value_ar' => $isCurrentEdit ? old('value_ar') : $statistic->value_ar,
              'label_ar' => $isCurrentEdit ? old('label_ar') : $statistic->label_ar,
            ];

            $editLanguageCompletion = [
              'id' => filled($editValues['value']) && filled($editValues['label']),
              'en' => filled($editValues['value_en']) && filled($editValues['label_en']),
              'ar' => filled($editValues['value_ar']) && filled($editValues['label_ar']),
            ];

            $editActiveLanguage = $isCurrentEdit && $errors->hasAny(['value_ar', 'label_ar'])
                ? 'ar'
                : ($isCurrentEdit && $errors->hasAny(['value_en', 'label_en']) ? 'en' : 'id');
          @endphp

          <details class="admin-stat-record" @if($isCurrentEdit) open @endif>
            <summary class="gallery-lite-row">
              <span class="gallery-lite-row__order">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>

              <span class="gallery-lite-row__body">
                <strong>{{ $statistic->value }} · {{ $statistic->label }}</strong>
                <small>
                  EN: {{ filled($statistic->value_en) && filled($statistic->label_en) ? $statistic->value_en . ' · ' . $statistic->label_en : '—' }}
                  · AR: {{ filled($statistic->value_ar) && filled($statistic->label_ar) ? $statistic->value_ar . ' · ' . $statistic->label_ar : '—' }}
                </small>
              </span>

              <span class="gallery-lite-status is-active">Aktif</span>
              <span class="admin-stat-record__edit-label">Edit</span>
            </summary>

            <div class="admin-stat-record__editor">
              <form id="stat-update-{{ $statistic->id }}" method="POST" action="{{ route('admin.stats.update', $statistic) }}" class="stats-manager-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_context" value="update">
                <input type="hidden" name="editing_id" value="{{ $statistic->id }}">

                <div data-language-tabs>
                  <div class="admin-inline-actions" role="tablist" aria-label="Bahasa statistik posisi {{ $index + 1 }}">
                    <button type="button" class="admin-primary-action {{ $editActiveLanguage === 'id' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $editActiveLanguage === 'id' ? 'true' : 'false' }}" data-language-tab="id">
                      Indonesia · Utama {{ $editLanguageCompletion['id'] ? '✓' : '' }}
                    </button>
                    <button type="button" class="admin-primary-action {{ $editActiveLanguage === 'en' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $editActiveLanguage === 'en' ? 'true' : 'false' }}" data-language-tab="en">
                      English {{ $editLanguageCompletion['en'] ? '✓' : '' }}
                    </button>
                    <button type="button" class="admin-primary-action {{ $editActiveLanguage === 'ar' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $editActiveLanguage === 'ar' ? 'true' : 'false' }}" data-language-tab="ar">
                      العربية {{ $editLanguageCompletion['ar'] ? '✓' : '' }}
                    </button>
                  </div>

                  <section class="admin-stat-language" data-language-panel="id" @if($editActiveLanguage !== 'id') hidden @endif>
                    <div class="admin-stat-fields">
                      <div class="admin-field">
                        <label for="stat-value-{{ $statistic->id }}">Nilai</label>
                        <input id="stat-value-{{ $statistic->id }}" name="value" maxlength="80" value="{{ $editValues['value'] }}" required>
                        @if($isCurrentEdit) @error('value')<small>{{ $message }}</small>@enderror @endif
                      </div>

                      <div class="admin-field">
                        <label for="stat-label-{{ $statistic->id }}">Label</label>
                        <input id="stat-label-{{ $statistic->id }}" name="label" maxlength="120" value="{{ $editValues['label'] }}" required>
                        @if($isCurrentEdit) @error('label')<small>{{ $message }}</small>@enderror @endif
                      </div>
                    </div>
                  </section>

                  <section class="admin-stat-language" data-language-panel="en" @if($editActiveLanguage !== 'en') hidden @endif>
                    <div class="admin-stat-fields">
                      <div class="admin-field">
                        <label for="stat-value-en-{{ $statistic->id }}">Value</label>
                        <input id="stat-value-en-{{ $statistic->id }}" name="value_en" maxlength="80" value="{{ $editValues['value_en'] }}" lang="en">
                        @if($isCurrentEdit) @error('value_en')<small>{{ $message }}</small>@enderror @endif
                      </div>

                      <div class="admin-field">
                        <label for="stat-label-en-{{ $statistic->id }}">Label</label>
                        <input id="stat-label-en-{{ $statistic->id }}" name="label_en" maxlength="120" value="{{ $editValues['label_en'] }}" lang="en">
                        @if($isCurrentEdit) @error('label_en')<small>{{ $message }}</small>@enderror @endif
                      </div>
                    </div>
                  </section>

                  <section class="admin-stat-language" data-language-panel="ar" @if($editActiveLanguage !== 'ar') hidden @endif>
                    <div class="admin-stat-fields">
                      <div class="admin-field">
                        <label for="stat-value-ar-{{ $statistic->id }}">القيمة</label>
                        <input id="stat-value-ar-{{ $statistic->id }}" name="value_ar" maxlength="80" value="{{ $editValues['value_ar'] }}" lang="ar" dir="rtl">
                        @if($isCurrentEdit) @error('value_ar')<small>{{ $message }}</small>@enderror @endif
                      </div>

                      <div class="admin-field">
                        <label for="stat-label-ar-{{ $statistic->id }}">التسمية</label>
                        <input id="stat-label-ar-{{ $statistic->id }}" name="label_ar" maxlength="120" value="{{ $editValues['label_ar'] }}" lang="ar" dir="rtl">
                        @if($isCurrentEdit) @error('label_ar')<small>{{ $message }}</small>@enderror @endif
                      </div>
                    </div>
                  </section>
                </div>
              </form>

              <div class="admin-stat-record__footer">
                <form method="POST" action="{{ route('admin.stats.destroy', $statistic) }}" data-admin-delete-form data-admin-delete-message="Hapus statistik {{ $statistic->value }} · {{ $statistic->label }}? Data tetap disimpan sebagai arsip.">
                  @csrf
                  @method('DELETE')
                  <button type="button" class="admin-small-action admin-small-action--danger" data-admin-delete-trigger @disabled($statistics->count() <= 1)>Hapus</button>
                </form>

                <button type="submit" form="stat-update-{{ $statistic->id }}" class="admin-primary-action">Simpan Perubahan</button>
              </div>
            </div>
          </details>
        @endforeach

        @if($archivedStatistics->isNotEmpty())
          <div class="admin-stat-archive-title">
            <strong>Arsip</strong>
            <small>{{ $archivedStatistics->count() }} data</small>
          </div>

          @foreach($archivedStatistics as $index => $statistic)
            @php($replacementCandidates = $replacementCandidatesByArchivedId->get($statistic->getKey(), collect()))

            <article class="gallery-lite-row is-deleted">
              <span class="gallery-lite-row__order">A{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>

              <span class="gallery-lite-row__body">
                <strong>{{ $statistic->value }} · {{ $statistic->label }}</strong>
                <small>
                  EN: {{ filled($statistic->value_en) && filled($statistic->label_en) ? $statistic->value_en . ' · ' . $statistic->label_en : '—' }}
                  · AR: {{ filled($statistic->value_ar) && filled($statistic->label_ar) ? $statistic->value_ar . ' · ' . $statistic->label_ar : '—' }}
                </small>
              </span>

              <span class="gallery-lite-status is-deleted">Arsip</span>

              <span class="gallery-lite-actions">
                @if($canRestoreWithoutReplacement)
                  <form method="POST" action="{{ route('admin.stats.restore', $statistic->getKey()) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="admin-small-action admin-small-action--restore">Pulihkan</button>
                  </form>
                @endif

                @foreach($replacementCandidates as $candidate)
                  <form method="POST" action="{{ route('admin.stats.restore', $statistic->getKey()) }}" data-admin-delete-form data-admin-delete-message="Pulihkan statistik arsip ini dan pindahkan statistik aktif #{{ $candidate->getKey() }} ke arsip?">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="replacement_site_statistic_id" value="{{ $candidate->getKey() }}">
                    <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--restore-swap">Pulihkan &amp; Gantikan #{{ $candidate->getKey() }}</button>
                  </form>
                @endforeach

                @if(! $canRestoreWithoutReplacement && $replacementCandidates->isEmpty())
                  <span class="admin-archive-note">Slot aktif penuh.</span>
                @endif
              </span>
            </article>
          @endforeach
        @endif
      </div>
    </section>
