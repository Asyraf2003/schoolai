# MAP-V2-18 — Gallery V2 and Testimonials handoff

STATUS: OWNER_ACCEPTED / IMPLEMENTING
Repository Asyraf2003/schoolai; main445fbf92; Terminal Codex.
Issue86 / branch feat/v2-scroll-gallery / PR87.

## OWNER_RAW / OWNER_CONFIRMED

Exactly10 real media after Values before populated Testimonials:3 left,4 center,
3 right. Media01 is the opening video and becomes the center column top.
Latest corrections supersede the initial white-background/top-aligned storyboard:
“judul galerinya ...line terakhir sudah80% ...kartu nilai siswa naik”; background
blue and lettering become black, large white Gallery title. Main media comes
from below under the heading with the same close media gap. Heading and media
rise together until media reaches viewport top; then main shrinks toward center
while heading continues upward. “hapus ...pause video ...videonnya selalu loop”.
Secondary media begin far below and approach only during shrink, reaching final
close gaps together when main finishes shrinking. This supersedes the earlier
request for secondary media attached from the beginning.
At60% of shrink, foreground words begin blurred; first word is clear at assembly,
later words progressively resolve while media keep rising. Owner delegates the
sentence about excellent facilities for the next generation and confirms text
in front of moving media. Final fade: “100vh sampe ke50vh ...semua sudah hilang
...gelap”; all media/text start blur/fade when the last bottom edge is at viewport
bottom and disappear fully when that edge reaches viewport middle.
Issue → branch → PR → merge main only after relevant checks PASS.

## FACT / LEGACY_REFERENCE

Original active V2 ends after Values. OLD gallery/depth templates, video-preview
and media preparation modules audited read-only. No legacy CSS/JS imports.
GalleryItem MAX_HOMEPAGE_ITEMS9 and legacy query limit9 remain protected.
Seed defines5 direct R2 videos and12photos; local working DB has no published
Gallery rows. Testimonial OLD has localized quotes/photo rows with explicit
sample-copy disclosure, preserved here without inventing new testimonial claims.
Fantasy homepage inspected directly in Chromium1440×900: centered large media,
side entrances, differing media heights, coordinated rise. No assets/code copied.
Temporary reference captures lost on runtime reset; implementation proof is durable.
Current source/runtime typography is Inter/Cairo; historical text-system paths
are absent from active resources after the source switch.

## AI_TRANSLATION / DECISION

One GSAP ScrollTrigger scrub timeline drives all10 media in one stable sticky
stage. Normal-flow heading and main rise natively with one media gap until stage
hits top; measured boundary starts centered shrink. Desktop fills viewport with
three columns and gap clamp6px→16px (half previous); fixed secondary widths.
Four center media are shorter than side media, equalizing visual column heights.
Mobile retains readable2-column adaptation from the accepted original brief.
Gallery owns the handoff paint state on the preceding world: data-gallery-dark
changes only its background to black and hides the decorative type plane.
The overlap is transparent so incoming Gallery never masks Values cards.
Existing Values animation/card/line/marquee controllers and geometry are unchanged.

Copy: ID “Fasilitas terbaik. Untuk generasi hebat.”;
EN “Exceptional spaces. For an extraordinary generation.”;
AR “مرافق متميزة. لجيل واعد.” Shared semantic DOM, two localized lines.
Automatic muted looping video, no visible pause UI; at most2 visible decoders.
Offscreen/hidden/reduced/suspended/failed media release video and retain poster.
No-JS/reduced/missing GSAP gives complete static responsive media and copy.

## AI_ASSUMPTIONS

26/30 opening example means86.7vw; short/wide viewports cap opening height84vh.
“Near80% of last strand” maps to Gallery beginning with its root at70% viewport
and90svh overlap against the existing500svh Values field. Compact flow content
may extend that field; complete cards take precedence over masking their end.
Poster photographs are existing facility catalog assets, not extracted frames.
Native Safari/physical-device/field performance certification is not inferred.

## Media inventory

