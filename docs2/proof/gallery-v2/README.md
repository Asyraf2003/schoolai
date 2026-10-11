# Gallery V2 proof — 2026-10-11

Scope: MAP-V2-18 / issue86 / feat/v2-scroll-gallery.

## Browser evidence

`gallery-scroll-{engine}.json` records ID/EN/AR ×360/390/640/768/1024/
1280/1440/1536/1920 at900px height, opening, formation, travel, exit and handoff.
PNG captures compare opening→entering→formation→exit→handoff at390 and1440.
`gallery-viewport-{engine}.json` adds twelve boundary/short-screen widths through
2560px,500→600px resize progress, keyboard pause and synthetic hidden-page policy.
`gallery-lifecycle-{engine}.json` records real video playback, max two hydrated/
playing videos, poster on failure, reduced motion, synthetic BFCache events and
real disposal. No-JS and blocked GSAP run in the same browser suite.

Chromium and WebKit local five tests each PASS, including viewport/resize.
CI will repeat all five tests per engine on the final committed source.
Real R2 media used; no mocked images/videos. `media-inventory.json` proves ten
unique URLs plus posters return200/206 and correct content types. Five videos,
five photographs. Facility poster photographs are catalog assets, not necessarily
frames extracted from the corresponding videos. Testimonial copy retains its
explicit temporary-sample disclosure.

## Source and PHP evidence

Geometry tests:56 viewport/direction combinations PASS. Structure309 files <=200
lines PASS. Production build, Pint and diff check PASS. Six focused Pest tests /
43 assertions PASS. `php-baseline-comparison.json`: full suite342,196PASS/71FAIL/
75ERROR; isolated main336,190PASS/71FAIL/75ERROR, identical failing/error test
identities. Historical failures reference missing legacy view/source paths and
admin contracts. Full-suite status remains FAIL; scoped regression comparison PASS.
Protected Hero/Header/About/Program/Values/legacy Gallery/model source has no diff.

## Reproduce

Build and serve the seeded application. Set PROGRAM_BROWSER_URL,
PROGRAM_PLAYWRIGHT_MODULE, PROGRAM_ENGINES=chromium,webkit and an existing
PROGRAM_PROOF_DIRECTORY. Run node --test --test-concurrency=1 on
GalleryScrollBrowser.test.mjs, GalleryFallbackBrowser.test.mjs and
GalleryViewportBrowser.test.mjs in tests/Unit. CI workflow v2-gallery.yml installs
browser dependencies. Local Arch WebKit needed isolated ICU74/XML2/Flite and
GStreamer good/libav plugins; no project dependency changes were made.

## Certification limits

No native Safari, physical device, native BFCache restoration, screen-reader
speech, browser200% zoom, Lighthouse100, INP/LCP/CLS field or performance trace
certification. Synthetic visibility/page events certify application handlers only.
Visual inspection covered opening, entries, staggered columns, progressive exit
and real populated testimonial handoff. Expected stage clipping while media enter
or leave is intentional; final column geometry has no accidental overlaps.
