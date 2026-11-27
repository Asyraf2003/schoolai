# Gallery and Testimonials continuous white choreography

Status: SUPERSEDED by the latest owner correction in the current-state ledger
Baseline: `dafca25dd91fbc915c655c99467a7a5af289f37f`
Surface: homepage Values → Gallery → Testimonials → Stories
Execution channel: local Codex

## Goal and scope

Gallery leads visitors from the school facilities into the existing testimonial
wall on one white background. The owner approved 30 tiles from the ten existing
assets, with two new fixed permutations and the original lead. The final three
lanes contain 9, 12 and 9 tiles at 30%, 40% and 30% of the available track
width after gutters. The prior 700/1000svh, equal-width lanes and black hold are
superseded. Asset sources, Values poses, the cursor, the scroll engine,
Testimonials cards and Stories internals remain on their existing owners.

Gallery Blade owns the display sequence. The current presenter still owns the
ten unique localized assets. Gallery CSS owns the static grid, stage and
white surface. The existing Gallery JS port owns measured geometry, trigger and
media lifecycle. Pacing consumes the same phase boundaries through its current
geometry cache. Testimonials stays in document flow with its existing wall
controller. Vite keeps the existing entry and GSAP loading path.

## Storyboard

| Phase | Trigger and visible behavior |
|---|---|
| Entry | Values blue blends to white before the Gallery heading arrives. Heading, localized statement and wide lead travel in normal document coordinates. |
| Center and shrink | Once the lead midpoint reaches the viewport midpoint, the lead scales around that point. The header keeps moving upward. |
| Formation | The remaining tiles arrive from below at their final widths. Gaps stay fixed and the lead holds center until the tracks meet. |
| Rise | All tiles rise one pixel per native scroll pixel. The measured tail determines the sticky stage length. |
| Handoff | The stage releases into native flow with its last tile at the seam. The existing Testimonials heading enters while the media tail is still moving through the viewport. Both surfaces are white. |
| Reverse and resize | The same poses reverse with scroll. Resize remeasures the stage and restores the current phase and fraction through that phase. |
| Static and lifecycle | Reduced motion, unavailable motion, or no JS show a normal-flow grid and Testimonials. Page hide releases video resources. BFCache resumes only visible playback. |

## Responsive, locale and accessibility

XS 360+, SM 640+, MD 768+, LG 1024+, XL 1280+ and 2XL 1536+ use the same
timing, geometry and semantic order. Gutters are fluid. Short landscape bounds
the composition to the space in which the lead can still shrink. The first
occurrence of each image has its existing localized alternative. Repeated
visual tiles are hidden from assistive technology. The localized Gallery
statement flows with the header on white. Native scrolling works with keyboard,
fine pointer and touch. Testimonials retains its two-column compact fallback
and capability-based desktop rails, focus and card interactions.

ID and EN use the existing Inter typography and LTR. AR keeps Cairo, its
heading line-height, RTL alignment and mirrored Gallery lanes. Vertical motion
is shared. Locale submissions rebuild server-rendered content and release the
outgoing media using the existing page lifecycle.

## Media and proof

The ten existing posters are lazy. Intersection changes control video
hydration. At most two visible video elements can hold a source; release
outgoing sources before hydrating new ones. Existing poster images stay visible
on media error or reduced motion. Gallery adds no dependency, renderer or
persistent frame loop.

Active step is to implement and verify the continuous handoff. Prove live center
and native movement, white seam, 30 media and decoder limits, no overlap,
reverse/resize, keyboard/touch/reduced motion, ID/EN/AR and six tiers in
Chromium and WebKit. Run structure, build, Node and PHP, plus cursor, pacing,
Values and footer regression checks. Track runtime evidence and remaining gaps
in the current-state ledger.