R2 base https://media.almustaqbal.sch.id, unique and verified inventory URLs.
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
Posters02–05 are under site/school-life/. Actual DB items take precedence.
Presenter selects up to10 safe unique published R2 items and fills known seeds
only if their URL has no DB record; never resurrect withheld/deleted media.
Unknown videos without a verified poster are excluded. No DB/schema/CRUD change.

## Storyboard / responsive / locale

Progress0: dark entry/large shared section heading; native rise preserves the
heading/main gap. Before measured stage-top boundary, main remains large and
secondary media remain at least one viewport below it. Boundary→.52: centered
shrink plus secondary approach. Words start at60% of that measured interval.
At.52 main center is viewport center,10 media form3–4–3 and first word is clear.
.52–.76: whole composition rises, later words clear by.74. Last bottom edge
reaches100vh at.76. .76–.90: rise another50vh, all blur/fade together to zero.
.90–1: black hold; populated Testimonials enters on continued native scroll.
XS360/390 and SM640:2 columns, longer1000svh runway; MD768/LG1024/XL1280/
2XL1536:3 columns,700svh. Same DOM/controller; CSS owns typography and layout.
ID/EN Inter/LTR; AR Cairo/RTL, mirror physical side entrances without mirroring
photos or vertical time. Server locale reload rebuilds geometry/lifecycle.
Resize preserves last measured progress before other section refresh handlers.
Reduced/static paths show all10 items without overlap or inaccessible controls.

## SCOPE / file ownership / OUT_OF_SCOPE

Editable: LandingGallery/Testimonials presenters, HomeController wiring, landing
index/new partials, new Gallery/Testimonial CSS/JS, entry imports/lifecycle,
localized Gallery copy, related PHP/geometry/browser tests/workflows and proof.
Latest explicit background request permits only Gallery-owned seam paint of the
preceding world; its existing motion ownership remains protected.
Forbidden: Hero/video system, Header/navigation, About, Program/card/detail,
Values/card/headings/SVG paths/marquee motion, articles, /galeri, legacy model/
admin/CRUD, schema and dependencies. No added renderer/RAF/per-item scroll loop.
ValuesLineBrowser/Render tests scope trigger counts to Values id and reconcile
native scroll after refresh. No Values product source changes.

## Acceptance / PROOF

Chromium/WebKit: ID/EN/AR ×9 representative widths and12 short-height/boundaries;
large title/early seam, heading/main gap, native rise→centered shrink, distant
secondary approach→3–4–3, balanced heights, ordered blur reveal, continuous rise,
last edge100vh→50vh blur/fade/black hold, populated Testimonials and reverse.
Real visible video playback, max2 decoders, poster/error fallback, hidden-page,
reduced/static/missing GSAP, resize/locale lifecycle and real disposal.
Record phase captures and browser proof separately from geometry/PHP/source checks.
Budgets: no video source before visible relevance, max2 video decoders, no added
continuous RAF, lazy poster proximity, stable reserved media geometry.
Run diff, structure<=200 lines, production build, Pint, focused PHP/full baseline.
Focused PHP6tests/46assertions PASS. Full342:196PASS/71FAIL/75ERROR; isolated
main336:190PASS/71FAIL/75ERROR, identical failing/error names. Full status FAIL;
scoped regression comparison PASS. Dependency audit known baseline FAIL; no lock
changes. Current-storyboard local Chromium5/5 and WebKit5/5 PASS; twelve
real Testimonial handoffs and Values line integration3/3 PASS. CI38117850566
Chromium PASS; WebKit4PASS/1cancelled at420s aggregate file limit without an
assertion failure. Same27 profiles now partitioned per locale into fresh bounded
processes with per-viewport duration proof; final CI pending in proof/gallery-v2.
Accessibility tree and18 DPR2 expanded-geometry cases PASS: full localized
sentence,10 named media, keyboard/focus and touch tap. An sr-only full sentence
fixes the inaccessible animated paragraph without changing visible copy.
No native Safari, native BFCache restoration, screen-reader speech, Lighthouse100
or field CWV certification claim. Synthetic lifecycle checks prove handlers only.

## GIT / NEXT VALID STEP

PR87 remains OPEN until latest visual/browser and scoped CI PASS.
Terminal Codex: finish latest owner-correction proof, update PR and gated merge.
