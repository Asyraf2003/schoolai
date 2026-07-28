# Unified Text System — M00 Admin Desktop Evidence

Status: PASS
Scope: M08 protected admin desktop UI only
Baseline: `main` at `bee0884bcaed96da7d4808464792bb9b1c95f486` before this documentation commit
Purpose: factual baseline for later M08 implementation. M00 performs discovery/documentation only and makes no typography, layout, DB, or behavior changes.

Article canvas/editor routes are deliberately excluded from this document and remain M09.

## 1. Active route, provider, controller, and render tree

The protected admin routes registered from `routes/web.php` run behind `auth`, `active.account`, `admin`, and `admin.locale` middleware and include:

- `routes/admin/core.php`;
- `routes/admin/ppdb.php`;
- `routes/admin/articles.php`;
- `routes/admin/gallery.php`;
- `routes/admin/statistics.php`.

Two additional active admin route families are registered outside those five route modules:

- `routes/testimonials.php` is loaded by `App\Providers\AppServiceProvider`;
- Hero admin routes are registered by `App\Providers\HeroServiceProvider` through `App\Providers\Concerns\RegistersHeroIntegration`.

`bootstrap/providers.php` registers both `AppServiceProvider` and `HeroServiceProvider`, so these provider-backed routes are part of the active M08 surface.

### Active entry surfaces

| Surface | GET entry routes | Active view tree | Content/data origin |
|---|---|---|---|
| Dashboard | `/admin`, `/admin/dashboard` | `admin.dashboard` -> `layouts.admin` | Blade + controller presentation arrays + DB/runtime counts/activity |
| PPDB | `/admin/ppdb`, `/admin/ppdb/showcase/{item}/edit` | `admin.ppdb.edit` -> `overview`, `showcase`, `showcase-list`, `showcase-form`, `behavior`, local `styles` | Blade + DB + controller/composer + runtime validation/session |
| Articles, non-canvas | `/admin/artikel`, `/create`, `/{article}`, `/{article}/edit` | `admin.articles.index`, `admin.articles.form` -> `form.fields` + `form.behavior`, `admin.articles.show` | Blade + DB/model accessors + runtime validation/session + JS preview |
| Gallery homepage items | `/admin/galeri`, `/create`, `/{item}`, `/{item}/edit` | `admin.gallery.index` -> `index.header`, `homepage-items`, `page-sections`; `admin.gallery.form` -> fields/scripts; `admin.gallery.show` | Blade + `lang/id/admin.php` + DB/model accessors + runtime + JS preview |
| Gallery page sections | section create/show/edit routes | `admin.gallery.page-sections.form`, `admin.gallery.page-sections.show` | Blade + DB + runtime validation/session |
| Gallery page media | media create/show/edit routes | `admin.gallery.page-media.form`, `edit`, `show` -> shared `_form`, fields/scripts | Blade + DB + runtime + JS preview/count/a11y title |
| Statistics | `/admin/stats` | `admin.site-statistics.edit` -> header, create-form, statistics-list, behavior | Blade + DB + runtime validation/session + CSS pseudo-content |
| Testimonials | `/admin/testimoni`, `/create`, `/{item}/edit` | `admin.testimonials.index`, `admin.testimonials.form` | Blade + DB/model accessors + runtime + JS hint |
| Hero | `/admin/hero`, `/create`, `/{heroSlide}/edit` | `admin.hero.index`, `admin.hero.form` -> partial fields/behavior | Blade + DB (`HeroSlide` + `Article`) + runtime + JS labels/hints |

Mutation-only POST/PUT/PATCH/DELETE routes do not create separate typography surfaces. Their visible success/error copy re-enters the same admin views through shared flash/error UI.

### Explicit M09 boundary

`routes/admin/articles.php` also exposes article-canvas routes. In M08, links/buttons such as `Buat via Canvas`, `Buka Canvas`, and `Buat Artikel` are classified only as M08 `action` text at their current render locations. The canvas/editor DOM, toolbar, document title/body, editor state, and canvas-specific CSS/JS are not audited here and remain M09.

## 2. Current desktop and locale contract

### Desktop contract

`resources/views/layouts/admin.blade.php` currently declares:

- `<meta name="viewport" content="width=1200, initial-scale=1">`;
- `.admin-desktop-shell` with a desktop minimum width;
- a separate `.admin-pc-only` state below the existing small-width threshold.

M08 therefore classifies the current desktop shell only. No mobile/tablet admin redesign is introduced or implied.

