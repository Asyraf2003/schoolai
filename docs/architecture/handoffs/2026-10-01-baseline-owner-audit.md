# MAP-00A owner and removal audit

Source: `ebdc0db5`. Date: 2026-10-01. Issue: #46.
Scope: owner-authorized canonical structure prerequisite; preserve behavior.

## Active owners

Every active over-limit file is USED_AND_EFFECTIVE / ACTIVE_REQUIRED.
Extract along these existing responsibilities; concatenate ordered CSS without
changing declarations. Preserve PHP methods/transactions and Blade DOM order.

| Existing owner | Bounded responsibility |
|---|---|
| ManagesArticles | CRUD, homepage placement/order, archive/restore |
| HeroAdminController | Opening settings vs Article spotlight placement/order |
| GalleryPageController | collection query vs trusted media presentation |
| SiteNavbarMenuPresenter | menu composition vs Gallery/Article media menus |
| public-auth-perspective.css | perspective shell, login panel, direction/responsive |
| welcome-hero-visual.css | Hero visual vs audio/navigation presentation |
| vision base.css | geometry/content vs RTL adapter |
| vision about-video.css | preview cue, modal frame, controls/fullscreen/responsive |
| article-showcase base.css | shell/empty, cards, CTA/interaction |
| public article-detail.css | Hero, body/related, responsive |
| public gallery.css | grid/media, reveal effects, modal/responsive |
| about-video-modal.js | preview lifecycle, labels/utilities, fullscreen, modal |
| cursor.js | top-layer/assets, gesture detection, pointer/emotion orchestration |
| gallery-wall.js | media measurement, grid/reveal, modal, bootstrap |
| values paint.js | color/opacity calculation vs frame painting |
| admin articles index | placement panels vs list/forms |
| vision-mission Blade | semantic story/media vs dialog |
| admin article-placement styles | placement grid/item vs row actions/responsive |

Cursor emotion extraction is strictly equivalence work in this prerequisite;
the following cursor map removes it under the already accepted runtime scope.

## Confirmed unreachable CSS source files

For each file below, inspected all app/Blade/config/routes/JS/CSS/Vite references,
dynamic import/URL generation and tests, not just class-name search. No runtime
entry or importer exists, including public Gallery, PPDB, auth/admin and homepage.
The current build graph and three local browser request lists do not deliver
these files. Existing source ownership tests explicitly exclude retired imports.

The state column records states present in the unreachable file. A breakpoint,
dynamic class, dataset, modal, fullscreen or no-JS state cannot load a stylesheet
that has no runtime edge. Required fallback/reduced rules remain in the active
surface/core owners. This classifies whole files, never similarly named live
selectors. No active stylesheet declaration is removed.

Paths below are relative to `resources/css/pages/welcome/`.
All rows are `DEAD_CONFIRMED` as production files. Test reads of the old Gallery
file are stale: current public Gallery entry already owns those effects/modal.
Those assertions must follow the active replacement, retaining every effect.

