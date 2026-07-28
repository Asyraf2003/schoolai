# Unified Text System — M00 Public Articles Evidence

Status: PASS
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
| Card link aria-label | card link `aria-label` | lang + DB title | accessibility action |

Runtime note: in the audited default list state `.article-category-result`, empty title, and empty description were not rendered because no category filter was active and article rows were present. Their absence is data/state-dependent, not an audit failure.

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

The runtime-discovered native reader used for baseline proof was:

`/artikel/belajar-bermakna-dimulai-dari-rasa-ingin-tahu`

It was discovered from the rendered `/artikel` card DOM, not guessed.

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

In the sampled current article body, paragraph content was present while `h2`, `h3`, and `figcaption` were absent. Those optional longform descendants remain valid classified roles from source evidence but were not falsely claimed as runtime-present.

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

Therefore runtime computed style was required for both list and reader surfaces and has now been captured.

## 8. Runtime proof matrix

Valid runtime computed-style proof exists for both surfaces at all required public widths:

- ID/LTR: 1440 / 768 / 390;
- EN/LTR: 1440 / 768 / 390;
- AR/RTL: 1440 / 768 / 390.

Representative `/artikel` list nodes successfully measured:

- page title;
- subtitle;
- search input;
- category filter action;
- card category;
- card metadata;
- DB-backed card title;
- DB-backed card description.

Representative native reader nodes successfully measured:

- back action;
- DB-backed page title/subtitle;
- author/byline metadata;
- tag action;
- longform root and paragraph;
- author footer;
- related label/title;
- related card metadata/title/description/read-time.

## 9. ID / EN baseline findings

ID and EN resolve to the same typography numerics for equivalent audited list and reader roles. Content length differs, but the role styling baseline is equivalent.

Important list values:

| Role | 1440 | 768 | 390 | Family summary |
|---|---:|---:|---:|---|
| list page title | 60.48px | 33.6px | 50.7px | ui-rounded |
| list subtitle | 19.2px | 18.304px | 17.17px | system-ui |
| card title | 22.32px | 20.48px | 20.48px | system-ui |
| card description | 15.68px | 15.68px | 15.68px | system-ui |

The page title is non-monotonic: `50.7px` at 390px is materially larger than `33.6px` at 768px. This is baseline evidence of current responsive cascade behavior, not an M00 fix.

Important reader values:

| Role | 1440 | 768 | 390 | Family summary |
|---|---:|---:|---:|---|
| reader page title | 66.4px | 53.76px | 43.2px | Charter-style reader serif |
| reader subtitle | 24.8px | 23.04px | 20px | Charter-style reader serif |
| longform paragraph | 20.8px | 18.08px | 18.08px | Charter-style reader serif |
| related section title | 37.6px | 30.72px | 27.2px | Charter-style reader serif |
| related card title | 18.88px | 18.88px | 18.88px | Charter-style reader serif |

## 10. Arabic / RTL baseline findings

Arabic runtime correctly renders `lang="ar"` and `dir="rtl"` on list and reader at all three widths.

The Arabic adapter is active, but the baseline is not yet role-normalized.

Observed list behavior:

- page title uses Cairo;
- subtitle uses Lateef at 36px / 63px line-height across all widths;
- search input uses Lateef;
- category action uses Cairo;
- DB card category/meta use Lateef;
- **DB card title also uses Lateef**, despite its target role being `component-title`;
- card description uses Lateef.

Observed reader behavior:

- back action and page title use Cairo;
- reader subtitle uses Lateef 36px / 63px;
- author/byline metadata use Cairo;
- tag action uses Lateef;
- `.native-article__body` root resolves Lateef 44px / 83.6px;
- actual longform `<p>` resolves Lateef 36px / 63px;
- author-footer description resolves Lateef 36px / 63px;
- related section heading and related-card title use Cairo;
- related-card description resolves Lateef 36px / 63px.

These differences are baseline evidence for the future locale adapter normalization. M00 does not select replacement numeric values.

## 11. Content parity observation outside typography ownership

The AR runtime still displayed some DB tags/author values such as `Pendidikan`, `Program`, and `Tim Al Mustaqbal` unchanged.

This proves those values are DB/content data rather than locale typography rules. M00 records the fact but does not silently translate or rewrite DB content as part of the text-system refactor.

## 12. M00 article conclusion

FACT:

- list and native reader source ownership are mapped;
- major visible/accessibility text groups are classified;
- DB/render-context separation is proven;
- longform is explicitly separated from ordinary body/description roles;
- runtime ID/EN/AR and 1440/768/390 proof is complete;
- optional nodes absent in the sampled state/content are documented rather than invented.

GAP:

- no material M00 evidence gap remains for `/artikel` or the sampled native reader.

DECISION:

- close this surface at M00;
- preserve the non-monotonic list title, Arabic family/scale differences, and untranslated DB content as baseline findings for later implementation/content work;
- do not add implementation changes during M00.

STATUS: PASS

## 13. Next valid step

Proceed to M00 discovery for the next locked public surface: `/ppdb`.

Do not repeat article list/reader source discovery or the completed 3-locale × 3-width runtime audit unless later implementation changes this surface.