The PC-only explanatory state is existing product behavior. Its conditional heading/copy are inventoried below, but no responsive implementation work belongs to M08 M00.

### Admin locale contract

`App\Http\Middleware\ForceAdminLocale` executes `app()->setLocale('id')` for protected admin routes.

Therefore:

- current admin chrome/runtime locale is Indonesian;
- `lang/id/admin.php` is the active admin translation source where `__('admin.*')` is used;
- EN/AR admin translation files are not current protected-shell runtime states;
- English and Arabic still appear as content-entry languages inside admin forms through explicit `lang="en"` / `lang="ar"` fields and language tabs;
- M08 must not invent an ID/EN/AR admin-shell switch that the current product does not provide.

## 3. Content-source contract

The current admin surface uses these factual content sources:

1. **Blade** — page headings, section headings, field labels, action wording, descriptions, empty states, accessibility labels, modal shell copy.
2. **lang** — active Indonesian admin navigation/gallery/meta strings from `lang/id/admin.php`.
3. **DB/model presentation** — article/gallery/PPDB/statistic/testimonial/hero titles, descriptions, status labels, media labels, counts, URLs, dates, and localized content values.
4. **controller/provider/runtime presentation** — dashboard row labels/counts, recent activity labels/actors/times, validation messages, session success/error messages, conditional statuses.
5. **JS** — preview placeholders, preview counts, generated iframe titles/alt text, Hero dynamic media labels/hints, testimonial media hint, toast close control, delete-modal message replacement.
6. **CSS pseudo-content exception** — statistics create summary emits visible `Buka` / `Tutup` using `::after` in `resources/css/pages/admin-panel/002-admin-panel-cascade-002.css`. This is a real current visible-content source and is recorded rather than falsely relabeled as Blade/JS. It is a later implementation cleanup target, not an M00 blocker.

DB records do not own typography. Render location determines semantic role.

## 4. Shared admin semantic-role inventory

| Render location / selector | Source | Canonical role | Notes |
|---|---|---|---|
| document `<title>` | Blade / lang | `meta` | Browser/document metadata; no component typography role marker exists yet. |
| `.admin-pc-only__box h1` | lang | `page-title` | Conditional existing PC-only state. |
| `.admin-pc-only__box p` | lang | `description` | Conditional existing PC-only state. |
| `.admin-sidebar-label` | lang | `label` | `Menu admin`. |
| `.admin-side-nav[aria-label]` | lang | `label` | Accessibility-only navigation label. |
| `.admin-side-link`, `.admin-side-site`, `.admin-side-logout` text | lang | `action` | Navigation/action wording. |
| `.admin-topbar h1` | Blade / DB depending page | `page-title` unless the same topbar pattern is nested as a subsection | Role follows render hierarchy, not the `h1` tag alone. |
| `.admin-counter` | Blade + DB/runtime | `meta` | Counts/status summaries. |
| `.admin-primary-action`, `.admin-small-action`, `[data-language-tab]` | Blade / lang / runtime | `action` | Buttons, CTA links, tab actions. |
| `.admin-gallery-block__head h2`, `.admin-dashboard-panel__head h2` | Blade | `section-title` | Major page sections. |
| `.gallery-lite-row__order`, `.admin-dashboard-number` | render index / DB/runtime | `meta` | Position/count values. |
| `.gallery-lite-row__body strong` | DB / controller / Blade | `component-title` | Row-local entity/title. |
| `.gallery-lite-row__body small` | DB / runtime | `meta` by default | If the node renders an entity description rather than facts, it is `description`; PPDB active item description is the important example. |
| `.gallery-lite-status`, `.ppdb-showcase-media-pill` | Blade / DB/model state | `label` | Compact status/media badge. |
| `.admin-section-card__body h3`, `.admin-media-card__body h3` | DB/model state | `component-title` | Local card heading. |
| `.admin-section-card__body small`, `.admin-media-card__body small` | DB/runtime | `meta` | Counts/deletion time. |
| `.admin-field > label`, `.admin-check-field`, `dt` | Blade / lang | `label` | Form/detail identifiers. |
| `.admin-field input`, `.admin-field textarea` visible values | DB / old input / user/runtime | `body` | Editable content value. Canvas long-form fields are excluded to M09. |
| `.admin-field select option` | Blade / controller/model options | `label` | Finite choice wording. |
| `.admin-field em`, form notes, validation copy | Blade / runtime | `description` | Secondary explanatory/error copy. |
| `.gallery-media-review__head strong` | Blade | `component-title` | Local preview/review heading. |
| `.gallery-media-review__head small` | JS/runtime | `meta` | Preview item count. |
| preview fallback `<span>` | Blade / JS | `description` | Empty/invalid preview guidance. |
| `.gallery-detail-summary__head h1` | DB/model | `page-title` when it is the standalone detail title | On nested duplicate-heading structures, the inner title is `component-title`. |
| `.gallery-detail-summary__head p` | DB/model/runtime | `meta` | Type/category/byline/media facts. |
| `.gallery-detail-list dt` | Blade / lang | `label` | Detail term. |
| `.gallery-detail-list dd` | DB/model/runtime | `body` / `meta` | Value role depends on prose vs factual metadata; links remain `action`. |
| `.flash-message`, `.admin-error-box` message text | runtime/controller/validation | `body` | System feedback; moved into toast stack by JS. |
| `.admin-toast__close` | JS | `action` | JS creates `×` and `aria-label="Tutup notifikasi"`. |
| `.admin-delete-modal__label` | Blade | `label` | `Konfirmasi hapus`. |
| `.admin-delete-modal__panel h2` | Blade | `component-title` | Modal title. |
| `[data-admin-delete-modal-message]` | Blade -> JS/runtime | `description` | JS replaces default copy from `data-admin-delete-message`. |
| delete modal buttons/backdrop aria-label | Blade | `action` | `Batal`, `Ya, hapus`, accessibility close action. |

