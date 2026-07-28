# Unified Text System — M00 Public Articles Evidence

Status: IN_PROGRESS
Scope: `/artikel` list page and `/artikel/{article:slug}` native reader
Purpose: persist factual baseline evidence so later sessions continue from proof instead of repeating source discovery.
Implementation rule: M00 is discovery/documentation only. No text-system implementation or styling cleanup is performed here.

## 1. Routes and controllers

Public article list:

- `GET /artikel` -> `ArticlePageController`.
- View: `resources/views/pages/artikel.blade.php`.

Native reader:

- `GET /artikel/{article:slug}` -> `NativeArticleController@show`.
- View: `resources/views/pages/artikel-native.blade.php`.

Both use the shared public layout and additionally load `resources/css/pages/article-reader.css`.

## 2. Article list content sources

`ArticlePageController` loads shell copy from `__('pages.artikel')`.

Published article cards come from DB `Article` rows. Per rendered locale the controller supplies:

- title;
- description;
- author;
- translated publish date;
- reading time for native articles;
- tags/categories;
- localized href;
- thumbnail.

Category filters are derived from DB article tags.

DB remains content-only. The list render location determines typography roles.

## 3. Article list rendered role inventory

| Render location | Selector / element | Source | Proposed role |
|---|---|---|---|
| Article page heading | `#artikel-title.public-hero__title` | `pages.artikel` lang | `page-title` |
| Hero subtitle | `.public-hero__subtitle` | `pages.artikel` lang | `subtitle` |
| Search label/placeholder | search label/input placeholder | lang/runtime | accessibility/UI label |
| Category filter link | `.article-category-filter a` | runtime + DB tags | `action` |
| Active-category result label/count | `.article-category-result` | runtime + DB tag | `meta` / `label` |
| Card category chips | `.article-index-card__categories > span` | DB tags | `meta` / `label` |
| Card date/author/read time | `.article-index-card__meta` | DB/runtime | `meta` |
| Card title | `.article-index-card__overlay strong` | DB Article title | `component-title` |
| Card description | `.article-index-card__description` | DB Article description | `description` |
| Thumbnail fallback number | `.article-index-card__media span` | controller index | `meta` / visual index |
| Empty-state title | `.article-index-empty h2` | lang/runtime | `component-title` |
| Empty-state description | `.article-index-empty p` | lang/runtime | `description` |
| Card link aria-label | `aria-label` on card link | lang + DB title | accessibility action |

## 4. Native reader content sources

`NativeArticleController` only renders native Article rows that are publicly visible, or admin-previewable for an authenticated admin.

Locale-selected DB content:

- article title;
- subtitle;
- description/meta description;
- HTML content body;
- author presentation;
- tags;
- related article title/description/href/categories/reading time.

Runtime translation copy supplies navigation, preview state, reading time, related-section labels, and author/footer copy.

## 5. Native reader role inventory

| Render location | Selector / element | Source | Proposed role |
|---|---|---|---|
| Back link | `.native-article__back` | runtime lang | `action` |
| Reader title | `.native-article__header h1` | DB Article title | `page-title` |
| Reader subtitle | `.native-article__subtitle` | DB Article subtitle | `subtitle` |
| Author name | `.native-article__byline strong` | DB Article author | `label` |
| Publish date/read time | `.native-article__byline small` | DB/runtime | `meta` |
| Tag links | `.native-article__tags a` | DB tags | `action` / `meta` |
| Article body root | `.native-article__body` | DB HTML content | `longform` |
| Article body headings | `.native-article__body h2/h3` | DB HTML content | nested longform heading hierarchy |
| Body paragraph/list/blockquote | descendants of `.native-article__body` | DB HTML content | `longform` |
| Figure caption | `.native-article__body figcaption` | DB HTML content | `meta` / longform caption |
| Code/pre | `.native-article__body code/pre` | DB HTML content | longform code presentation; documented exception |
| Author footer heading | `.native-article__footer strong` | runtime + DB author | `component-title` / label |
| Author footer copy | `.native-article__footer p` | runtime lang | `description` |
| Related eyebrow | `.native-article__related-eyebrow` | runtime lang | `label` |
| Related heading | `.native-article__related h2` | runtime lang | `section-title` |
| Related card category | `.native-related-card__body small` | DB tags | `meta` |
| Related card title | `.native-related-card__body strong` | DB Article title | `component-title` |
| Related card description | `.native-related-card__body > span` | DB Article description | `description` |
| Related read time | `.native-related-card__body em` | runtime | `meta` |
| Admin preview banner, when applicable | `.native-article__preview-banner` | runtime | `label` / `meta` state UI |

## 6. Important longform decision

The native article body is not normalized as ordinary card/body text.

The correct target role is `longform`, with nested heading, paragraph, quote, list, figure-caption, code, and editorial exceptions preserved inside the reader rhythm.

Current reader CSS already demonstrates a distinct editorial contract:

- reader serif family;
- fluid article title scale;
- dedicated subtitle scale;
- longform body font/line-height;
- nested `h2` / `h3` hierarchy;
- pull quotes;
- drop caps;
- image/figure variants;
- code/pre formatting.

M00 records this distinction so later normalization does not flatten article content into generic `body` or `description` roles.

## 7. Current CSS ownership evidence

`resources/css/pages/article-reader.css` imports exactly two reader cascade files.

`001-article-reader-cascade-001.css` owns most native reader typography, including:

- title;
- subtitle;
- byline;
- tags;
- longform body;
- nested headings;
- code and editorial text variants.

`002-article-reader-cascade-002.css` owns additional longform/editorial and related-article typography, plus category-filter/result styles.

Article index card typography is also defined in late shared welcome CSS, especially `045-halaman-artikel-clean-editorial-grid-3-2-1-gambar-ko.css`.

Therefore runtime computed style is still required for both list and reader surfaces.

## 8. Current M00 article status

Completed:

- route/controller discovery;
- list/reader Blade discovery;
- DB/lang/runtime content-source classification;
- initial list role inventory;
- initial native-reader role inventory;
- explicit `longform` distinction;
- reader CSS ownership discovery;
- article-index late-cascade ownership discovery.

Pending:

- runtime proof for `/artikel` in ID/EN/AR at 1440 / 768 / 390;
- discover a real current native article href from runtime rather than guessing a slug;
- runtime proof for that native reader in ID/EN/AR at 1440 / 768 / 390;
- confirm which optional nodes are present in current data;
- record locale/responsive baseline findings;
- close this M00 surface.

## 9. Next valid step

Run one read-only Brave/CDP batch that:

1. audits `/artikel` at all three locales and widths;
2. discovers a current same-origin native `/artikel/{slug}` href from rendered cards;
3. audits that native reader at all three locales and widths;
4. uses the already-proven real language-switch POST forms;
5. does not repeat browser capability or shared homepage discovery.
