# Vision/Mission Static Copy with Restored Image Journey

BLUEPRINT ID: `HOME-VISION-010-STATIC-COPY-IMAGE-ROLLBACK`
STATUS: `OWNER_ACCEPTED`
OWNER: Asyraf Mubarak
DATE: 2026-08-05
SOURCE MAIN SHA: `278b8a9a298d00a30a6fcc36fed13c7c0e346eaf`
TARGET BRANCH: `main`

## Goal

Keep Vision and Mission permanently visible in three responsive layouts while
restoring the pre-static image geometry, image 2-3 vertical swap, pinned journey,
and Program transition.

## Owner decisions

- Static applies only to Vision and Mission copy.
- Desktop keeps Vision left, Mission right, and Mission slightly lower.
- Large tablet `1024-1180px` uses two centered columns.
- Narrow tablet and phone `<=1023px` use a close centered stack.
- ID, EN, and AR copy never receives opacity, transform, splitting, or a scroll
  trigger.
- Image and Program return to the scroll-owned track.
- Images two and three use the original vertical stack and `-50%` swap.

## Architecture

The semantic copy stage is outside the animated track. A dedicated motion wrapper
owns the sticky pin, track, image scene, and Program. The controller queries only
the motion wrapper, track, frame, and image stack. Typography modules are not
imported or called.

## Proof plan

```bash
git diff --check
npm run check:structure
npm run build
php artisan test --filter=HomeVisionMissionHeadingTest
php artisan test
```

Runtime proof: ID/EN/AR at 390, 768, 1023, 1024, 1165, 1180, 1181, and 1440
in Chromium and WebKit, including reload, upward scroll, and reduced motion.