### Source-present but intentionally hidden groups

Current admin-panel CSS sets `display: none` on these groups in the desktop shell:

- `.admin-topbar > div:first-child > p`;
- `.admin-gallery-block__head p`;
- `.stats-manager-section-head p`;
- `.ppdb-showcase-admin__head p`;
- `.admin-section-card__body > p`;
- `.gallery-lite-empty > p`.

These strings remain in source but are neither visible nor accessibility-exposed while `display:none` wins. They are recorded so later implementation does not mistake dead runtime text for a missing role.

## 5. Surface-specific semantic inventory

### 5.1 Dashboard

| Selector / group | Source | Role |
|---|---|---|
| `.admin-topbar h1` | Blade | `page-title` |
| `.admin-dashboard-metric__label` | Blade | `label` |
| `.admin-dashboard-metric strong` | DB/runtime counts or PPDB state | `display` |
| `.admin-dashboard-metric small` | DB/runtime | `meta` |
| `.admin-dashboard__metrics[aria-label]` | Blade | `label` (a11y-only) |
| `.admin-dashboard-panel__head h2` | Blade | `section-title` |
| `.admin-dashboard-table__head [role="columnheader"]` | Blade | `label` |
| `.admin-dashboard-table__row strong` | controller presentation array | `component-title` |
| `.admin-dashboard-number` | DB/runtime | `meta` |
| `.admin-dashboard-activity__body strong` | controller/runtime | `component-title` |
| `.admin-dashboard-activity__body small`, `time` | runtime/audit log | `meta` |
| dashboard metric links, `Kelola`, quick actions | Blade/controller routes | `action` |
| empty-activity `.gallery-lite-empty h2` | Blade | `component-title` |

Dashboard counts are computed from current DB tables. Controller-supplied row labels are presentation content, not typography ownership.

### 5.2 Articles, excluding canvas/editor

| Selector / group | Source | Role |
|---|---|---|
| list `.admin-topbar h1` | Blade | `page-title` |
| list `.gallery-lite-row__body strong` | Article DB/model accessor | `component-title` |
| list row `small` | DB/runtime | `meta` |
| list status | model/runtime | `label` |
| list/show/form action links/buttons, including Canvas entry actions | Blade/runtime | `action` |
| replacement select `.sr-only` label | Blade | `label` (a11y-only) |
| form `.admin-topbar h1` | Blade | `page-title` |
| language tabs | Blade/runtime completion state | `action` |
| form labels | Blade | `label` |
| form input/textarea content | DB/old input/user | `body` |
| form help/error text | Blade/runtime validation | `description` |
| `Preview Thumbnail` | Blade | `component-title` |
| preview empty/invalid text | Blade/JS | `description` |
| generated preview `img.alt` | JS/file name | `label` (a11y-only) |
| show `.gallery-detail-summary__head h1` | Article DB/model | `page-title` |
| show byline/time | DB/runtime | `meta` |
| show detail `dt` / `dd` | Blade + DB | `label` / `body` or `meta` |

