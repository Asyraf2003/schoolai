# Homepage About / Vision / Mission Pinned Reveal

BLUEPRINT ID: `HOME-AVM-001`
STATUS: `IMPLEMENTING`
OWNER: Asyraf
DATE: 2026-08-08
SOURCE MAIN SHA: `66eb28b1467b3f4e3ba77f9ef9af4b8379f1a78d`
ACTIVE ROUTE/SURFACE: `/` -> About / Visi / Misi before Program
TARGET EXECUTION CHANNEL: Web AI GitHub mutation + owner/local runtime proof

## Owner goal and reference

Replace the previous Visi/Misi choreography completely with the interaction concept from `https://codepen.io/gridmorphic/pen/WbQPRwv`.

SchoolAI translation:

- exactly three narrative states: About, Vision, Mission;
- existing SchoolAI images only;
- on capable wide layouts, narrative copy scrolls while one image field stays pinned and stacked images reveal through a vertical mask with mild parallax;
- compact layouts use ordinary sequential story/image flow;
- Program remains the next independent section below this surface.

Reference facts observed from the Pen:

- desktop uses a pinned image column, stacked images, scroll-scrubbed `clip-path` reveal, and image-position parallax;
- compact mode removes the desktop pin and interleaves copy/media;
- the Pen uses GSAP, ScrollTrigger, and Lenis.

The SchoolAI implementation must not copy its assets, global body styling, Outfit font, Lenis ownership, or exact code. Native project-owned RAF smoothing is preferred to avoid a new global scroll owner.

## FACT / GAP

FACT:
- current homepage renders `vision-mission` immediately before `featured-programs`;
- existing surface owns three local `vision-paper-0*.webp` images;
- ID/EN/AR already provide About story content and Visi/Misi content;
- Program currently contains an integration adapter that can move Program into the old Visi/Misi track.

GAP:
- GitHub mutation cannot prove browser rendering, WebKit parity, PageSpeed, reduced-motion appearance, or exact visual fidelity to the reference.

## Scope

SCOPE IN:
- `resources/views/home/sections/vision-mission.blade.php`
- `resources/css/pages/welcome-vision-waapi/**`
- `resources/js/pages/welcome-vision-story.js`
- `resources/js/surfaces/home/vision-story/**`
- Program's obsolete Visi/Misi integration boundary only
- focused tests and current-state documentation

SCOPE OUT:
- Program's own local visual journey after the boundary;
- Values, Gallery, Articles, Hero, navbar, footer;
- public content wording and language files;
- WebGL/3D.

## Semantic experience

DOM reading order is About -> Vision -> Mission -> Program. All meaningful copy stays in Blade. Images are decorative complements. Without JS, on compact layouts, reduced motion, or enhancement failure, the three stories and their three images remain ordinary sequential content.

## Ownership and motion

- Blade owns three narrative states and current translation content.
- CSS owns static flow, split layout, sticky image field, responsive/RTL.
- JS owns only enhancement state and scroll-to-visual progress.
- Native document scroll remains authoritative.
- RAF visual progress chases scroll target; code never writes document scroll.
- Wide enhancement begins at 1024px; XS/SM/MD remain sequential.
- RTL changes text direction/alignment but does not reverse vertical time.

## Six-tier contract

- XS 360-639: single column, story then image, no pinned enhancement.
- SM 640-767: single column with wider media, no pinned enhancement.
- MD 768-1023: bounded single column, no pinned enhancement.
- LG 1024-1279: two-column pinned reveal with reduced image/gap sizing.
- XL 1280-1535: full two-column pinned reveal.
- 2XL >=1536: same interaction, bounded max-width composition.

`prefers-reduced-motion` receives static sequential flow.

## Performance and accessibility

No new package, font, remote asset, WebGL, or global smooth-scroll dependency. Reuse existing lazy images. Enhancement loads through the existing deferred Vision entry after Hero. Continuous RAF runs only while the surface is near and moving. Canvas is not introduced.

## Active execution

1. ACTIVE: replace the surface source and detach Program from the old shared pin.
2. PENDING: owner/local structural build and PHP proof.
3. PENDING: Chromium/WebKit visual, reverse-scroll, responsive, locale/RTL, reduced-motion and PageSpeed proof.

Completion remains `BLOCKED_BY_MISSING_EVIDENCE` until runtime gates run.
