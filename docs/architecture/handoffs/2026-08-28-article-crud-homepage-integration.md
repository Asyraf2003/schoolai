# Article CRUD + Homepage Integration Handoff

Date: 2026-08-28
Repository: `Asyraf2003/schoolai`
Branch: `main`
Audit baseline: `6da7fec4e2bdf1666092418d5682b44633d4355b`

## Scope

This handoff records the current Article architecture before and after the first homepage Article database integration. It covers:

- Article model/domain state;
- admin CRUD for external and native/Canvas articles;
- publication/scheduling;
- public listing and native reader;
- Hero promotion;
- Article media through Cloudflare R2;
- soft delete/restore;
- homepage Article source;
- known gaps and the agreed next implementation.

## Current assessment before homepage integration

Overall Article foundation before homepage integration: **7.4/10**.

| Area | Assessment | Notes |
| --- | ---: | --- |
| Model/domain | 8/10 | Native + external, localized fields, draft/published/scheduled, soft delete |
| Admin CRUD backend | 8/10 | Store/update cleanup and restore flow are already separated into concerns |
| Admin UX | 7.5/10 | Usable, but no search/status/source filters yet |
| Canvas/editor | 6.5/10 | Strong custom editor; Arabic autosave parity gap remains |
| Sanitization/security | 9/10 | Server-side allowlist and URL restrictions are strong |
| R2/Cloudflare media | 8.5/10 | Canonical media origin and verified uploads; inline content GC still missing |
| Soft delete/restore | 8.5/10 | Public hiding + admin archive + restore are protected by tests |
| `/artikel` listing | 8/10 | Reads published Article records from DB |
| Native article reader | 8.5/10 | Publication visibility, localization, related articles, admin preview |
| Hero Article integration | 9/10 | DB-backed explicit promotion and ordered runtime slides |
| Homepage Article section | 2.5/10 | Was dummy translation content and local placeholder media |

## Facts: Article domain

`App\Models\Article` owns both external and native content.

Important state:

- `article_source`: `external` or `native`;
- `article_status`: `draft`, `published`, or `scheduled`;
- localized title/subtitle/description/content/link fields;
- tags and word count;
- thumbnail URL;
- `published_at` / `scheduled_at`;
- `hero_position`;
- Laravel `SoftDeletes`.

`latestPublished()` is the canonical public ordering and visibility scope. It orders by `published_at DESC`, then `id DESC`.

## Facts: admin CRUD

There are two authoring paths:

1. **External article** through the normal form (`Tambah Link Medium`).
2. **Native article** through the custom Article Canvas (`Buat via Canvas`).

External Article validation already covers:

- required Indonesian title and URL;
- optional EN/AR fallback fields;
- public URL validation;
- safe image validation;
- thumbnail upload to R2;
- publication date;
- replacement thumbnail cleanup only after the database update succeeds.

The admin list includes active/draft/scheduled/deleted states, detail/edit actions, soft delete, restore, and `Pulihkan & Gantikan` for an archived external article whose normalized Indonesian source URL matches an active replacement.

### Remaining admin UX debt

Not a blocker for homepage activation, but future hardening should add:

- title search;
- status filter (`active`, `draft`, `scheduled`, `archive`);
- source filter (`Canvas`, `external`).

## Facts: Canvas/editor

The active native editor is **not TinyMCE**. It is the project-owned contenteditable Article Canvas.

It currently owns:

- autosave;
- formatting/context toolbars;
- ID/EN/AR editor UI;
- image upload;
- thumbnail upload;
- Unsplash search;
- publish/schedule drawer;
- server-side sanitization;
- public native rendering.

### Known P1 gap: Arabic autosave parity

The frontend can emit:

- `title_ar`;
- `subtitle_ar`;
- `content_ar`.

However the inspected backend autosave path currently validates/updates ID and EN only. This contradicts the existing Arabic feature test contract. This must be corrected before the Canvas can be considered fully hardened.

This handoff does **not** authorize replacing Canvas with TinyMCE. The target is to harden one editor, not maintain two competing authoring pipelines.

## Facts: media architecture

Runtime Article uploads use `R2MediaStorage` with the configured S3-compatible media disk.

Canonical public origin:

`https://media.almustaqbal.sch.id`

Object namespaces:

- `articles/thumbnails/{scope}/{uuid}.{ext}`
- `articles/content/{article-id}/{uuid}.{ext}`

The storage service verifies the object exists after upload. Public URLs are derived from the canonical media origin through `MediaUrlResolver` rather than trusting arbitrary client URLs.

### Thumbnail lifecycle

Thumbnail replacement is reference-aware. An old owned thumbnail is not deleted if it is still referenced by any active or soft-deleted Article thumbnail/content field.

### Remaining P1 media gap

Inline Canvas content uploads do not yet have complete reference reconciliation / garbage collection. An uploaded content image can become orphaned in R2 if the author later removes it from the document.

Recommended future shape:

- explicit Article media ownership metadata or an equivalent reconciliation manifest;
- mark references on autosave/publish;
- purge unreferenced objects only after a safe grace period.

## Facts: soft delete

Article delete is intentionally reversible:

`active -> deleted_at set -> hidden publicly -> retained in admin archive -> restore`

Media is retained during soft delete so restore remains lossless.

There is currently no application `forceDelete` path. A permanent-retention policy still needs to be made explicit later: archive forever, or a guarded purge after a retention period.

