# Vision/Mission WAAPI Scroll Story

BLUEPRINT ID: `HOME-VISION-004-WAAPI`
STATUS: `OWNER_ACCEPTED / IMPLEMENTING`
DATE: 2026-08-04
TARGET: `Asyraf2003/schoolai` branch `main`
SURFACE: homepage `#visi-misi`

## Goal

Rebuild Vision/Mission with the original full-screen Vision atmosphere while
using a bounded Web Animations API timeline instead of per-character frame
painting. The result must preserve native scrolling, feel inertial, reverse
cleanly, and avoid starting expensive work during the Hero critical path.

## Choreography

1. Vision opens full viewport with artwork `9.png` through `12.png` moving
   inward from the edges.
2. The full Vision composition shrinks into the logical end-side panel.
3. The editorial title appears on the logical start side and a vertical divider
   grows between title and panel.
4. Vision leaves horizontally while Misi 1 rises from the lower end corner,
   follows a curved staging path, and docks in the end-side panel.
5. Misi 2 through 4 repeat with bounded overlap.
6. Sticky release returns to normal document flow after Misi 4.
7. RTL mirrors the spatial choreography while preserving content direction.

## Runtime contract

- server-render one semantic Vision and four semantic Misi articles;
- keep a styled static fallback before enhancement and for reduced motion;
- load only a small entry during initial page load;
- dynamically import the controller after Hero presentation during idle time;
- create paused WAAPI animations only when the section approaches the viewport;
- drive all animations from one native-scroll progress value and one RAF;
- write only animation `currentTime` during normal frames;
- animate wrapper `transform` and `opacity`, not layout properties or moving blur;
- no character splitting, virtual scrolling, scroll prevention, or parallel old
  controller.

## Proof boundary

Source publication proves implementation only. Required owner-side gates:

```bash
git diff --check
npm run check:structure
npm run build
php artisan test --filter=HomeVisionMissionHeadingTest
```

Visual review is required at six responsive tiers in ID/EN/AR, LTR/RTL,
Chromium/WebKit, normal and reverse scroll, plus reduced motion.
