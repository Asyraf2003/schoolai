# Unified Text System — M00 PPDB Evidence

Status: IN_PROGRESS
Scope: public `/ppdb` page only
Purpose: persist factual baseline evidence so future sessions continue from proof instead of repeating source discovery.
Implementation rule: M00 is discovery/documentation only. No visual/text-system implementation or CSS cleanup is performed here.

## 1. Route and controller

Route:

- `GET /ppdb` -> `PpdbPageController`.

Controller:

- `app/Http/Controllers/PpdbPageController.php`.

View:

- `resources/views/pages/ppdb.blade.php`.

The controller supplies:

- current `PpdbSetting` as `ppdbAdmission`;
- ordered `PpdbShowcaseItem` collection as `ppdbShowcaseItems` when the table exists.

`PpdbSetting` controls registration/information URLs and open/closed state. It does not carry typography.

## 2. Render tree

`resources/views/pages/ppdb.blade.php` renders:

1. page-scoped styles;
2. Hero;
3. Showcase, only when at least one DB audience has items;
4. admission steps;
5. programs;
6. document/timeline information;
7. FAQ;
8. final CTA;
9. closed-registration modal.

The shared public layout also loads `resources/css/pages/ppdb-journey.css` and `resources/js/pages/ppdb-journey.js` on the PPDB route.

## 3. Content-source map

### 3.1 Locale-backed page shell

`resources/views/pages/ppdb.blade.php` loads `__('pages.ppdb')` into `$page`.

This provides the page shell and static section content, including:

- Hero heading/subtitle/note/stats/mini cards;
- Showcase shell heading/subtitle/note/button;
- steps;
- programs;
- documents;
- timeline;
- FAQ;
- final CTA.

Locale files are under:

- `lang/id/pages.php`;
- `lang/en/pages.php`;
- `lang/ar/pages.php`.

### 3.2 Runtime translation labels

Runtime translation keys provide:

- register button;
- guide button;
- final CTA button;
- closed-registration modal title/body/button;
- audience labels/aria label;
- showcase fallback task/note/follow-up text.

These are content strings, not typography ownership.

### 3.3 DB-backed PPDB showcase

`PpdbShowcaseItem` records are grouped by `audience` (`parents` / `school`).

Per current render locale the showcase uses:

- `titleForLocale()`;
- `descriptionForLocale()`;
- media URL / video state.

DB showcase title/description remain content. Their render location determines semantic role.

### 3.4 PPDB settings

`PpdbSetting` supplies registration state/URLs. It influences which action target/modal is used, but does not define text role or typography.

## 4. Rendered role inventory

### 4.1 Hero

| Render location | Selector / element | Source | Proposed role |
|---|---|---|---|
| Page heading | `#ppdb-title.public-hero__title` | `pages.ppdb` | `page-title` |
| Hero subtitle | `.public-hero__subtitle` | `pages.ppdb` | `subtitle` |
| Registration button | `.btn--ppdb-register` | runtime lang | `action` |
| Guide button | `.btn--ppdb-guide` | runtime lang | `action` |
| Hero note | `.public-note` | `pages.ppdb` | `description` / `body` |
| Statistic value | `.public-stat-row strong` | `pages.ppdb` | `display` / `meta` according to final contract |
| Statistic label | `.public-stat-row span` | `pages.ppdb` | `label` |
| Mini-card title | `.ppdb-mini-card h2` | `pages.ppdb` | `component-title` |
| Mini-card text | `.ppdb-mini-card p` | `pages.ppdb` | `description` |

### 4.2 Showcase / journey

This section renders only when DB showcase audience items exist.

| Render location | Selector / element | Source | Proposed role |
|---|---|---|---|
| Audience tabs | `.ppdb-liftoff__tab` | runtime lang | `action` |
| Showcase heading | `.ppdb-liftoff__top h2` | `pages.ppdb` | `section-title` |
| Showcase subtitle | `.ppdb-liftoff__top p` | `pages.ppdb` | `subtitle` |
| Step number | `.ppdb-liftoff-step` | render index | `meta` |
| DB card title | `.ppdb-liftoff-card__text h3` | DB `PpdbShowcaseItem` | `component-title` |
| DB card description | `.ppdb-liftoff-card__text p` | DB `PpdbShowcaseItem` | `description` |
| Fallback visual title | `.ppdb-liftoff-ui__panel h3` | DB item title | visual-only duplicate / component text |
| Fallback task/follow-up text | `.ppdb-liftoff-list__item` | runtime lang | `label` / `body` |
| Showcase CTA note | `.ppdb-liftoff__cta p` | `pages.ppdb` | `description` |
| Showcase CTA link | `.ppdb-liftoff__cta .btn` | `pages.ppdb` | `action` |

The desktop journey can change position, opacity, blur, and pointer state through JS. JS does not own font sizing.

### 4.3 Admission steps

| Render location | Selector / element | Source | Proposed role |
|---|---|---|---|
| Section heading | `#alur-ppdb .public-section-head h2` | `pages.ppdb` | `section-title` |
| Section subtitle | `#alur-ppdb .public-section-head p` | `pages.ppdb` | `subtitle` |
| Step number | `.public-step-card__number` | render index | `meta` |
| Step title | `.public-step-card h3` | `pages.ppdb` | `component-title` |
| Step text | `.public-step-card p` | `pages.ppdb` | `description` |

