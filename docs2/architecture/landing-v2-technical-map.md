# Landing V2 technical map

> HISTORICAL CHECKPOINT 2026-10-04. Arahan terbaru:
> [MAP-V2-02](../blueprints/menu-hero-correction.md). EN-only sudah diganti
> dengan bahasa aktif EN/ID/AR. Dilarang merge sampai owner UI review.


STATUS: VERIFYING / BLOCKED_BY_MISSING_EVIDENCE untuk penutupan final.
OWNER_RAW: [verbatim source](../owner/landing-v2-raw.md).
OWNER_CONFIRMED / SCOPE / OUT_OF_SCOPE: [active blueprint](../blueprints/landing-shell-menu-hero.md).
AI_TRANSLATION: contracts below describe implemented ownership; proof completeness is recorded separately.
AI_ASSUMPTIONS: NONE promoted to requirements.

## SOURCE — LEGACY_OBSERVATION

All paths below are read-only references. Prefix `old/` means `resources_old/`.
Families identify related files inspected for the same bounded concern, not permission to migrate a tree.

| SOURCE | Classification | RESPONSIBILITY / observation |
| --- | --- | --- |
| old/views/welcome.blade.php | VISUAL_REFERENCE | Semantic DOM and ordered Vite entries; inline critical styles; includes out-of-scope sections |
| old/views/home/sections/hero.blade.php | VISUAL_REFERENCE, MEDIA_SOURCE | Slides, poster/video source hydration, arrows, accessible state |
| old/views/home/partials/hero-title.blade.php | DATA_SOURCE, URL_SOURCE | Eyebrow/title/description/CTA and campaign links |
| old/views/partials/home-hero-copy-layout.blade.php | VISUAL_REFERENCE | Inline copy geometry overriding component CSS |
| old/views/partials/site-navbar.blade.php | VISUAL_REFERENCE | Header assembly; inline styles/scripts and mega CSS entry |
| old/views/partials/site-navbar/header.blade.php | VISUAL_REFERENCE, URL_SOURCE | Desktop links, mega panels, audio controls, hamburger |
| old/views/partials/site-navbar/mobile-navigation.blade.php | VISUAL_REFERENCE, URL_SOURCE | Separate duplicate mobile nav tree, backdrop, nested panels |
| old/views/partials/site-navbar/behavior.blade.php | BEHAVIOR_REFERENCE, LANG_SOURCE | Language modal, focus, body scroll, manual menu fallback mutation |
| old/views/partials/site-navbar/mega-roll-script.blade.php | BEHAVIOR_REFERENCE | Label grapheme rotation through WAAPI; observer watches class changes |
| old/views/partials/site-navbar/styles/{responsive,desktop-mega-layout}.blade.php | VISUAL_REFERENCE | 1180/1181 switch, language styling, panel geometry |
| old/js/pages/welcome-hero.js | BEHAVIOR_REFERENCE | Starts both Hero and mega menus: ownership conflict |
| old/js/pages/welcome-hero/{opening,carousel,slider-media,slider-playback,mega-menu}.js | BEHAVIOR_REFERENCE, MEDIA_SOURCE | Media/playback/state/timers plus unrelated Header ownership |
| old/js/pages/welcome/{navigation,navigation-state,navigation-header-visibility,navigation-menus}.js | BEHAVIOR_REFERENCE | Scroll thresholds, active links, lazy mobile controller, duplicate language handling |
| old/js/pages/mobile-navigation-cinematic.js | BEHAVIOR_REFERENCE | State open/closed, body lock, reverse exit with 920ms timeout |
| old/css/pages/{welcome-critical,welcome,welcome-home-hero,welcome-hero,welcome-hero-carousel}.css | VISUAL_REFERENCE | Import graph and delivery order |
| old/css/pages/welcome-hero/{004-full-viewport-mixed-media-hero,005-welcome-hero-cascade-005,006-welcome-hero-cascade-006,007-tablet-and-mobile-navigation-hero-behavior,008-welcome-hero-cascade-008,009-welcome-hero-cascade-009,carousel-home-layout}.css | VISUAL_REFERENCE | Full viewport media, responsive copy, arrow geometry, Header states |
| old/css/pages/{welcome-hero-appearance,welcome-hero-visual,welcome-hero-motion,mobile-navigation-cinematic}.css | VISUAL_REFERENCE, BEHAVIOR_REFERENCE | Shadows/overlay, scale+blur transitions, mobile reveal |
| old/css/pages/welcome-home-type-latin.css | VISUAL_REFERENCE | Text-system then Latin adapter |
| old/css/{text-system,public-latin-inter,arabic-typography,arabic-typography-base,arabic-type-scale}.css | VISUAL_REFERENCE, LANG_SOURCE | Type roles, local Inter, Cairo RTL adapter; AR only inspected for future boundary |
| lang/en/{home,home_parity,shared}.php | DATA_SOURCE, LANG_SOURCE, URL_SOURCE | Fallback content, labels, menu destinations; not final DB-resolved content |
| config/media.php | MEDIA_SOURCE | Canonical CF media mapping, logo/nav/poster/video |
| routes/web/public.php, app/Http/Middleware/SetLocale.php | URL_SOURCE, LANG_SOURCE | Existing route names and session/cookie/default locale resolution |
| app/Http/Controllers/{HomeController,Concerns/BuildsHomePage,Concerns/BuildsHomeHero}.php | DATA_SOURCE | Initial home/lang normalization; whole homepage delivery must not power V2 |
| app/View/Composers/{HomePageComposer,HomeHeroComposer}.php | DATA_SOURCE | Unrelated program copy and Hero slide presentation |
| app/View/Presenters/{SiteNavbarPresenter,SiteNavbarMenuPresenter,Concerns/PreparesNavbarMediaMenus}.php | DATA_SOURCE, URL_SOURCE | Effective links differ from raw lang; DB gallery categories/article tags |
| app/Support/HomeHeroPresentation.php | DATA_SOURCE, URL_SOURCE | Campaign vs article linking normalization |
| app/Providers/Concerns/{InjectsDatabaseHero,BuildsArticleHeroSlides,RegistersHeroIntegration}.php | DATA_SOURCE | Composer replaces slides with opening + promoted articles; PPDB override |
| app/Models/HeroSetting.php | DATA_SOURCE, LANG_SOURCE | Locale-backed DB values; no model/admin mutation authorized here |

