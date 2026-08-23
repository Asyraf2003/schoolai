# Blade Presentation-Purity Inventory — 2026-08-23

Status: `D2_PASS / DURABLE`
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Scope: read-only inventory for H5 planning; no runtime/view mutation is authorized by this document.

## Purpose

Freeze the current Blade/PHP presentation debt so H5 can remove view-owned data
preparation without rediscovering scope or blindly moving every expression into
controllers.

Target invariant remains:

- no raw `<?php` in Blade templates;
- no `@php ... @endphp` or `@php(...)` in Blade templates;
- no business/data-access logic in views;
- Blade control directives and escaped presentation expressions remain allowed;
- data normalization, collection shaping, route/menu construction, locale copy
  assembly, and business-state preparation move to their actual application
  owner;
- simple presentation conditions should be expressed directly with Blade rather
  than creating needless service classes.

## Definitive repository inventory

Owner-terminal `rg`/`find` proof on 2026-08-23 found:

- 38 Blade files containing `@php` blocks/expressions;
- 0 raw `<?php` hits reported inside those Blade templates;
- 3 non-Blade PHP files living under `resources/views`.

The three raw PHP files in the view tree are:

- `resources/views/partials/site-navbar/data/context.php`
- `resources/views/partials/site-navbar/data/menu.php`
- `resources/views/partials/site-navbar/data/presentation.php`

`resources/views/partials/site-navbar.blade.php` currently `require`s those three
files from inside an `@php` block. This is a high-confidence H5 migration owner,
not presentation-only Blade.

## Classification A — Home surface data shaping

These views currently prepare collections, translated structures, media maps,
kinetic lines, locale-derived copy, or presentation state before rendering:

- `resources/views/welcome.blade.php`
- `resources/views/home/sections/featured-programs.blade.php`
- `resources/views/home/sections/vision-mission.blade.php`
- `resources/views/home/sections/gallery.blade.php`
- `resources/views/home/sections/gallery-depth.blade.php`
- `resources/views/home/sections/school-values.blade.php`
- `resources/views/home/sections/articles.blade.php`
- `resources/views/home/partials/editorial-section-heading.blade.php`

Proven examples:

- `welcome.blade.php` collects Hero slides and builds Hero status copy, then
  constructs Program/Values kinetic-line data with collection mapping/filtering.
- `featured-programs.blade.php` loads translation data, builds collections,
  defines the six-item media map, generates kinetic lines, chooses media per
  loop, and derives title-size state.
- `vision-mission.blade.php` resolves locale/copy/assets and mutates Arabic
  mission text for the honorific replacement inside the render loop.
- `gallery.blade.php` builds locale-specific heading copy in the view.
- `articles.blade.php` limits/reindexes article collections, chooses opening
  article/CTA, and builds locale-specific display/closing headings.

Target owner:

- existing Home page/controller data-building layer or a focused Home
  presentation builder/view-model;
- locale copy that is content belongs in translation files/data contracts;
- reusable media/presentation metadata belongs in a bounded presenter/config
  owner rather than repeated view-local arrays.

Do not create a service merely to replace a simple Blade condition.

## Classification B — Public page shaping

Current `@php` ownership exists in:

- `resources/views/pages/ppdb.blade.php`
- `resources/views/pages/ppdb/showcase.blade.php`
- `resources/views/pages/artikel.blade.php`
- `resources/views/pages/artikel-detail.blade.php`
- `resources/views/pages/galeri.blade.php`
- `resources/views/pages/partials/gallery-wall-card.blade.php`

Examples include PPDB URL/open-state resolution, audience grouping and initial
audience selection, localized labels, article/gallery page aliases, and gallery
card presentation normalization.

Target owner:

- the corresponding page controller/service/presenter should deliver final
  render-ready data;
- model/service methods remain responsible for business state such as PPDB
  registration availability;
- Blade may keep direct conditional/loop rendering against already prepared
  values.

## Classification C — Shared chrome, locale, and metadata

Current H5 owners:

- `resources/views/partials/site-navbar.blade.php`
- `resources/views/partials/site-navbar/header.blade.php`
- `resources/views/partials/site-navbar/mobile-navigation.blade.php`
- `resources/views/partials/site-footer.blade.php`
- `resources/views/partials/site-head-meta.blade.php`
- `resources/views/partials/language-flag.blade.php`
- `resources/views/layouts/admin.blade.php`
- plus the three raw navbar PHP files listed above.

The navbar is the strongest violation in this group. Current source under
`resources/views/partials/site-navbar/data/` performs:

- current-locale and mode resolution;
- language option construction;
- localized mega-menu copy construction;
- route and anchor construction;
- menu-array mutation/filtering;
- login-item insertion;
- static media URL construction;
- logo/modal presentation preparation.

Target owner:

- a dedicated navbar presenter/view composer/application support owner;
- translatable user-facing copy should live in the locale data contract rather
  than large `match` blocks under `resources/views`;
- route/anchor construction should be prepared before the template renders;
- header/mobile templates should consume the same prepared semantic model.

Footer/meta/language/admin-layout preparation should follow the same pattern:
use a composer/presenter when data must be built, but keep simple conditional
markup in Blade.

## Classification D — Admin forms, archived/replacement state, and lists

Current `@php` ownership exists in:

- `resources/views/admin/ppdb/edit.blade.php`
- `resources/views/admin/ppdb/edit/showcase-list.blade.php`
- `resources/views/admin/hero/form.blade.php`
- `resources/views/admin/site-statistics/edit.blade.php`
- `resources/views/admin/site-statistics/edit/statistics-list.blade.php`
- `resources/views/admin/gallery/page-media/_form.blade.php`
- `resources/views/admin/gallery/form.blade.php`
- `resources/views/admin/gallery/page-sections/form.blade.php`
- `resources/views/admin/gallery/page-sections/show.blade.php`
- `resources/views/admin/gallery/show.blade.php`
- `resources/views/admin/gallery/index.blade.php`
- `resources/views/admin/gallery/index/page-sections.blade.php`
- `resources/views/admin/gallery/index/homepage-items.blade.php`
- `resources/views/admin/placeholder.blade.php`
- `resources/views/admin/articles/form.blade.php`
- `resources/views/admin/articles/index.blade.php`
- `resources/views/admin/testimonials/form.blade.php`

This group mixes two different cases and H5 must not conflate them:

1. actual collection/data shaping such as grouping items and looking up
   replacement candidates;
2. simple presentation aliases/conditions such as `$item->trashed()` or
   assigning one translated page array.

Existing application structure already demonstrates the preferred owner for the
first case:

- `AdminPpdbEditComposer` prepares archived showcase rows and replacement
  candidates before rendering;
- `AdminGalleryIndexComposer` prepares archived Gallery sections and replacement
  candidates before rendering.

Therefore H5 should extend/use controller/composer/presenter ownership for
collection shaping rather than reproduce it in Blade. Simple aliases and state
checks should be removed by rendering the expression directly where practical,
not by manufacturing unnecessary backend layers.

## Logic-type map

Use this classification during H5 execution:

| Logic type | Current examples | H5 destination |
| --- | --- | --- |
| Presentation alias | translated page aliases, `trashed()` local alias | direct Blade expression or already-prepared view value |
| Collection shaping | `collect`, `values`, `filter`, `map`, grouping, candidate lookup | controller/view composer/presenter |
| Business state | PPDB registration state, replacement identity semantics | model/service/controller; Blade consumes boolean/result |
| Routing/navigation | route/anchor building, menu mutation | navbar/page presenter |
| Locale/copy assembly | large locale `match`, manual label arrays | translation data + presenter when composition is required |
| Media presentation map | Program media arrays, static presentation metadata | bounded presenter/config/content owner |
| Render normalization | title sizing, URL/card normalization, fallback selection | presenter/view-model when non-trivial; direct Blade expression when trivial |

## H5 execution order

H5 should be incremental and semantics-preserving:

1. shared navbar/chrome preparation, because it currently contains raw PHP files
   under the view tree and route/locale/menu construction;
2. Home surface data shaping (`welcome`, Program, Values, Vision/Mission,
   Gallery, Article);
3. public PPDB/Article/Gallery page preparation;
4. admin forms/list shaping, reusing/extending existing composers where they
   already establish the correct pattern;
5. remove the three navbar PHP data files only after all consumers have migrated;
6. final repository proof that production Blade has no `@php`/raw-PHP view
   preparation while rendered semantics/tests remain unchanged.

This ordering is an implementation guide, not authorization to perform H5 before
its turn in the hardening sequence.

## H5 acceptance contract derived from D2

H5 may be called complete only when:

- repository search reports no production `@php`/`@endphp`/`@php(...)` in
  `resources/views`;
- no non-Blade PHP data/preparation files remain under `resources/views` unless
  an explicit documented exception is owner-approved;
- views do not query models/DB or build business decisions;
- all ID/EN/AR rendered semantics and LTR/RTL behavior remain unchanged;
- route/CTA/menu behavior remains unchanged;
- admin create/edit/archive/restore/replacement behavior remains unchanged;
- existing tests plus focused migrated-surface tests pass;
- `git diff --check`, structure check, build, and the repository test suite are
  reported honestly against the frozen baseline.

## D2 conclusion

The Blade problem is broad but mechanically bounded: 38 Blade files plus three
raw navbar PHP files. The migration should follow four ownership classes rather
than one giant controller refactor. Existing view composers prove that the repo
already has an appropriate mechanism for admin render-data preparation.

No runtime source was changed during this inventory.