### 4.4 Programs

| Render location | Selector / element | Source | Proposed role |
|---|---|---|---|
| Section heading | `.program-public-grid` preceding section head `h2` | `pages.ppdb` | `section-title` |
| Program age | `.program-public-card__age` | `pages.ppdb` | `meta` / `label` |
| Program title | `.program-public-card h3` | `pages.ppdb` | `component-title` |
| Program text | `.program-public-card > p:not(.program-public-card__age)` | `pages.ppdb` | `description` |

### 4.5 Documents and timeline

| Render location | Selector / element | Source | Proposed role |
|---|---|---|---|
| Documents heading | `.ppdb-info-grid .public-section-head h2` first group | `pages.ppdb` | `section-title` |
| Document item | `.document-list li` | `pages.ppdb` | `body` / `label` |
| Timeline heading | `.ppdb-info-grid .public-section-head h2` second group | `pages.ppdb` | `section-title` |
| Timeline date | `.timeline-card > span` | `pages.ppdb` | `meta` |
| Timeline title | `.timeline-card h3` | `pages.ppdb` | `component-title` |
| Timeline text | `.timeline-card p` | `pages.ppdb` | `description` |

### 4.6 FAQ

| Render location | Selector / element | Source | Proposed role |
|---|---|---|---|
| FAQ heading | FAQ section `.public-section-head h2` | `pages.ppdb` | `section-title` |
| FAQ question | `.faq-card summary` | `pages.ppdb` | `action` / `component-title` |
| FAQ answer | `.faq-card p` | `pages.ppdb` | `description` / `body` |

The FAQ question is interactive semantics and must not be mapped by tag name alone.

### 4.7 Final CTA and closed modal

| Render location | Selector / element | Source | Proposed role |
|---|---|---|---|
| Final CTA heading | `.public-final-cta__box h2` | `pages.ppdb` | `section-title` |
| Final CTA subtitle | `.public-final-cta__box p` | `pages.ppdb` | `description` |
| Final CTA button | `.public-final-cta__box .btn` | runtime lang | `action` |
| Closed modal heading | `.ppdb-closed-modal__panel h2` | runtime lang | `component-title` |
| Closed modal description | `.ppdb-closed-modal__panel p` | runtime lang | `description` |
| Closed modal close link | `.ppdb-closed-modal__close` | runtime lang | `action` |

## 5. Accessibility / JS-created text

The showcase includes `.ppdb-journey-status` with `aria-live="polite"` and `aria-atomic="true"`.

`resources/css/pages/ppdb-journey.css` visually hides this node with the standard 1px/clipped pattern.

`resources/js/pages/ppdb-journey/progress.js` updates its `textContent` to the currently active step number and title.

Classification:

- accessibility-relevant dynamic status;
- not visible typography;
- content derived from existing rendered step number/title;
- JS does not assign font sizing.

The journey JS also toggles panels, inert state, opacity, transforms, blur, and CTA visibility. These are interaction/layout behavior and are outside typography ownership.

## 6. CSS ownership evidence

PPDB typography currently comes from multiple layers:

1. shared public/welcome CSS inherited through the public layout;
2. page-scoped inline CSS in `resources/views/pages/ppdb/styles.blade.php`;
3. route-specific `resources/css/pages/ppdb-journey.css`;
4. Arabic typography adapter loaded by the public layout.

Examples:

- PPDB registration/guide buttons set heavy weight in page-scoped CSS;
- closed modal heading has its own clamp scale;
- showcase tabs/headings/card text have PPDB-specific rules;
- desktop journey has additional heading/subtitle sizing in `ppdb-journey.css`;
- RTL journey layout adjusts direction/alignment;
- Arabic family/scale may override shared/PPDB rules.

Therefore source declaration discovery does not establish winners. Runtime computed style is required.

## 7. Current M00 PPDB status

Completed:

- route/controller discovery;
- render-tree discovery;
- locale/runtime/DB content-source classification;
- role inventory for Hero, showcase, steps, programs, information, FAQ, final CTA, and closed modal;
- accessibility-only journey status identified;
- JS interaction vs typography ownership separated;
- CSS ownership layers identified.

Pending:

- runtime ID/EN/AR proof at 1440 / 768 / 390;
- confirm whether showcase is present in current DB runtime;
- confirm open/closed registration state and therefore which CTA target is active;
- computed typography for representative visible roles;
- record responsive/locale baseline findings;
- close `/ppdb` M00 surface.

Approximate PPDB M00 progress: 65%.

## 8. Next valid step

Run one read-only Brave/CDP runtime batch on `/ppdb` using the already-proven real locale-switch forms.

Measure representative nodes only. Optional showcase nodes may be `found=false` if the current DB has no audience items; that is a runtime-data fact, not an automatic failure.

Required locales/widths:

- ID: 1440 / 768 / 390;
- EN: 1440 / 768 / 390;
- AR: 1440 / 768 / 390 with `dir=rtl`.

Do not repeat browser capability checks or source discovery.