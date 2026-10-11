# MAP-V2-18 — Scroll Gallery and Testimonials handoff

STATUS: OWNER_ACCEPTED / IMPLEMENTING
Source main445fbf92; channel Terminal Codex.

## OWNER_RAW

Implement Gallery after Values and before Testimonials: ten real media, 3 left /
4 center / 3 right. Opening video01 at80vw shrinks/rises into center top; nine
media enter from sides/below with scale changes. Coordinated scrubbed movement,
continuous upward travel and progressive fade exit. White, slight radius/inset
edge shading. Reverse/fast scroll, resize, reduced motion and media lifecycle.
Issue → branch → PR → merge only after relevant visual/browser checks PASS.

## FACT / LEGACY_REFERENCE

V2 ends after Values; no Gallery or Testimonials. OLD gallery Blade/depth and
video-preview/preparation modules inspected; no runtime imports permitted.
GalleryItem MAX_HOMEPAGE_ITEMS9 and legacy query limit9 are protected. Seed has
five direct R2 videos and12photos. Local DB published Gallery inventory is empty.
Testimonial OLD is a localized photo-backed quote wall, explicitly sample copy;
retain that disclosure and quotes. Existing R2 testimonial photographs reused.
Fantasy homepage inspected in Chromium1440×900: large centered opening media,
side columns initially outside viewport, differing item heights and staged rise.
Reference observed directly; temporary reference captures were lost on runtime reset.
No reference assets/code copied. Durable implementation captures: ../proof/gallery-v2/.

## AI_ASSUMPTIONS / DECISION

New V2 presenter takes up to10 eligible published unique DB media, preferring
homepage placement, then fills from known seed media only when no DB record for
that URL exists. Never resurrect unpublished/deleted records. Known video mapping
supplies a real corresponding facility photograph as poster; unknown videos
without verified poster are excluded. No DB/admin/schema mutation.
Testimonial presentation reuses OLD quote/photo rows in a responsive wall with
native horizontal row access; no new testimonial claims or blank placeholder.

## Media inventory / empty-DB fallback

R2 base: https://media.almustaqbal.sch.id
01 IT lab: gallery/media/it-v1.mp4; poster site/school-life/labit-v1.webp
02 Mushallah: gallery/media/musola-v1.mp4; poster mushalla-v1.webp
03 Hall: gallery/media/aula-v1.mp4; poster aula-v1.webp
04 Pool: gallery/media/renang-v1.mp4; poster renang-v1.webp
05 Taekwondo: gallery/media/tekwondo-v1.mp4; poster taekwondo-v1.webp
06 Library: site/school-life/perpustakaan-v1.webp (photo)
07 Counseling: site/school-life/psikolog-v1.webp (photo)
08 Parents class: site/school-life/parenting-v1.webp (photo)
09 Dental care: site/school-life/gigianak-v1.webp (photo)
10 Full day: site/school-life/fullday-v1.webp (photo)
Poster paths02–05 use site/school-life/. All URL availability is a proof gate.

## Storyboard and six tiers

Desktop/tablet768+: one sticky stage; progress0–.12 opening80vw centered;
.12–.46 shrink/rise and staggered entries; .46–.70 balanced3–4–3 composition
continues rising; .70–1 upward travel/progressive opacity exit into Testimonials.
XS360/390 and SM640: opening80vw, then two spacious columns within the same DOM;
continuous traveling composition, increased vertical runway. MD768/LG1024/
XL1280/2XL1536: full three columns, bounded width/gaps, balanced total heights.
ID/EN use Inter, AR Cairo/RTL; center remains center, side entrances mirror RTL.
Short heights cap opening height at72vh (80vw otherwise); resize recomputes
geometry and preserves the last progress before other section refresh handlers.
No-JS/reduced/missing GSAP: all10 media in semantic static responsive layout.
Media: poster always present, video src only when actually visible; max2 playing,
pause offscreen/hidden/reduced/suspended; rejected play/error retains poster.

## Ownership / boundaries

Editable: new LandingGallery/Testimonials presenters; HomeController data wiring;
landing index/new partials; CSS/JS new section modules; entry imports/lifecycle;
new localized labels, focused tests/workflow, docs2 and canonical progress ledger.
Forbidden: Hero/Header/About/Program/Values owners; legacy Gallery/model/admin;
/galeri, articles, schemas and dependencies. One timeline, no per-item listeners.

## Acceptance / PROOF

Four phase screenshots in Chromium/WebKit, nine widths plus boundaries, locales,
no overflow/overlap; first80vw→center top; all10; balanced heights; continuous rise;
reverse/fast scroll; exit; real media/video failures; at most2 videos; reduced,
noJS/missingGSAP; resize, hidden tab, BFCache/disposal. Keyboard/zoom/RTL checks.
Run geometry tests, focused PHP, full PHP with baseline comparison, Pint,
diff/structure/build. No native Safari/field CWV/100-point claim without evidence.
Initial budget: no video URLs hydrated before proximity/visibility, max2decoders;
no added continuous RAF, no reserved-layout shift on activation. Compare traces.

## NEXT VALID STEP

Terminal Codex: complete browser gates, publish PR and merge only scoped PASS.

## AI_TRANSLATION / GAP / IMPACT

A single GSAP scrub timeline controls the ten existing media in one stable sticky
stage. Gap addressed: missing V2 Gallery/Testimonial surfaces and legacy nine-item
cap. V2 presenters read existing data without changing old model/CRUD rules.
Header overlays the stage at one explicit layer so the pause control remains
visible and operable; this resolves measured media interception, not a global
z-index override. No navigation source changed.

## PROGRESS

Issue86; branch feat/v2-scroll-gallery. Six focused PHP tests PASS. Full suite
342 tests:196PASS/71FAIL/75ERROR; isolated main336:190PASS/71FAIL/75ERROR.
Failing/error test identities match exactly; historical legacy failures remain.
Chromium/WebKit opening→formation→travel→exit and media lifecycle passed initial
matrix; final resize/short-height runs and publication gates are in progress.
Native Safari, physical devices, real BFCache restoration, screen-reader speech,
field CWV and Lighthouse100 certification are not claimed. Synthetic page and
visibility events prove handlers, not native browser navigation restoration.
