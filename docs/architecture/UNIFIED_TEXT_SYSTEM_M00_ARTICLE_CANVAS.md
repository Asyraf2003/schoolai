# Unified Text System — M00 Article Canvas / Editor Evidence

Status: PASS
Scope: M09 protected article canvas/editor UI only
Baseline: `main` at `ca0ee96cc1d3b5aa4469f2873e2207bf56b6043a` before this documentation commit
Purpose: factual baseline for later M09 implementation. M00 performs discovery/documentation only and makes no typography, layout, DB, route, or editor-behavior changes.

Non-canvas Article admin surfaces are deliberately excluded because they are already covered by `UNIFIED_TEXT_SYSTEM_M00_ADMIN.md`. Public article list/native reader are already PASS in `UNIFIED_TEXT_SYSTEM_M00_ARTICLES.md` and are not re-audited here.

## 1. Active route, controller, and render tree

`routes/admin/articles.php` registers the active article-canvas route family:

- `POST /admin/artikel/canvas/start` -> `ArticleCanvasAdminController::start`;
- `GET /admin/artikel/canvas/unsplash` -> `ArticleCanvasAdminController::searchUnsplash`;
- `GET /admin/artikel/{article}/canvas` -> `ArticleCanvasAdminController::edit`;
- `PATCH /admin/artikel/{article}/canvas` -> `ArticleCanvasAdminController::autosave`;
- `POST /admin/artikel/{article}/canvas/image` -> `ArticleCanvasAdminController::uploadImage`;
- `POST /admin/artikel/{article}/canvas/publish` -> `ArticleCanvasAdminController::publish`.

`ArticleCanvasAdminController` uses:

- `ManagesArticleCanvasDrafts`;
- `ManagesArticleCanvasMedia`;
- `PublishesArticleCanvas`.

The controller explicitly sets application locale to `id` in its constructor.

### Start/edit data flow

`start()` creates a native `Article` draft and redirects to the canvas edit route.

`edit()`:

- requires a native Article;
- derives category suggestions from existing Article tags;
- renders `admin.articles.canvas`;
- passes `article`, `adminPageKey`, and `categorySuggestions`.

### View tree

`resources/views/admin/articles/canvas.blade.php` extends `layouts.article-canvas` and includes exactly these active editor partials:

1. `admin.articles.canvas.topbar`;
2. `admin.articles.canvas.workspace`;
3. `admin.articles.canvas.toolbars`;
4. `admin.articles.canvas.dialogs`;
5. `admin.articles.canvas.publish-drawer`.

The root `.article-canvas-app` also exposes autosave, image-upload, Unsplash, publish, and current-thumbnail URLs through data attributes for JS behavior.

### Canvas layout and active assets

`resources/views/layouts/article-canvas.blade.php` is a dedicated editor layout rather than `layouts.admin`.

Source facts:

- `<html lang="id">` is hardcoded for the editor shell;
- viewport is `width=device-width, initial-scale=1`;
- persisted Arabic title/subtitle/content are emitted in a JSON data node;
- Vite loads, in this order:
  1. `resources/css/pages/article-canvas.css`;
  2. `resources/css/arabic-typography.css`;
  3. `resources/js/pages/article-canvas-arabic.js`;
  4. `resources/js/pages/article-canvas.js`;
  5. `resources/js/pages/article-canvas-context-ui.js`.

The order matters: Arabic document/button injection runs before the main canvas module collects language buttons/documents.

## 2. Document-language and persistence contract

### Indonesian and English

Blade renders two editor documents directly:

- `[data-document-language="id"]`;
- `[data-document-language="en"]`.

Each contains:

- `.canvas-title` textarea;
- `.canvas-subtitle` textarea;
- `.canvas-body[data-editor]` contenteditable long-form body.

Content source is the current `Article` DB record.

### Arabic

Arabic is not rendered as a third Blade document.

`resources/js/pages/article-canvas-arabic.js`:

- reads persisted Arabic Article values from the layout JSON node;
- creates the `AR` language-switch button;
- creates `[data-document-language="ar"]` with `lang="ar"` and `dir="rtl"`;
- creates Arabic title/subtitle textareas and body editor;
- injects persisted Arabic body HTML.

`PersistArabicArticleCanvas` intercepts the canvas autosave route when Arabic payload is present, validates/sanitizes `title_ar`, `subtitle_ar`, and `content_ar`, updates the Article, recalculates word/character metrics, and amends the JSON response.

Therefore Arabic editor content is DB-backed persisted content whose render nodes are created by JS. It is not transient JS-only content.