## Effective data pipeline

LEGACY_OBSERVATION:
`home + home_parity → canonical media mapping → BuildsHomeHero → welcome composer`
`→ DB HeroSetting + PPDB registration state + promoted article slides → HomeHeroPresentation`.
Ignoring the composer changes content. Article slides inside Hero are not the out-of-scope article section.
Local read-only DB audit: PPDB open; promoted article list empty; fallback setting title
`Rooted in the Qur’an. Moving Forward with Innovation.` PPDB campaign takes precedence.
Production DB equality is UNKNOWN. Never seed/mutate DB to manufacture matching evidence.

## OWNER / PORT / ADAPTER — AI_TRANSLATION

| OWNER | RESPONSIBILITY | INPUT / EVENT | OUTPUT / STATE | PORT / ADAPTER |
| --- | --- | --- | --- | --- |
| Header core | One open panel, compact/desktop state, close policy | toggle, dismiss, breakpoint change | closed/mobile/panel key | render callback; DOM adapter handles events/aria/focus |
| Header browser adapter | DOM, focus return/trap when modal, lifecycle | click/Escape/outside, viewport | aria-expanded, visibility; cleanup | matchMedia feature detection; scoped root |
| Header scroll | Own Header treatment/visibility | scroll distance, Hero boundary, focus/menu state | scrolled/hidden | browser measurements input to pure policy; no Hero writes |
| Hero core | Active slide, pause/audio intent, autoplay eligibility | next/previous/end/visibility/reduced motion | slide index, playback intent | media + schedule ports only where used |
| Hero browser adapter | Media element, source hydration, failure/poster | play success/rejection/error | actual playback/audio availability | HTMLMediaElement; observers optional |
| Composition entry | Connect existing ports only | Header audio intent | Hero command; Header status update | injected functions; no global manager/event bus |
| Foundation CSS | Reset/tokens/font/focus/direction/media baseline | cascade | baseline presentation | no Header/Hero selectors |
| Component CSS | Layout/state/motion of its own surface | component attributes/classes | visual treatment | fluid sizing; logical properties; progressive enhancements |