Article form JS does not set typography. It only toggles language panels and creates preview fallback/image nodes.

### 5.3 Hero admin

| Selector / group | Source | Role |
|---|---|---|
| `.admin-topbar h1` | Blade | `page-title` |
| row `strong` | HeroSlide/Article DB | `component-title` |
| row `small` | DB/model/runtime | `meta` |
| status pill | DB state | `label` |
| row/form buttons | Blade/runtime state | `action` |
| form labels | Blade/JS | `label` |
| form control values/options | DB/Article options/user | `body` / `label` |
| form `<em>` hints | Blade/JS | `description` |

Hero behavior JS changes visible text for upload label, media URL label, file/URL hints, and type-mode hint. Those nodes retain `label` or `description`; JS does not set font properties.

### 5.4 Testimonials

| Selector / group | Source | Role |
|---|---|---|
| page/form `.admin-topbar h1` | Blade | `page-title` |
| list row `strong` | DB/model accessors | `component-title` |
| list row `small` | DB/model | `meta` |
| status | DB state | `label` |
| actions | Blade/runtime state | `action` |
| form labels/options | Blade | `label` |
| form input value | DB/user | `body` |
| upload/embed help | Blade/JS | `description` |
| `Media Saat Ini` | Blade | `component-title` |
| image alt / iframe title | Blade | `label` (a11y-only) |

Testimonial JS only changes the visible upload-format hint and field visibility; it does not set typography.

### 5.5 PPDB admin

| Selector / group | Source | Role |
|---|---|---|
| overview `.admin-topbar h1` (`Pengaturan PPDB`) | Blade | `page-title` |
| overview status counter | DB/runtime | `meta` |
| setting form labels/check label | Blade | `label` |
| setting URLs/current values | DB/user | `body` |
| setting help | Blade | `description` |
| setting/toggle actions | Blade/DB state | `action` |
| `Status cepat` | Blade | `section-title` |
| nested showcase `.admin-topbar h1` (`Konten PPDB`) | Blade | `section-title` | This is a section role despite the current `h1` tag. |
| `.ppdb-showcase-admin__head .admin-counter` | DB/runtime counts | `meta` |
| panel `h2` (`Daftar item`, add/edit item) | Blade/runtime mode | `component-title` |
| `.ppdb-showcase-group__title > span` | controller option label / Blade | `component-title` |
| group title `small` | DB/runtime count | `meta` |
| active row `strong` | PPDB showcase DB | `component-title` |
| active row body `small` containing `description_id` | DB | `description` |
| archived row body `small` | DB/runtime | `meta` |
| media pill | model/runtime | `label` |
| form language tabs/actions | Blade/runtime | `action` |
| form labels/select options | Blade/controller | `label` |
| form content fields | DB/user | `body` |
| form help/errors | Blade/runtime | `description` |
| preview header strong | Blade | `component-title` |
| preview counter | model/runtime | `meta` |
| iframe title / image alt | Blade | `label` (a11y-only) |

PPDB JS only toggles panels/field visibility. No PPDB admin typography logic exists in JS.

### 5.6 Gallery admin

| Selector / group | Source | Role |
|---|---|---|
| main/form/detail top-level `h1` | Blade/lang/DB | `page-title` |
| `.admin-gallery-block__head h2` | Blade | `section-title` |
| homepage/gallery rows `strong` | DB/model | `component-title` |
| homepage/gallery rows `small` | DB/model/runtime | `meta` |
| statuses/media badges | lang/model state | `label` |
| row/card actions | Blade/lang/runtime | `action` |
| page-section card `h3` | DB/model | `component-title` |
| page-section card `small` | DB/runtime | `meta` |
| section detail topbar DB title | DB/model | `page-title` |
| section detail `Detail Bagian` / `Media` | Blade | `section-title` |
| media card `h3` | model | `component-title` |
| media card `p` | model media label | `meta` |
| form field labels/options | Blade/lang/controller | `label` |
| form input/textarea values | DB/user | `body` |
| form help/errors | Blade/lang/runtime | `description` |
| review title | Blade/lang | `component-title` |
| review fallback | Blade/JS | `description` |
| review count | JS | `meta` |
| detail-list `dt` / `dd` | Blade/lang + DB | `label` / `body` or `meta` |
| media-detail page topbar `Detail Media` | Blade | `page-title` |
| inner `.gallery-detail-summary__head h1` on media-detail page | model type label | `component-title` | The page already has a primary title. |
| generated preview `img.alt` / `iframe.title` | JS/file name | `label` (a11y-only) |