Existing `NativeArticleCanvasArabicTest` records the same persistence contract and verifies Arabic content is sanitized, stored, published, and rendered by the native reader.

## 3. Content-source contract

M09 currently contains these human-visible/accessibility-relevant text sources:

- Blade static editor chrome;
- Article DB title/subtitle/content/author/tags/thumbnail/date;
- controller/runtime autosave and publish responses;
- validation responses;
- existing Article tags used as category suggestions;
- JS-created/mutated editor UI text;
- Unsplash response metadata plus server-side Unsplash error messages;
- CSS pseudo-content/placeholders;
- browser-native `alert`/`prompt` surfaces triggered by canvas JS.

There is no locale-file-backed canvas copy today. The editor shell is a mixture of Indonesian and English literal strings.

## 4. Canonical semantic role inventory

### 4.1 Primary document editing surface

| Render location | Selector / element | Source | Canonical role |
|---|---|---|---|
| Article title being edited | `.canvas-title` | DB Article ID/EN; DB + JS for AR | `page-title` |
| Article subtitle being edited | `.canvas-subtitle` | DB Article ID/EN; DB + JS for AR | `subtitle` |
| Article body root | `.canvas-body[data-editor]` | DB sanitized HTML | `longform` |
| Body paragraph/list/blockquote | `.canvas-body p/li/blockquote/ul/ol` | DB/editor runtime | `longform` |
| Body h2/h3 | `.canvas-body h2/h3` | DB/editor runtime | `longform` with nested heading hierarchy preserved |
| Pull quote | `.canvas-body blockquote.article-pull-quote` | DB/editor runtime | `longform` |
| Code/pre | `.canvas-body code/pre` | DB/editor runtime | `longform` documented code exception |
| Figure caption | `.canvas-body figcaption` | DB/editor runtime | `meta` / longform caption |
| Body empty placeholder | `.canvas-body:empty::before` from `data-placeholder` | Blade for ID/EN; JS for AR; rendered by CSS | `description` |
| Title/subtitle placeholders | textarea `placeholder` | Blade for ID/EN; JS for AR | `label` |
| Document aria labels | document/title/subtitle accessibility attributes | Blade for ID/EN; JS for AR | `label` |

This follows the already-PASS reader contract: article content is not flattened into generic body/description typography. The editorial root remains `longform` with nested hierarchy and exceptions preserved.

### 4.2 Topbar and state UI

| Render location | Selector / element | Source | Canonical role |
|---|---|---|---|
| AM return control | `.canvas-brand` + aria-label | Blade | `action` |
| Save state | `.canvas-save-state` | Blade + controller response + JS | `meta` |
| Language buttons ID/EN/AR | `.canvas-language-switch button` | Blade + JS AR injection | `action` |
| Language group aria label | `.canvas-language-switch[aria-label]` | Blade | `label` |
| Word-count toggle | `.canvas-count` | Blade + JS metrics | `action` / `meta` |
| Word count popover | `[data-word-count]` | Blade + JS | `meta` |
| Character count popover | `[data-character-count]` | Blade + JS | `meta` |
| Publish open button | `.canvas-publish-button` | Blade | `action` |

### 4.3 Block menu

| Render location | Selector / element | Source | Canonical role |
|---|---|---|---|
| Add-block plus control | `.canvas-plus` + aria-label | Blade | `action` |
| Group headings: Jenis blok / Ukuran & posisi / Warna teks / Latar blok / Sisipkan | `.canvas-block-actions__group > span` | Blade | `label` |
| Paragraph/H2/H3/quote/dropcap controls | `[data-format]`, `[data-insert="dropcap"]` | Blade title + visible glyph | `action` |
| Size/alignment controls | block menu `[data-format]` | Blade title + glyph | `action` |
| Text/background color controls | `[data-block-color]`, `[data-block-background]` | Blade title / glyph / visual swatch | `action` |
| Insert image/Unsplash/video/embed/code/divider controls | block menu `[data-insert]` | Blade title + glyph | `action` |

Color swatches that have no visible word still carry human-readable `title` text and remain action labels for inventory purposes.

### 4.4 Inline, image, link, and code toolbars