| Source file | State/dependency audit | Supersession evidence |
|---|---|---|
| 003-8-quick-info.css | hover/hidden | 08d1cbab; current shell/core |
| 004-11-nilai-sekolah.css | hover/hidden | aaa60de2; Values story adapter |
| 007-welcome-cascade-007.css | empty comment | 6c9ab6ec; retired import |
| 008-welcome-cascade-008.css | empty comment | 6c9ab6ec; retired import |
| 009-welcome-cascade-009.css | hidden | aaa60de2; Values story adapter |
| 010-welcome-cascade-010.css | dynamic is-* | aaa60de2; Values story adapter |
| 011-welcome-cascade-011.css | media/hover/reduced/is-* | aaa60de2; Values story adapter |
| 012-welcome-cascade-012.css | hidden | aaa60de2; Program adapter |
| 013-welcome-cascade-013.css | dynamic is-* | aaa60de2; Program adapter |
| 014-gallery-teaser-data-ready-local.css | media/hover/reduced/hidden | aaa60de2; Program/Gallery adapters |
| 015-premium-compact-gallery-buffer-like-centered-header.css | media | aaa60de2; Gallery adapter |
| 016-edit-di-sini-jarak-antara-teks-kiri-dan-gambar-kanan.css | is-*/hidden | aaa60de2; Gallery adapter |
| 017-edit-di-sini-mobile-jangan-geser-gambar-di-tablet-hp.css | media/hover | aaa60de2; Gallery adapter |
| 018-editorial-article-layout-featured-story-compact-side.css | media/hidden | aaa60de2; Gallery/Article adapters |
| 019-welcome-cascade-019.css | media/hover | aaa60de2; Article showcase |
| 020-kartu-program-dipindah-ke-kiri-spotlight-besar-ke-ka.css | media/hidden | 6c9ab6ec; Program/Article adapters |
| 036-welcome-cascade-036.css | media/hover | 41ab16fa; public Article Detail |
| 037-welcome-cascade-037.css | media/hidden | 41ab16fa; public Article Detail |
| 038-welcome-cascade-038.css | keyframes | 65eab399; PPDB liftoff |
| 039-welcome-cascade-039.css | media/reduced/is-*/hidden | 65eab399; PPDB liftoff |
| 040-welcome-cascade-040.css | hidden | ee502424; public Gallery |
| 041-welcome-cascade-041.css | hover/is-*/hidden | ee502424; public Gallery |
| 042-welcome-cascade-042.css | media/reduced/is-* | ee502424; public Gallery modal |
| 043-gallery-page-sections.css | media | ee502424; public Gallery |
| 043-welcome-cascade-043.css | media/data/is-*/hidden | ee502424; public Gallery |
| 044-scoped-typography-rhythm-only-do-not-touch-gallery-s.css | media/is-*/hidden | ee502424; public Gallery/core lazy media |
| 046-social-video-covers-provider-identity-without-loadin.css | media/hover/reduced/hidden | 65eab399; public surface owners |
| 047-welcome-cascade-047.css | media | 65eab399; public surface owners |
| 049-gallery-codrops-navigation.css | media/hover/reduced/is-*/hidden/modal | ce661339; public Gallery adapter |

CSS consumers with identical selector names in replacement files remain intact.
No global cascade, fallback, modal/fullscreen or responsive behavior is retired.

## Source equivalence drift

The manifest still lists the old welcome generation after its imports were
intentionally retired in the commits above. Reconcile orderedModules and the
current combined checksum only after this consumer audit. Hero/shared mega
checksums also drifted with accepted prior edits; preserve their existing import
order and capture current source. This updates evidence; it does not change the
checker, thresholds or runtime import graph.

## Proof boundaries

Compare every emitted entry CSS hash against `/tmp/schoolai-baseline-build-hashes.json`.
Run existing owner behavior/source contracts, full PHP, structure, build, Pint
and diff. Source-contract tests follow extracted owners rather than dormant files.
Browser composition, modal controls and no-JS fallback still require runtime
proof; test/build success alone cannot certify them.

## Verified result

PASS: full PHP 304 tests / 3,487 assertions, targeted Article 1/85 and
media/Hero/Gallery 7/234, Node fullscreen runtime 1 test; structure 592 files
≤200 lines, build 142 modules, Pint/diff. All 29 emitted CSS entry SHA256s
match the original build exactly. Chromium 153 at 1440×900 reproduces original
SSR section order/startup; no exceptions; native modal opens/closes and retains
Indonesian fullscreen label. Fullscreen extraction lost its labels closure in
an intermediate build: browser caught it, explicit parameter fixed it, and
the new executed controller test covers all locale labels and API failure.
Raw local proof: `/tmp/schoolai-home-equivalence-*`. These tests certify owner
extraction, not production CWV or Safari parity.
