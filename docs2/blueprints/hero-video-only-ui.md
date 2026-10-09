# MAP-V2-16 — Hero V2 video-only UI

STATUS: CLOSED (scoped CI PASS; existing repository security baseline FAIL)
SOURCE BASE MAIN: ef62c3023ca27da15fd02cab994e6b9572315ffc
MERGE MAIN: 3b9e269798be58315c54d403186790ec2db465d0
ISSUE: https://github.com/Asyraf2003/schoolai/issues/80
BRANCH: fix/hero-v2-video-only-ui

## OWNER_RAW

“fokus ui nya dlu ajaa agar bener\" cuma nampilkan video itu aja siii bisa lu pr isue push main?”

Earlier context: Hero should have no articles, only the video; heading to PPDB is desired in a separate follow-up.

## AI_TRANSLATION

The active V2 homepage Hero displays precisely one configured opening video and its existing text overlay. Remove promoted articles from the V2 presentation data, so previous/next controls never render. Preserve the video poster fallback.

## AI_ASSUMPTIONS

“Cuma menampilkan video” refers to Hero media/slide count. Existing title, description, CTA, navigation header and sound toggle remain because owner explicitly narrowed this turn to UI without authorizing a copy/PPDB-contract rewrite.

## OWNER_CONFIRMED

Only Hero V2 UI is in scope now. PR, issue and merge to main requested.

## SCOPE

- app/View/Presenters/LandingHeroPresenter.php
- Focused V2 tests and the scoped verification workflow
- docs2 map and handoff

## OUT_OF_SCOPE

- Legacy welcome Hero and its carousel
- Article CRUD, hero_position data, admin Hero/Article controls
- /ppdb registration-open/closed policy and link behavior
- Hero copy, Header, About, Program, Values, SVG and background

## LEGACY_REFERENCE

NONE. Existing V2 openingHeroSlide and video runtime are retained.

## FACT

V2 previously appended promotedArticleHeroSlides to openingHeroSlide. Its Blade renders arrows only when there are multiple slides, and hero-media loops the video when exactly one slide exists. The configured URL is media.homepage_hero_video_url with a static poster fallback. PPDB title links are currently conditional on registration being open.

## DECISION

Produce one opening slide via the existing V2 presenter. Keep the shared old/admin article placement pathway unchanged. Existing conditional controls do not appear with a single slide. Preserve present media URL, title, overlay, a11y and audio contract without new JS/CSS.

## PROOF

PROVEN: [Hero video-only CI](https://github.com/Asyraf2003/schoolai/actions/runs/37997336131) completed SUCCESS: Chromium/WebKit, ID/EN/AR, 390px and 1440px, one video/slide, controls hidden, looping/poster and RTL. Scoped PHP feature tests (LandingV2, HeroArticlePlacement, HeroDatabaseFallback), source structure, Vite build and media runtime unit tests PASS. Screenshots are available as workflow artifacts.

The first CI attempt also included the unchanged PublicAuthAndPpdbAccessTest.php, which failed 13 existing legacy-view / navigation expectations (missing auth.login-perspective and pages.ppdb views; login-link count mismatch). All 10 other selected tests passed. The unrelated legacy suite is explicitly excluded from this V2 Hero UI acceptance gate, not silently reported green.

## GIT

Issue #80 CLOSED; branch fix/hero-v2-video-only-ui; PR #81 MERGED to main at 3b9e269798be58315c54d403186790ec2db465d0. Repository-wide security audit still FAILs on the pre-existing dependency findings; no dependencies changed.

## NEXT VALID STEP

No further Hero UI action in this scope. PPDB heading/link contract and admin Article Spotlight cleanup are separate, owner-gated follow-ups.