| Render location | Selector / element | Source | Canonical role |
|---|---|---|---|
| Inline toolbar group label | `.canvas-inline-toolbar[aria-label]` | Blade | `label` |
| Bold/italic/strike/highlight/link | `.canvas-inline-toolbar [data-format]` | Blade visible glyph + aria-label | `action` |
| Paragraph/H2/H3/alignment inline controls | `.canvas-inline-toolbar [data-format]` | Blade visible glyph + aria-label | `action` |
| Inline color controls | `.canvas-inline-color-button` | Blade title / visual swatch | `action` |
| Link URL input placeholder/aria-label | `.canvas-link-input input` | Blade | `label` |
| Image layout controls | `.canvas-image-toolbar [data-image-layout]` | Blade | `action` |
| Image alignment controls | `.canvas-image-toolbar [data-image-align]` | Blade title + glyph | `action` |
| Alt text / thumbnail / replace / continue / delete | image toolbar buttons | Blade | `action` |
| Code block state label | `.canvas-code-toolbar > span` | Blade | `label` |
| Code exit/add paragraph | `[data-code-exit]` | Blade | `action` |

### 4.5 URL and Unsplash dialogs

| Render location | Selector / element | Source | Canonical role |
|---|---|---|---|
| Dialog eyebrow | `.canvas-dialog__eyebrow` | Blade + JS mutation for URL mode | `label` |
| Dialog title | `.canvas-dialog__panel h2` | Blade + JS mutation for URL mode | `component-title` |
| URL help copy | `[data-url-help]` | Blade | `description` |
| URL/search placeholders | dialog inputs | Blade | `label` |
| URL/Unsplash error | `.canvas-dialog__error` | JS + controller/network/runtime | `description` |
| Cancel/embed/search/close actions | dialog buttons | Blade | `action` |
| Unsplash result image alt | result `<img alt>` | external Unsplash response | `description` / media alt |
| Unsplash result button tooltip | result button `title="Foto oleh …"` | external response + JS | `label` |

### 4.6 Publish drawer

| Render location | Selector / element | Source | Canonical role |
|---|---|---|---|
| Publish eyebrow | `.canvas-dialog__eyebrow` in drawer | Blade | `label` |
| Drawer heading | `#publish-title` | Blade | `component-title` |
| Preview article title | `[data-preview-title]` | DB + JS preview | `component-title` |
| Preview subtitle | `[data-preview-subtitle]` | DB + JS preview | `description` |
| Preview author/date/read time | preview meta descendants | DB/runtime + JS | `meta` |
| Change thumbnail | `[data-thumbnail-change]` | Blade | `action` |
| Author field label | `.canvas-publish-field > span` | Blade | `label` |
| Category field label/help | category field heading/small | Blade | `label` / `description` |
| Category chips | `.canvas-category-chips > span` | DB tags + JS | `label` |
| Category chip remove | chip button + generated aria-label | JS + DB tag | `action` |
| Category suggestions | `.canvas-category-suggestions button` | DB existing tags + JS | `action` |
| Publish schedule legend | `.canvas-publish-schedule legend` | Blade | `label` |
| Now/schedule radio wording | schedule labels | Blade | `action` |
| Publication date label | `[data-publish-date-label]` | Blade + JS mutation | `label` |
| Publish error | `.canvas-publish-error` | validation/controller/runtime + JS | `description` |
| Final publish/schedule button | `[data-publish-submit]` | Blade + JS mutation | `action` |

### 4.7 Browser-native feedback triggered by editor JS

Two application-supplied strings are presented through browser-native UI rather than DOM nodes:

- invalid image `window.alert(...)` from `blocks.js` -> `description` feedback;
- image alt `window.prompt(...)` from `wire-images.js` -> `label` / action prompt.

Their typography is browser chrome and is not owned by application CSS. Their copy still belongs to the M09 text inventory because it is human-visible application-supplied text.

The generic browser before-unload confirmation has no application-supplied visible string and is not a separate text-role group.

## 5. JS-created and JS-mutated visible/accessibility text

### Persistence / metrics

`persistence.js` mutates:

- `Draft · Menyimpan…`;
- autosave response label, normally `Draft · Tersimpan`;
- `Draft · Gagal menyimpan`;
- word count `${n} kata`;
- character count `${n} karakter`.

Controller autosave response owns the successful saved label and current metrics.

### Image/upload state

`blocks.js` / `wire-images.js` can emit:

- invalid-image alert;
- `Mengupload thumbnail…`;
- `Mengupload gambar…`;
- `Thumbnail · Tersimpan`;
- `Upload gagal`;
- `Thumbnail dipilih · Menyimpan…`;
- browser-native alt-text prompt.

### URL dialog

`image-code.js` mutates:

- eyebrow to `Video` or `Embed`;
- title to `Tempel URL video` or `Tempel URL media`;
- invalid-provider error text.

