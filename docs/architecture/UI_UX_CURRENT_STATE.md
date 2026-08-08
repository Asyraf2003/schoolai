# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-08
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-AVM-001-PINNED-MASK-REVEAL`
Source baseline: `66eb28b1467b3f4e3ba77f9ef9af4b8379f1a78d`
Implementation commit: `91e8d3af5e9d4616eb3b6052a4f64fe45460c1f0`
Active blueprint: `blueprints/2026-08-08-home-about-vision-mission-pinned-reveal.md`

## Latest owner decision

- Replace the former homepage Visi/Misi choreography completely with the
  interaction concept from `https://codepen.io/gridmorphic/pen/WbQPRwv`.
- The SchoolAI surface has exactly three narrative states: About, Visi, Misi.
- Reuse the three existing `media/home/vision-paper-0*.webp` assets.
- Program remains the next independent semantic section below this surface.
- The reference interaction is translated, not copied: do not import its
  global Lenis ownership, Outfit typography, external assets, or exact source.

## Implemented source contract

- `vision-mission.blade.php` now renders one semantic About -> Visi -> Misi
  sequence with three existing SchoolAI images and localized ID/EN/AR copy.
- XS/SM/MD keep ordinary sequential story/media flow.
- LG/XL/2XL use a two-column enhanced layout: copy progresses vertically while
  the image field is sticky and stacked images reveal through a vertical
  `clip-path` mask with mild image parallax.
- Native document scroll remains authoritative. Visual progress chases the
  target through one bounded RAF smoothing loop and never writes scroll.
- Reduced motion and failed/no-JS enhancement preserve readable sequential
  content without the pinned reveal.
- The old Visi/Misi horizontal track, image-stack choreography, and Program
  shared-pin ownership were removed from the active surface.
- Program's integration adapter is now defensive cleanup only; Program geometry
  measures from its own document position and no longer consumes Vision travel.
- Program's own local seven-frame journey, content, rail, HUD, and exit remain
  otherwise locally owned and unchanged.
- Focused source tests were rewritten for the new three-state contract and for
  the independent Program boundary.

## Reference translation

The CodePen reference uses GSAP ScrollTrigger plus Lenis for a pinned image
column, scroll-scrubbed mask reveal, and image-position parallax. SchoolAI keeps
that interaction meaning while using project-owned native scroll + RAF motion,
so no new runtime dependency or global smooth-scroll owner was added.

## Source publication proof

- Blueprint publication commit: `119f0cc53445f0e54cdf4c09a6793821ff83346a`.
- Implementation publication commit: `91e8d3af5e9d4616eb3b6052a4f64fe45460c1f0`.
- GitHub compare from the implementation parent reports one fast-forward source
  commit affecting only the About/Visi/Misi surface, the obsolete Program
  integration boundary, and the two focused feature tests.
- No Hero, Values, Gallery, Articles, navbar, footer, public language source, or
  database content was changed in the implementation commit.

## Blocked proof

This GitHub channel cannot run the local application or browser matrix. These
remain `BLOCKED_BY_MISSING_EVIDENCE` until actually run:

```bash
git diff --check
git status --short
npm run check:structure
npm run build
php artisan test
```

Runtime proof is also still required for:

- Chromium and WebKit forward/reverse/fast/interrupted scroll;
- 360, 390, 640, 768, 1024, 1180/1181, 1280, 1440, 1536, 1920 widths;
- ID, EN, AR and RTL composition;
- reduced motion, resize/orientation, BFCache, keyboard/touch and 200% zoom;
- About -> Visi -> Misi visual fidelity, clip reveal, image crop and the clean
  handoff into Program;
- PageSpeed/CWV delta against the declared baseline.

## Progress / status

Source implementation and publication are complete for this batch. Runtime,
build, browser, responsive, accessibility, and performance completion are not
proven by the GitHub commit.

STATUS: `BLOCKED_BY_MISSING_EVIDENCE`

NEXT EXECUTION CHANNEL: `owner/local terminal`

NEXT VALID STEP: run the five required local proof commands above against the
current `main`, report their exact output, then proceed to browser visual proof.
