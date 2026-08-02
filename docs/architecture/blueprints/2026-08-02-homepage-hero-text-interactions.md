# Homepage Hero Text Interactions Blueprint

Blueprint ID: `HOME-HERO-TEXT-001`
Status: `IMPLEMENTING`
Owner: repository owner through the 2026-08-02 implementation brief
Source main SHA: `684328dd3c2f4eb962501aee9d4486cdc6d9ac6c`
Surface: `/` homepage hero
Execution channel: Terminal Codex

## FACT and GAP

- Translation fallback normalizes `flower.mp4` as the first video slide.
- The database composer currently replaces all fallback slides, so an article
  can displace that primary video.
- Article slides carry `article_id` and a public article URL only inside the
  generic CTA contract.
- Blade renders one `h1`, subsequent `h2`, descriptions, and CTAs server-side.
- `welcome-hero.js` currently wraps every title from a generic CTA URL after
  DOM ready; this can falsely link non-article and PPDB slides.
- `PpdbSetting` already owns registration-open state and safe public URL
  normalization.
- Navbar roll is self-contained in navbar Blade/CSS and is already active.
- GAP `HERO-TEXT-001`: explicit primary/PPDB/title-link presentation fields and
  browser proof do not exist.
- GAP `HERO-BROWSER-001`: local Chromium/WebKit availability must be resolved
  before browser claims.

## Goal, impact, and scope

Add server-rendered article title links, conditional primary-video PPDB CTA,
directional title glow, and a hero-only PPDB roll without changing other
homepage surfaces or navbar output.

Editable owners:

- hero controller/provider presentation normalization;
- article-to-hero mapping and `HeroSlide::toHeroArray()`;
- homepage hero Blade;
- hero page JS modules and hero visual CSS;
- ID/EN/AR runtime translations;
- directly related hero tests;
- this blueprint and `UI_UX_CURRENT_STATE.md`.

Forbidden: About, Testimonial, navbar markup/script/style/behavior, other
homepage sections, unrelated admin/public routes, meta title/description,
media loading policy, and new dependencies.

## Decision and presentation contract

- Preserve normalized translation slide zero as the primary video. When
  database/article slides exist, append them after that slide and replace only
  the remaining fallback slides.
- Every normalized slide exposes `is_primary_slide`, `show_ppdb_cta`,
  `ppdb_url`, `ppdb_label`, and `title_href`.
- `show_ppdb_cta` is true only for normalized index zero when it renders as
  video and `PpdbSetting::isRegistrationOpen()` is true.
- An open primary PPDB state suppresses its normal description and normal CTA,
  rendering one server-side PPDB CTA to `publicRegistrationUrl()`.
- A closed state renders normal description and normal CTA with no reserved
  PPDB gap.
- `title_href` originates only from an article mapping, is normalized through
  the existing public-link boundary, and is never inferred from CTA data.
- Keep navbar roll untouched. A hero-owned roll enhancer uses the same
  base/incoming, `translateY`, `rotateX`, and stagger grammar.

## Semantic and motion contract

- Keep exactly one `h1`; later slides remain `h2`; an article anchor stays
  inside its heading and exists in initial HTML.
- Plain semantic title text remains the visible/no-JS source. JS adds only an
  `aria-hidden` decorative glow overlay without changing heading geometry.
- ID/EN use grapheme segmentation with `Intl.Segmenter` and a code-point
  fallback; grouped words preserve normal wrapping.
- AR remains one shaped text run and uses a direction-mirrored gradient sweep.
- Pointer enter/leave and link focus trigger bounded/cancelable animation.
- Through LG on coarse/hover-none input, each activated slide gets one sweep;
  tap behavior and slider swipe remain native.
- Reduced motion removes sweep, stagger, blur, and rotation while retaining
  semantic text, focus, and navigation.

## Six-tier and locale contract

| Tier | Layout/content | Input/motion |
|---|---|---|
| XS 360–639 | existing copy width; no PPDB residue | active-slide sweep once on touch |
| SM 640–767 | existing fluid copy | active-slide sweep once on touch |
| MD 768–1023 | existing copy/arrow geometry | active-slide sweep once on touch |
| LG 1024–1279 | existing composition; prove 1180/1181 | touch sweep or pointer/focus |
| XL 1280–1535 | existing desktop composition | pointer/focus sweep |
| 2XL 1536+ | bounded existing copy | pointer/focus sweep |

ID/EN remain Inter/LTR with left-to-right energy. AR remains Cairo/RTL with a
right-to-left whole-run sweep. Server locale switching reconstructs the same
contract without duplicate DOM/state.

## Capability, performance, and accessibility

- Tier 0: fully usable server HTML and static labels.
- Tier 1: CSS plus bounded Web Animations API; no RAF loop, canvas, WebGL,
  dependency, request, or per-character listener.
- Chromium and WebKit receive the same capability-detected path; unsupported
  animation APIs retain the static result.
- Base text reserves all geometry, decorative layers are absolute and
  `aria-hidden`, and only bounded transform/opacity/filter/background motion is
  used.
- Article focus ring remains distinct from decorative glow. No nested
  interactive element or duplicate accessible label is allowed.

## Execution and proof

- The presentation contract, Blade title link/PPDB branch, hero-owned motion
  modules, translations, and focused tests are implemented locally.
- Focused hero, navbar, and upstream hotfix regression: `PASS`, 15 tests and
  262 assertions.
- Production Vite build: `PASS`, 86 modules transformed.
- `git diff --check`: `PASS`.
- Full PHP suite: `FAIL`, 146/147 tests pass; the remaining failure is the
  pre-existing stale `HomeAboutReelTest` for the protected disabled About
  surface. About was not changed to conceal that conflict.
- Source structure: `FAIL` on the three pre-existing oversized Vision/Mission
  files and the pre-existing stale Hero source checksum.
- Chromium: `PASS` for open/closed PPDB across 360, 640, 768, 1024, 1180,
  1181, 1280, and 1536 in ID/EN/AR, plus touch single-tap and reduced motion.
- WebKitGTK 2.52.5: `PASS` for the same widths, locales, states, pointer,
  keyboard focus, layout, and motion. WebKit touch emulation and reduced-motion
  emulation remain `BLOCKED_BY_MISSING_EVIDENCE` in the available driver.
- Real Safari, Lighthouse/PageSpeed, zoom, orientation, short-height, and field
  CWV remain outside this bounded proof and are not claimed.

## Status and next valid step

Overall release status: `FAIL`. Blueprint remains `IMPLEMENTING`; automated
gate failures prevent push. The next valid execution channel is owner/local
terminal: resolve or formally retire the stale About test and structure
baseline debt in their own accepted scopes, rerun every gate, then repeat the
missing WebKit touch/reduced-motion proof before publication.