### Unsplash

`embeds.js` creates result buttons/images and propagates controller/network errors into the visible dialog error node.

Controller-side Unsplash states include configuration-disabled and provider-unavailable messages.

### Categories

`categories.js` creates:

- suggestion buttons from existing DB tags;
- selected category chips;
- generated `Hapus kategori {item}` aria labels;
- chip remove `×` actions.

### Publish

`publishing.js` mutates:

- `Jadwal publikasi` / `Tanggal publikasi`;
- `Schedule to publish` / `Publish now`;
- publish error text;
- preview title/subtitle/author/date/read time.

Controller publish validation/success messages are runtime sources. Successful publication immediately redirects, so the success message is not rendered by the current canvas DOM before redirect.

### Arabic document injection

`article-canvas-arabic.js` creates:

- `AR` switch action;
- Arabic document aria-label;
- Arabic title/subtitle placeholders and aria labels;
- Arabic body placeholder.

### Context/formatting modules

`article-canvas-context-ui.js`, `selection-context.js`, `contextual.js`, `wire-toolbar.js`, `wire-documents.js`, `wire-global.js`, `formatting.js`, and `shortcuts.js` manage selection, position, visibility, state attributes, or longform structure.

They do not define component-specific font family/size/line-height logic.

## 6. CSS-created visible text and decorative pseudo-content

`002-article-canvas-cascade-002.css` creates visible pseudo-content:

- `.canvas-body:empty::before` -> `attr(data-placeholder)`; this is classified `description`;
- `.canvas-body figcaption:empty::before` -> `Type caption for image (optional)`; classified `meta` / longform caption placeholder;
- `.canvas-body hr::after` -> `• • •`; decorative divider, not a semantic text role.

`004-article-canvas-cascade-004.css` creates `·` separators between preview metadata. These are decorative separators, not independent semantic text groups.

The figcaption placeholder is currently English regardless of active document language. This is localization/content evidence, not an M00 typography blocker.

## 7. CSS typography ownership

### Load order

`article-canvas.css` imports exactly four canvas cascade files in order:

1. `001-article-canvas-cascade-001.css`;
2. `002-article-canvas-cascade-002.css`;
3. `003-article-canvas-cascade-003.css`;
4. `004-article-canvas-cascade-004.css`.

The layout then loads `arabic-typography.css` after `article-canvas.css`.

### `001` ownership

Primary owners include:

- canvas shell UI family (`--canvas-ui`);
- story/editor family (`--canvas-story`);
- brand/save/language/count/publish UI typography;
- `.canvas-title` family/size/line-height/letter-spacing;
- `.canvas-subtitle` family/size/line-height;
- `.canvas-body` family/size/line-height/letter-spacing.

### `002` ownership

Primary owners include:

- nested longform h2/h3 sizes;
- small/large longform variants;
- code/pre;
- quote/pull-quote;
- drop cap;
- figure caption typography;
- block-menu group labels/buttons;
- body/figure CSS pseudo-content.

### `003` ownership

Primary owners include:

- floating inline/image/code toolbar text;
- dialog eyebrow/title/help/error;
- dialog actions;
- Unsplash search UI;
- publish drawer panel shell.

### `004` ownership

Primary owners include:

- publish preview title/subtitle/meta;
- publish field/schedule labels;
- category chips/suggestions;
- publish submit;
- explicit canvas responsive rules at `<=850px` and `<=520px`.

M00 records those responsive declarations but does not convert the admin/editor product into a new responsive scope.

## 8. Arabic typography ownership

`arabic-typography.css` imports Arabic base rules followed by Arabic type scale and is loaded after canvas CSS.

Specific canvas adapters prove:

- `[data-document-language="ar"] .canvas-title` -> Cairo display family and normalized letter spacing;
- `[data-document-language="ar"] .canvas-subtitle` -> Lateef body family plus Arabic lead size/line-height;
- `[data-document-language="ar"] .canvas-body` -> Lateef body family plus Arabic longform size/line-height.

The document section itself carries `lang="ar"` and `dir="rtl"`.

Important source detail:

- the page root remains `<html lang="id">`, so generic `html[lang="ar"] ...` page rules are not the canvas mechanism;
- the dedicated `[data-document-language="ar"]` selectors are the active Arabic canvas adapter.

The Arabic title keeps the canvas title numeric scale because the current Arabic type-scale file does not define a separate `.canvas-title` size rule; it does replace family/letter-spacing.

## 9. Why `getComputedStyle()` is not required for M09 closure

No unresolved winning-cascade question remains:

1. the dedicated canvas layout does not load the general `app.css` bundle;
2. canvas typography enters through one four-file import chain;
3. Arabic typography is loaded after the canvas CSS;
4. the Arabic canvas rules target `[data-document-language="ar"]` directly;
5. JS modules do not set font family, font size, font weight, line height, or letter spacing;
6. responsive canvas overrides are explicit in source and do not create an unknown competing stylesheet layer.

Therefore source order + selector specificity are sufficient to identify current typography ownership for M00. Runtime computed-style proof would be redundant rather than closing missing evidence.

This does not waive runtime proof for later M09 implementation. Implementation must still use computed style as required by the DOD/Arabic typography guide before deleting or replacing legacy declarations.

## 10. Current baseline observations, not implementation tasks

- Canvas shell language is fixed `id` and many editor labels are literal Indonesian/English rather than locale-backed.
- Article document content supports ID, EN, and AR.
- Arabic document nodes are injected by JS before main canvas mount.
- Save/count/publish/category/Unsplash state can be JS-created or JS-mutated.
- The body uses a distinct editorial `longform` contract consistent with the public native reader.
- Current canvas CSS contains its own story font scale and many local UI sizes.
- Arabic adapter already changes the Arabic document family/optical scale without changing shell language.
- The empty figcaption placeholder is English for all document languages.
- Browser-native alert/prompt text exists and is outside application CSS typography ownership.
- Existing canvas responsive CSS exists in source; M00 does not redesign or expand product support.

These are evidence for later semantic-token implementation, not reasons to modify the editor during M00.

## 11. Repository proof already present

Existing source-level feature tests document core canvas contracts:

`tests/Feature/Admin/NativeArticleCanvasTest.php` covers:

- starting a native draft;
- sanitized autosave;
- dedicated thumbnail upload;
- native publish flow;
- scheduling behavior.

`tests/Feature/Admin/NativeArticleCanvasArabicTest.php` covers:

- Arabic canvas asset/data-node presence;
- Arabic payload autosave/sanitization;
- Arabic publication and native-reader output;
- Arabic fallback behavior.

These tests were inspected as repository evidence. No claim is made that they were freshly executed in this M00 documentation-only batch.

## 12. M00 conclusion

FACT:

- active M09 routes, controller traits, dedicated layout, partial render tree, language-document creation, persistence flow, JS text mutation, browser-native feedback, CSS pseudo-content, semantic roles, and CSS ownership are mapped from current `main`;
- ID/EN document nodes are Blade-rendered from DB content;
- AR document nodes are JS-created from DB content and persisted through `PersistArabicArticleCanvas`;
- no active canvas JS import remains uninspected for visible/accessibility-relevant text creation;
- longform role mapping is consistent with the already-PASS native reader baseline.

GAP:

- no material M09 baseline gap remains for M00.

GOAL:

- preserve a complete article canvas/editor text-role baseline for later M09 implementation without changing the editor.

IMPACT:

- documentation only; no runtime UI, route, DB/schema, CSS, JS, sanitizer, persistence, or publish behavior changes.

DECISION:

- close M09 article canvas/editor M00.

EXECUTION:

- create this dedicated M09 evidence document only;
- update the Unified Text System handoff/progress next;
- do not create `text-system.css`, add `data-text-role`, remove legacy typography, or begin implementation inside this M00 closure commit.

PROOF:

- route/controller/view/asset/import tree closed from current `main`;
- all major visible/accessibility-relevant editor text groups have a source and canonical role;
- all active main-canvas JS modules plus Arabic/context modules were inspected for visible/a11y text mutation;
- CSS ownership is deterministic from the dedicated canvas load order;
- existing core and Arabic feature tests record the persistence/render contracts;
- no unresolved cascade question requires M00 `getComputedStyle()`.

STATUS: PASS

NEXT VALID STEP:

- M00 baseline is now complete across M02-M09 surfaces;
- update `UNIFIED_TEXT_SYSTEM_HANDOFF.md` to record M00 = 100%;
- then the next implementation milestone is **M01 shared text-system foundation**;
- do not start M02-M10 migration work before M01 foundation itself passes its proof gate.

## 13. Progress after M09 closure

- M00 public surfaces: 100%.
- M08 admin desktop M00: PASS.
- M09 article canvas/editor M00: PASS.
- M00 overall: 8 of 8 implementation surface groups = **100%**.
- Unified Text System implementation M01-M10: not started.
- Whole Unified Text System project: approximately **9%** using the same conservative milestone-level method as the session handoff.