## INVARIANT / FALLBACK / BROWSER SUPPORT

- Core modules import no DOM/browser objects; tests can drive state with plain data/functions.
- Header never edits Hero internals; Hero never queries Header controls.
- Canonical video: `config('media.homepage_hero_video_url')`, poster: `media.static.hero_school`.
- One semantic component tree per purpose; no separate locale/device implementations.
- Native links usable without enhancement; hidden panel focus must remain inaccessible.
- Poster and text survive media failure, blocked autoplay, no JS, reduced motion.
- `100vh` baseline with supported small/dynamic viewport upgrade; content can grow at zoom.
- Optional blur/WAAPI/IntersectionObserver features must have usable baseline and cleanup.
- Old carousel arrow logic assumes LTR; new direction input must keep future RTL possible.
- Browser execution results are in ../proof/landing-v2-status.md; WebKit is not branded Safari.
- Source review alone is not BROWSER VERIFIED or visual parity proof.

## LEGACY REPLACEMENT / risks

Legacy Hero bootstrap starts menu; replace with Header-owned initialization.
Legacy opening/carousel query global audio buttons; replace with explicit audio command/status port.
Legacy language modal manually edits menu classes/styles; EN-only does not run that path.
Legacy inline + imported CSS ownership overlaps; derive component rules instead of importing layers.
Legacy mobile close timeout/inline CSS fallback must not be copied as architecture.
Legacy carousel has userPaused state but passes playbackButton:null: provide usable pause control
when motion is implemented; this is an accessibility requirement, not proof it exists today.
Global readiness/scroll-gate is out of scope; poster/content do not wait for global readiness.

## UNKNOWN / NEEDS_OWNER_DECISION

- OWNER_CONFIRMED: missing-section links stay active with their original URL.
- "no 3" and "no 4 a" antecedents unavailable; no requirement inferred.
- Computed CSS winners and visual runtime parity not yet measured.
- Live production content/media availability not yet certified.

## NEXT VALID STEP

Complete remaining visual/browser proof and review draft PR; do not add another section.

## Implemented files and boundaries

- `resources/views/landing/{index,header,hero,hero-copy}.blade.php`: semantic delivery only.
- `resources/css/foundation/*`: base, tokens, local Inter font; font bytes copied with OFL license.
- `resources/css/sections/{header,hero}.css`: component-owned responsive layout and motion.
- `resources/js/sections/header-state.js`: pure policy; `header.js`: DOM/focus/scroll adapter.
- `resources/js/sections/header-motion.js`: optional label-roll browser adapter, no state owner.
- `resources/js/sections/hero-state.js`: pure policy; `hero-media.js`: media operations;
  `hero.js`: DOM/lifecycle/scheduling adapter.
- `resources/js/index.js`: mounts components, wires audio/boundary functions and page lifecycle.
- `HomeController`: sets request locale EN (session/cookie unchanged), delivers landing view.
- `LandingHeroPresenter`: reuses existing server-only Hero data traits; does not invoke
  legacy view injection or mutate provider registration. Existing data normalization stays shared.
- `SiteNavbarPresenter`: reused for canonical URL/media/database menu content.
- `tests/Feature/LandingV2Test.php`, `tests/Unit/LandingV2Runtime.test.mjs`: scoped regression coverage.

Additional VISUAL_REFERENCE inspected: old/css/pages/welcome-mega-menu/001..003,
old/css/pages/welcome-hero/carousel-controls.css, bounded text search in welcome/030..035.
Font MEDIA_SOURCE: old/fonts/inter/inter-latin-variable.woff2 and OFL.txt.
No runtime import to old source; source archive remains unchanged.
