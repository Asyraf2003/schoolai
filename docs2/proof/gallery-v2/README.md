# Gallery V2 final owner-correction proof — 2026-10-11

MAP-V2-18 / issue86 / feat/v2-scroll-gallery / PR87 / product49742cdc.
Latest black entry/centered shrink/distant approach/100vh→50vh exit supersedes
all initial white-background proof. Initial PNG captures have been replaced.

## Browser proof

Latest integrated rerun after owner main merge ac22aeef/d3108694 and Gallery
resize fix: Chromium6/6 PASS (104.11s), WebKit6/6 PASS (213.14s), raw
gallery-integrated-{engine}.log, current scroll/lifecycle/viewport/heading JSON
and verified-source.json. Main baseline now dae40c14; PR93 is separate.
Heading waits until actual exiting Values cards have at most20% body remaining
(18% gate), including spring settling; reverse closes the gate.
Preceding-world resize3000→2250 after viewport1535x500→600 previously shifted
progress.52→.7065. Gallery reflow reconciliation now preserves progress and
centered geometry across all12 boundary/short profiles in both engines. Prior geometry-identical visual proof on49742cdc:
Chromium5/5 (54.14s), WebKit5/5 (141.38s), gallery-owner-{engine}.log.
ID/EN/AR ×360/390/640/768/1024/1280/1440/1536/1920 at900px height.
Twelve boundary/short-height profiles through2560,500→600px resize and native
wheel; hidden/reduced/static/error/missingGSAP/dispose and synthetic BFCache.
Real visible video currentTime advances, max2 hydrated/playing decoders, loop
property enabled, no Gallery button. Poster always survives rejected/error video.
Heading uses identical computed size to Values; title/main retain a fixed close
gap during native rise. Shrink starts at stage top and finishes main center at
viewport center. Nine secondary media begin far below and resolve their gaps
at assembly. First word clear at assembly, later words blurred then resolve.
Last bottom edge900px→450px coincides with all media/text blur/fade to zero.
Black hold retains no media/text/pause UI; real populated Testimonials follows.
Reverse returns geometry and restores blue/type plane before Gallery entry.

96 JPEGs compare title→rise→opening→approach→assembly→clear text→blur→blackout
at390/1440 in both engines and three locales. Early-title capture intentionally
shows the title held while Values remain; six additional delayed-title captures
show the resolved heading after actual card exit. Superseded settled-entry
captures were removed; native heading/main geometry retains the close gap.
Three Values line/render integration tests PASS in WebKit (66.63s), raw log.
Testimonial-handoff.json separately proves12 native handoffs (both engines,
three locales,390/1440): populated quotes enter after the black hold, Gallery
media remain fully hidden, and no horizontal overflow. Matching JPEGs reviewed.
CI38117850566: Chromium PASS; WebKit4PASS/1cancelled by420s file timeout,
without an assertion failure. Phase artifacts reached EN/ID and AR390.
Heading38117850572 and Values38117850614 PASS both engines (Values14/14
per engine). Gallery partition run38118576424 on8b97c7e6 pending WebKit;
Chromium PASS. Final semantic-correction publication CI pending.
Gallery CI now partitions the unchanged27-profile matrix per locale into fresh
bounded browser processes and records per-viewport duration/per-locale JSON.
Integrated CI38129531132 onac22aeef: Chromium27-profile composition and
heading PASS, viewport regression FAIL before the Gallery resize fix.
Final scoped CI on the corrected source must PASS before merge.
video-loop-boundary.json proves native real-video loop restart in both engines
with playback advancing past100ms after restart.

## Assets and profile

Real R2 video URLs, five videos/five catalog photographs, no invented URLs.
media-inventory.json separately proves10 unique URLs/posters200/206/types.
Local and CI normal-motion proof serve cached copies of those actual R2 poster
files and exact existing GSAP3.7.1 CDN modules; video requests remain live R2.
This isolates geometry/timing from repeated external poster-network delays.
Posters are facility photographs, not necessarily extracted video frames.
Testimonials preserve the existing explicit temporary-sample disclosure.
Chromium version and WebKit26.6/Playwright1.63.0 in JSON; local Linux/Arch,
127.0.0.1:8020 isolated seeded SQLite, no production/working DB writes.
Local WebKit uses isolated ICU74/XML2/Flite and GStreamer good/libav plugins.
No project dependencies or system package installation changed.

## Source, PHP and scheduling

Structure312 source files<=200 lines, build, Pint, geometry56 profiles and diff
PASS. Six focused Pest tests/46assertions PASS. Full342:196PASS/71FAIL/75ERROR;
isolated latest main dae40c14 336:190PASS/71FAIL/75ERROR with identical failing/error test names.
Full suite remains FAIL; scoped regression comparison PASS. Dependency audit
FAIL on the unchanged npm lock baseline. No lock/schema/legacy CRUD changes.
Hero/Header/About/Program/Values product-source owners have no diff; Gallery's
explicitly authorized seam CSS/data state alone paints the preceding world black
and hides its type plane. Their motion/geometry is unchanged.

gallery-scroll-scheduling.json:3 comparable warm samples each at390/1440 in
headless Chromium, Gallery travel/blur/exit only. RAF intervals, long tasks and
animation layout shifts are recorded as measurements. RAF scheduling is not
GPU render-frame timing, Lighthouse or initial loading/CWV certification.

## Reproduce / limits

Build/serve seeded Laravel. Set PROGRAM_BROWSER_URL, PROGRAM_PLAYWRIGHT_MODULE,
PROGRAM_ENGINES and existing PROGRAM_PROOF_DIRECTORY. Run the three Gallery
browser test files in tests/Unit with node --test --test-concurrency=1.
v2-gallery.yml repeats the same scope; v2-values-cards.yml proves integration.
Accessibility-tree inspection found the paragraph aria-label was ignored while
all animated words were hidden from assistive technology. One semantic sr-only
sentence now exposes the complete localized copy, with visual words unchanged.
accessibility-geometry.json proves18 cases in Chromium/WebKit ×ID/EN/AR ×
390/720/960 CSS pixels at DPR2: equivalent200% physical780/1440/1920 layout,
complete named media/copy, native keyboard panning/visible focus and touch tap.
This is geometry/semantic-tree evidence, not native browser-menu zoom or reader
speech. The latest full browser rerun separately asserts the sentence in all
three locales. Native Safari/physical devices/native BFCache/reader speech/browser-menu zoom/
Lighthouse100/field LCP-INP-CLS certification. Synthetic lifecycle proves handlers.
Stage clipping during intentional entrance/exit remains part of choreography;
final media geometry has no accidental overlap or horizontal overflow.
