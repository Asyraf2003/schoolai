# Homepage Values Card Story Blueprint

Blueprint ID: `HOME-VALUES-001`
Status: `IMPLEMENTING`
Owner: Asyraf Mubarak
Date: 2026-08-03
Source main SHA: `4bb9a00eab448aa4b33d28119b2421e3736976db`
Surface: homepage `#nilai`
Execution channel: Web AI with explicit direct-`main` authorization

## Owner goal and reference

Replace the current Values grid with a full-viewport scroll story informed by
the owner-provided Lusion About screenshots. Four real Al Mustaqbal value cards
must begin readable from the front, flip one by one, reveal a school-owned line
pattern on their backs, form a fan, compress into a stack, and leave the scene
while the oversized localized Values heading remains.

The screenshots define chronology, spatial quality, white-card/blue-field
contrast, and editorial scale. Lusion code, assets, marks, branding, card art,
and exact composition are forbidden. The Al Mustaqbal translation uses current
Qur'anic, Innovative, Integrative, and Inspirational content.

## FACT and GAP

- The current DOM is `school-values.blade.php` with four interactive buttons.
- `value-cards.js` owns hover/click/focus active-card state.
- Legacy Values CSS is spread through `welcome/004`, `009`, `010`, and `011`.
- Values content comes from `lang/{id,en,ar}/home.php` through `BuildsHomePage`.
- ID/EN use Inter/LTR; AR uses Cairo/RTL.
- Current main has no dedicated Values surface entry.
- Runtime, six-tier, WebKit, accessibility, build, and performance proof remain
  gaps until executed after publication.

## Scope

In scope:

- replace only the homepage Values semantic DOM and interaction owner;
- add a dedicated Values CSS entry and imported surface modules;
- replace the old Values controller import with one scroll-story controller;
- add a focused feature test and update durable architecture state.

Out of scope and protected:

- Hero, Vision/Mission, Programs, Gallery, Articles, navigation, footer;
- About and Testimonial activation/state;
- DB schema, controller data shape, translations, routes, and admin behavior;
- WebGL, third-party dependencies, Lusion assets, and unrelated legacy cleanup.

## Semantic and fallback contract

- One `section`, one localized `h2`, one description, and four `article` cards.
- DOM reading order stays Q, I, G, N in ID, EN, and AR.
- Front faces carry all meaningful text; card backs are decorative and
  `aria-hidden`.
- No JavaScript, unsupported 3D, and reduced motion render a normal responsive
  card grid with every front visible.
- Scroll motion has no buttons, focus traps, hidden primary actions, or canvas.

## Ownership

| Concern | Target owner |
|---|---|
| DOM/content | `home/sections/school-values.blade.php` + current lang data |
| layout/motion CSS | `css/surfaces/home/values/*` |
| CSS route entry | `css/pages/welcome-values-story.css` |
| interaction state | `js/surfaces/home/values/controller.js` |
| geometry/timeline | `layout.js` and `motion.js` in the same surface |
| route composition | `welcome.blade.php`, `welcome.js`, and `vite.config.js` |

Legacy `.nilai-*` CSS becomes unreachable because the new DOM uses a dedicated
namespace. The old `value-cards.js` owner is removed.

## Storyboard

| State | Scroll result |
|---|---|
| static/failed | localized heading and readable front-card grid |
| enter | four fronts settle into the tier composition |
| flip | cards rotate right-to-left one by one around the Y axis |
| fan | revealed backs spread with bounded Z depth and rotation |
| stack | fan compresses into one centered stack |
| exit | stack moves below the viewport while the heading advances |
| reverse | every state reverses deterministically from scroll progress |
| suspended | offscreen/hidden work stops; resize/BFCache snaps and remeasures |

## Six-tier contract

| Tier | Composition |
|---|---|
| XS 360–639 | one readable stack; compact fan; full-width static fallback |
| SM 640–767 | 2x2 fronts; wider fan; two-column static fallback |
| MD 768–1023 | larger 2x2 fronts and balanced vertical spacing |
| LG 1024–1279 | four fronts in one row; bounded card width |
| XL 1280–1535 | wider four-card field and editorial heading |
| 2XL >=1536 | bounded cards/content with expanded blue cinematic field |

Card width also responds to short viewport height. Navigation is not changed,
so the 1180/1181 contract remains read-only.

## Locale and direction

- The same DOM, order, physical flip order, timing, and neutral 3D rotations are
  shared by ID/LTR, EN/LTR, and AR/RTL.
- Copy and font/direction change through existing server-rendered locale owners.
- Arabic does not reverse time or card order. Semantic typography roles avoid a
  parallel Arabic component.

## Browser, performance, and accessibility

- Capability tier 0: semantic static grid.
- Capability tier 1: CSS 3D plus one bounded JavaScript RAF scheduler.
- No WebGL, media, external package, font, image, or critical-path dependency.
- Frequent animation is transform/opacity only; work pauses offscreen/hidden.
- Chromium and WebKit require runtime proof for sticky, preserve-3d,
  backface-visibility, viewport units, reverse scroll, and short heights.
- Reduced motion keeps all content visible without sticky travel.

## Active execution and proof

1. `ACTIVE`: publish the bounded Values owner replacement to `main`.
2. `PENDING`: run `git diff --check`, `npm run check:structure`, `npm run build`,
   focused/PHP tests, and inspect the first desktop Chromium result.
3. `PENDING`: prove all six tiers, ID/EN/AR, WebKit, reduced motion, zoom, and
   performance.

Commit publication proves source state only. Until the pending gates run, final
status is `BLOCKED_BY_MISSING_EVIDENCE`, not `PASS`.