## Facts: public Article surfaces

### `/artikel`

The Article listing already reads `Article::latestPublished()` and maps localized title, description, author, date, categories, link and thumbnail.

### `/artikel/{article:slug}`

The native reader already enforces native/public visibility, allows admin preview, resolves locale content, reading time, tags and related articles.

## Facts: Hero Article

Hero Article is already database-backed and should **not** be rebuilt.

Flow:

`published Article -> explicit admin promotion -> hero_position -> promotedInHero() -> homepage Hero slide`

Opening Hero remains first. Article Hero entries are manually selected and ordered. Draft, future scheduled and soft-deleted Articles are automatically excluded from public Hero output.

This manual editorial promotion is intentionally separate from the normal Article homepage section.

## Previous gap: homepage Article section

Before this implementation, `HomeArticlesComposer` read dummy `home_article_preview` items and assigned local media:

- `media/home/2.png`
- `media/home/5.png`
- `media/home/11.png`
- `media/home/12.png`

Every dummy card linked only to `/artikel`.

Therefore an Article created in admin reached `/artikel`, native detail and optionally Hero, but not the homepage Article Lead Rail.

## Decision implemented in this session

The homepage Article section is now connected to the database with the **three latest publicly visible Articles**.

Current rule:

`Article::latestPublished() -> newest #1, #2, #3`

Presentation:

- desktop: Article #1 is the large Lead card; #2 and #3 are the right rail;
- tablet: #1 lead, then #2 and #3 in the supporting grid;
- mobile: #1, #2, #3 flow vertically;
- `Lihat selengkapnya` continues to route to `/artikel`;
- localized title/description/link are derived from the Article model;
- real Article thumbnail URLs are used, including the canonical R2 public URL;
- the first Article tag is used as card category, with a localized generic fallback;
- native articles show localized reading time plus publication date;
- external articles show publication date;
- the homepage query selects presentation fields only and does not select `content_id`, `content_en`, or `content_ar`;
- Hero manual promotion remains independent.

Dummy item arrays were removed from ID/EN/AR `home_article_preview` translations. Those translation files own only section heading, generic category fallback and the listing CTA label.

### Presentation simplification after DB integration

The homepage Article surface was simplified after the database connection:

- the descriptive paragraph below the section display heading was removed;
- the per-card `Baca artikel ↗` / `Read article ↗` / Arabic equivalent cue was removed;
- each Article card remains one full clickable `<a>` surface, so the extra nested read cue was redundant;
- the card's own article description remains inside the media overlay;
- the separate `Lihat selengkapnya` listing CTA remains unchanged, including its LTR/RTL directional arrow behavior.

The removed presentation copy and selectors were also deleted from the composer, ID/EN/AR translations and Article showcase CSS rather than merely hidden.

### Implementation source checkpoints

Database-backed homepage Article source and initial tests were implemented through commit:

`4c140476d6af88e0c190c82af81f6224523b6420`

The later presentation simplification is covered by the source/runtime contracts updated after that integration.

Key implementation files:

- `app/View/Composers/HomeArticlesComposer.php`;
- `resources/views/home/sections/articles.blade.php`;
- `resources/css/surfaces/home/article-showcase/base.css`;
- `resources/css/surfaces/home/article-showcase/typography.css`;
- `resources/css/surfaces/home/article-showcase/responsive.css`;
- `lang/id/home_article_preview.php`;
- `lang/en/home_article_preview.php`;
- `lang/ar/home_article_preview.php`;
- `tests/Feature/HomepageArticleSectionTest.php`;
- `tests/Feature/HomeArticleStorySourceTest.php`.

## Pinning decision

A dedicated homepage pinning model is **not** added in this first connection.

Reason: establish one source of truth and a working automatic latest-three path first. Editorial pinning may be introduced later if the school needs to keep an older Article in the homepage set.

That future feature must be separate from `hero_position`.

Candidate future rule:

`homepage pinned articles first -> fill remaining slots from latestPublished()`

No homepage pin schema is authorized yet.

## Verification status

Source-level verification completed for the DB integration and simplified presentation contracts:

- homepage composer no longer reads dummy item arrays;
- homepage composer uses `latestPublished()` and `limit(3)`;
- desktop Lead Rail is 1 + 2;
- section description and redundant per-card read cue have been removed at source level;
- homepage tests target real DB Article records and three-card ordering;
- Hero Article code was not redesigned;
- no homepage pin schema was added.

Runtime/build/test execution for the latest presentation simplification must still be run locally before marking it PASS.

Recommended local gate:

```bash
sai pull
php artisan test --filter=HomepageArticleSectionTest
php artisan test --filter=HomeArticleStorySourceTest
npm run build
```

If the focused tests pass, run the broader Article test group before deployment.

## Remaining Article hardening after homepage connection

Priority order:

1. fix Arabic Canvas autosave parity;
2. add inline Article media ownership/reconciliation for R2 orphan cleanup;
3. add admin Article search/status/source filters;
4. decide archive-forever versus guarded permanent purge policy;
5. only then evaluate homepage pinning if editorial usage proves it is needed.

## Out of scope for this implementation

- Arabic Canvas autosave fix;
- Article inline-media garbage collection;
- admin search/filter UX;
- permanent Article purge;
- new homepage pin database columns;
- Hero redesign;
- Article listing redesign.