Gallery JS creates/replaces preview placeholder text, preview count strings, image alt values, and iframe titles. It does not assign font sizes/families/weights.

### 5.7 Statistics

| Selector / group | Source | Role |
|---|---|---|
| `.admin-topbar h1` | Blade | `page-title` |
| header counter | DB/runtime | `meta` |
| `details.admin-stat-create > summary` | Blade | `action` |
| `summary::after` `Buka` / `Tutup` | CSS pseudo-content | `action` |
| language tab buttons | Blade/runtime completion state | `action` |
| field labels / Arabic field labels | Blade | `label` |
| field values | DB/user | `body` |
| row `strong` | Statistic DB | `component-title` |
| row `small` | Statistic DB | `meta` |
| row status | Blade | `label` |
| `.admin-stat-record__edit-label` | Blade | `action` |
| archive title `strong` | Blade | `component-title` |
| archive title `small` | DB/runtime | `meta` |
| save/delete/restore actions | Blade/runtime | `action` |
| notice/error/success copy | Blade/runtime | `description` / `body` |

Statistics JS toggles language panels and opens the create `<details>` element. It creates no visible text.

## 6. Accessibility-relevant text inventory

The following a11y groups are part of M08 even when they do not own visible typography:

- sidebar `nav[aria-label]`;
- dashboard section/table labels and `role="columnheader"` text;
- gallery/article/hero/testimonial/statistics list `aria-label` values;
- language-tablist `aria-label` values;
- delete dialog `aria-labelledby`, `aria-describedby`, and backdrop close label;
- toast close `aria-label` created by JS;
- `sr-only` replacement-select label in article restore UI;
- image `alt` and iframe `title` values in media previews, including JS-generated preview nodes.

These use semantic role `label` unless the visible counterpart already carries a stronger role. Non-rendered accessibility attributes do not require a visual typography owner.

## 7. CSS ownership and source-resolved cascade

### Main ownership layers

| Text family | Current CSS ownership |
|---|---|
| Admin body/sidebar | `layouts/admin/styles/foundation.blade.php` + later `desktop-shell.blade.php` |
| Dashboard metrics/table/activity | `resources/css/pages/dashboard/001-page-css-dashboard-umum-dan-dashboard-admin.css` + `002-dashboard-cascade-002.css` |
| Topbar title, counters, actions, list rows, section/card headings | `resources/css/pages/admin-panel/001-unified-admin-ui-scoped-to-the-admin-shell-so-public.css` with lower-specificity legacy declarations still present in layout style partials |
| Form tabs/labels/controls/help | `resources/css/pages/admin-panel/002-admin-panel-cascade-002.css` + `layouts/admin/styles/forms.blade.php` |
| Detail titles/meta/list labels | `layouts/admin/styles/media-detail.blade.php` + `gallery-detail.blade.php` |
| Gallery cards/media | `layouts/admin/styles/gallery-management.blade.php` + admin-panel modules + `admin-panel-detail-guards.css` |
| PPDB showcase | `admin/ppdb/edit/styles.blade.php` + admin-panel module 002 |
| Statistics | admin-panel modules 002/003 + `layouts/admin/styles/statistics.blade.php` |
| Archived/deleted-state text decoration | `resources/css/pages/admin-soft-delete.css` |
| Toast feedback | `layouts/admin/styles/notifications.blade.php` |
| Delete modal | `layouts/admin/styles/delete-dialog.blade.php` |
| Arabic content-entry fields | `arabic-typography-base.css` / `arabic-type-scale.css` interacting with shared form CSS; document-level `html[lang="ar"]` rules are inactive because admin locale is forced to `id` |

### Source-resolved winner examples

No `getComputedStyle()` request is needed for M08 M00 because the materially relevant ownership conflicts can be resolved from selector specificity and source order in the current source tree.

Examples:

- `body.admin-desktop-body .admin-topbar h1` in admin-panel module 001 is more specific than later `.admin-topbar h1` layout declarations, so the scoped admin-panel title scale owns the current desktop topbar title properties.
- The same body-scoped admin-panel rules own current counter/primary/small-action sizes, list row title/meta sizes, status size, gallery section heading size, and standard form-control sizes over lower-specificity layout declarations.
- `body.admin-desktop-body .admin-field input/select/textarea` owns the current control `font-size` (`0.88rem`) over lower-specificity form rules.
- The admin request document remains `lang="id"`, so generic `html[lang="ar"] ...` heading/body rules do not apply to admin chrome.
- Arabic text inputs using `input[lang="ar"]:not([dir="ltr"])` still match the element-level Arabic display-family selector. Arabic URL inputs explicitly using `dir="ltr"` do not.
- `textarea[lang="ar"]` competes at equal specificity with the later inline `.admin-field textarea { font: inherit; }`; the later inline declaration wins inherited font-family/line-height, while the more-specific body-scoped admin-panel rule still owns the `0.88rem` font size. This is a current baseline asymmetry for later M08 implementation, not an M00 change.
- Statistics `Buka` / `Tutup` are authored as CSS pseudo-content and therefore have CSS as both content source and typography owner today.

This layering is exactly why M08 implementation must later migrate role ownership carefully instead of deleting legacy declarations wholesale.

## 8. JS behavior audit

Visible/admin-relevant JS text behavior is limited to:

- shared notification close control (`×`, `Tutup notifikasi`);
- shared delete modal description replacement;
- article thumbnail preview placeholder/error/alt;
- Hero upload/media labels and help text;
- testimonial upload-format hint;
- gallery item preview placeholder/iframe title;
- gallery page-media preview placeholder/count/iframe title/alt.

PPDB and statistics JS only toggle UI state/visibility. Language-tab scripts only change selected/hidden state.

No inspected M08 script contains viewport-dependent or component-specific font size/family/weight/line-height logic.

## 9. Proof and closure

### Source proof completed

- current `main` source was used;
- protected admin route modules were discovered from `routes/web.php`;
- provider-backed Testimoni and Hero admin routes were also discovered and included;
- active controllers/providers were traced to their actual entry views;
- the shared admin layout/render tree was mapped;
- all major visible text groups were classified by canonical role;
- accessibility-only major text groups were classified;
- Blade/lang/DB/controller/runtime/JS content sources were separated;
- CSS pseudo-content was recorded as a factual exception rather than hidden;
- CSS ownership was mapped, including source-resolvable cascade conflicts;
- admin locale is proven to be forced Indonesian;
- desktop-only product contract is proven from the current layout;
- article canvas/editor internals were kept out of M08 and remain M09;
- public surfaces already marked PASS were not re-audited.

### Why no new browser/runtime command was requested

M00 requires current computed typography only where source evidence cannot establish the relevant winner. For M08, current route/render ownership, visibility, locale applicability, and materially relevant typography ownership are determinable from source specificity/order. Conditional DB rows alter content/state, not the semantic role contract of their render locations.

A runtime command would therefore repeat evidence rather than close a concrete M08 gap.

## 10. M00 conclusion

FACT:

- all active protected admin route families, entry views, shared layout text groups, content sources, JS-created text, accessibility text, and current CSS ownership are mapped for the supported desktop admin surface.
- admin chrome is currently Indonesian-only by middleware; EN/AR are content-entry languages inside selected forms.
- M09 canvas/editor internals remain deliberately excluded.

GAP:

- no material M08 baseline gap remains for M00.

GOAL:

- preserve a complete admin text-role baseline for later M08 implementation without changing current UI.

IMPACT:

- documentation only; no runtime UI, DB, schema, route, JS behavior, or CSS implementation changes.

DECISION:

- close M08 admin desktop M00.

EXECUTION:

- create this dedicated admin M00 evidence document;
- update the Unified Text System handoff/progress only;
- do not create `text-system.css`, add `data-text-role`, remove legacy CSS, or start M01.

PROOF:

- route/provider/view tree closed from current `main`;
- no major visible/accessibility-relevant admin text group remains unclassified;
- content source and CSS owner are recorded for each major group;
- no unresolved cascade question requires `getComputedStyle()` for M08 closure.

STATUS: PASS

NEXT VALID STEP:

- continue M00 with **M09 article canvas/editor UI only**;
- do not re-audit M08 unless later code changes invalidate this baseline;
- do not start M01 until M09 is also PASS.

## 11. Progress after M08 closure

- M00 public surfaces: 100% (unchanged).
- M08 admin desktop M00: PASS.
- M09 article canvas/editor M00: NOT YET BASELINED.
- M00 overall: 7 of 8 implementation surface groups = **87.5%**.
- Unified Text System implementation M01-M10: not started.
- Whole Unified Text System project: approximately **8%** using the same conservative milestone-level method as the handoff.
