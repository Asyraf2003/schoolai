# Article CRUD + Homepage Integration Handoff

Date: 2026-08-28
Repository: `Asyraf2003/schoolai`
Branch: `main`
Audit baseline: `6da7fec4e2bdf1666092418d5682b44633d4355b`

## Scope

This handoff records the current Article architecture before homepage Article data is connected to the database. It covers:

- Article model/domain state;
- admin CRUD for external and native/Canvas articles;
- publication/scheduling;
- public listing and native reader;
- Hero promotion;
- Article media through Cloudflare R2;
- soft delete/restore;
- current homepage Article source;
- known gaps and the agreed next implementation.

## Current assessment

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
| Homepage Article section | 2.5/10 | Still dummy translation content and local placeholder media |

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

## Gap: homepage Article section before this handoff implementation

Before the next implementation step, `HomeArticlesComposer` reads dummy `home_article_preview` items and assigns local media:

- `media/home/2.png`
- `media/home/5.png`
- `media/home/11.png`
- `media/home/12.png`

Every dummy card links only to `/artikel`.

Therefore an Article created in admin currently reaches:

- `/artikel`: yes;
- native detail: yes;
- Hero when promoted: yes;
- homepage Article Lead Rail: **no**.

## Decision for this session

Connect the homepage Article section to the database with the **three latest publicly visible Articles**.

Initial rule:

`Article::latestPublished() -> newest #1, #2, #3`

Presentation target:

- desktop: Article #1 remains the large Lead card; #2 and #3 become the right rail;
- tablet: #1 lead, then #2 and #3 in the supporting grid;
- mobile: #1, #2, #3 flow vertically;
- `Lihat selengkapnya` continues to route to `/artikel`;
- localized title/description/link and real thumbnail are used;
- Hero manual promotion remains independent.

## Pinning decision

A dedicated homepage pinning model is **not** added in this first connection.

Reason: first establish one source of truth and a working automatic latest-three path. Editorial pinning may be introduced later if the school needs to keep an older Article in the homepage set. That future feature should be explicit and separate from `hero_position`, not overloaded onto Hero ordering.

Possible future rule:

`homepage pinned articles first -> fill remaining slots from latestPublished()`

but no schema is authorized by this handoff yet.

## Execution order after this document

1. Replace dummy homepage Article items with the three latest public DB Articles.
2. Preserve the existing section heading, description and CTA translation copy.
3. Adapt Lead Rail layout from 1+3 to 1+2 without changing the broader homepage choreography.
4. Update homepage Article tests from dummy/4-card assumptions to DB-backed/3-card contracts.
5. Verify source state and record the resulting commit SHA in this handoff.
6. Runtime/build tests must be run locally before claiming PASS.

## Out of scope for the immediate homepage connection

- Arabic Canvas autosave fix;
- Article inline-media garbage collection;
- admin search/filter UX;
- permanent Article purge;
- new homepage pin database columns;
- Hero redesign;
- Article listing redesign.
