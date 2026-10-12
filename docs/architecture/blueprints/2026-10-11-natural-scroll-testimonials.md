# Natural scroll and Gallery–Testimonials continuity

STATUS: OWNER_ACCEPTED / IMPLEMENTING
Owner: Asyraf; issue100; Terminal Codex; 2026-10-11, corrected2026-10-12.
Branch: feat/natural-scroll-testimonials; main cdfd30980516bbe2022fd0f8c12e64e4a613c8c3.
Both PR97 5518756f and PR99 7d020133 retained via combined parent e0b7931f.
The latest owner's white sentence/lower-corner storyboard supersedes black ink.

## FACT → GAP → GOAL → IMPACT → DECISION

FACT: initial tree clean; original branches/main/stash unchanged. PR97/99 OPEN.
PR99 used one1680px/s cap,112px debt and145ms input expiry. WebKit first RAF
149–180ms after accepted wheel discarded input before any scrollBy call.
Values uses native position and1:1 exit; Gallery GSAP scrub=true, ten media.
OLD wall uses three alternating tracks, photo2:1 quote/cite cards, tight gaps,
and2.333/3.333/5.333-card compositions. Existing localized source is disclosed
sample content; no new quotes, names, verification claims or photographs.
Historical typography paths absent; live V2 fonts/tokens/locale owners audited.
GAP: painted phase limits and final handoff need actual browser evidence;
native Safari, physical devices, assistive speech and production metrics absent.
GOAL: quick free movement, responsive reverse, perceptible short coast; all
critical poses remain visible, then dark Gallery→white question→white OLD wall.
IMPACT: pacing and Testimonials orchestration/Blade/CSS, localized question,
composition lifecycle, focused tests/workflow and current-state proof ledger.
DECISION: native scroll remains authoritative; no new dependency/animation engine.
Bound impulse debt, not universal velocity. Ordinary112px wheel gives224px input
with45ms response (~4159px/s sustained at60Hz, ~2.5× old1680). Stronger intent
scales debt by sqrt(delta/112), so free space has no fixed speed ceiling.
Synchronous first bounded delivery fixes WebKit delayed-RAF swallowing. Release
replaces unused input with delivered velocity,90ms damping, <=224px coast and
260ms expiry. A stalled paint>100ms discards old input rather than replaying it.
Reverse clears old direction; fractional native pixels accumulate correctly.
CSS/WAAPI retain real remaining clocks/visible travel. Completed animations do
not impose limits. Values budget reads original flip curves and last rendered
pose: <=45deg change per delivery, one shared budget until the next card paint,
plus stagger face-crossing checkpoint. Compact tiers use original responsive
flip curves and painted angle. Hold/exit remain freely native, including1:1.
Gallery: assembly/exit eight segments, rise six, word opacity five shared segments
(.10 opacity plus .016 stagger), opening six. Original GSAP timeline/geometry/
media unchanged. Existing trigger.update() follows each native paced write,
preventing the reproduced .04048-progress/255px delayed scene observation.
Composition also synchronizes the existing nearby Testimonials owner through
its scrollChanged port; its word state follows the same delivered native position.

## Bounded ownership

Editable: scroll-pacing*.js, testimonials*.js, Testimonials Blade/CSS,
locale/ar.css, lang/*/testimonials.php (question/instruction only), index lifecycle,
related tests/workflow, this blueprint and current-state ledger/proof.
Gallery CSS only migrates old Testimonials AR font ownership to locale adapter.
Read-only: About/Program/Values controllers/CSS; Gallery timeline/geometry/media/
entry; existing presenter/data/config; OLD source. Forbidden: Article, navigation/
Hero redesign, DB/schema/admin/media changes, dependencies, main writes or merge.

## Semantic and motion storyboard

One localized H2 with accessible full question and aria-hidden visual word spans.
No visible secondary description; original sample disclosure remains semantic.
All21 OLD-source cards retain semantic order, lazy reserved2:1 photographs,
quote/top and cite/end composition. Clean rows hide scrollbar/arrow chrome;
native keyboard/horizontal swipe retains every card. Manual interaction transfers
current track offset to native scrollLeft without a visible jump.
Question begins exactly after Gallery media/words vanish at progress.90, within
its existing empty black hold. Testimonials-only native overlap is measured as
viewport +10% of actual Gallery height. Before blackout the field is transparent
and question invisible, preserving all active Gallery exit media and words.
Observe existing Gallery enable/resize lifecycle; never mutate its timeline.
White sentence sits at lower logical-start gutter. Exactly Gallery foreground
clamp(2rem,4.5vw,5.5rem),600/1.12/-.035em; same .10 linear word opacity,
16px→0 blur, .016 opacity/.22 sharpness stagger and actual Gallery blur duration.
Sharp full white text reads on #08090b before any lightening. The owner's latest
correction requires equal physical lower/side gutters and a sharp reading hold
equal to the entire blur-to-sharp scroll distance. Departure begins at twice
(.22 stagger + actual blur duration); text and first media then rise together
by native1:1 travel, keeping their existing gap constant.
Text fades and blurs14px while field brightens toward white. OLD tracks alternate
horizontal displacement with vertical scroll and mirror meaningful direction in
RTL. White field and clean empty70svh ending; Article stays untouched.
Reduced/no-JS/capability failure: native white flow, black readable question,
no overlap/coast; reduced wrapping grid. Resize remeasures without scroll writes.
Keyboard/scrollbar/direct navigation bypass wheel; nested/dialog/edit/zoom and
Shift-wheel stay native. Touch stays native; no touch scroll trapping. Outgoing
locale/page/hidden lifecycle cancels frames/debt; resume remeasures actual bounds.
Dispose removes every enhancement variable/attribute and all observers/listeners.

## Six tiers and locale

XS360/390 and SM640:2.333 strip, min16rem media card. MD768/LG1024:3.333.
XL1280/1440 and2XL1536/1920:5.333. Reduced grids1/2/3 columns by available space.
Question2–3 natural lines, CSS-owned scale/measure; short height/200% text remains
semantic flow. Test affected639/640,767/768,1023/1024,1279/1280,1535/1536 bounds.
ID/EN Inter/LTR lower-left; AR Cairo/RTL lower-right, normal tracking/1.45 leading.
Same DOM and vertical meaning; joined words preserved, photographs unmirrored.
Six locale-switch directions reconstruct state from server locale/native bounds.
Navigation contract unchanged: hamburger through1180, desktop1181.

## Proof, budgets and next step

No idle animation loop, new critical media/context, or copied OLD/Lusion assets.
One event-driven RAF per existing controller; offscreen/hidden work sleeps except
necessary bounded geometry observation. Full white text on black has >7:1 contrast;
photo quotations retain contrast overlay. Focus/touch/manual/reduced/no-JS usable.
Browser proof: ordinary/coast/reverse/direct/nested/reduced/stalled input; actual
painted Values angles/stagger/compact flips, native1:1 exit, ten-media Gallery
assembly/rise/fade and same-turn native/GSAP agreement. New handoff: exact font
match, lower logical corner, no premature overlay, full white/black reading state,
constant title/media gap, upward blur/white arrival, OLD reverse, clean ending.
ID/EN/AR × nine widths plus five boundaries, short/orientation/200% text, all six
locale switches and synthetic lifecycle; Chromium/WebKit, no-JS/reduced fallback.
Mandatory diff/structure/build/php artisan test; scoped Pest/Pint and Node/browser.
Three comparable local frame samples; report median/worst/idle/long-task evidence,
not Lighthouse100 or field CWV. Existing full PHP71FAIL/75ERROR must remain FAIL.
ACTIVE STEP: finish corrected combined proof and publish continuation PR; no merge.
Update UI_UX_CURRENT_STATE before each next implementation capability.
STATUS: BLOCKED_BY_MISSING_EVIDENCE final corrected proof/CI/native/device/speech/
PSI/field certification pending. Blueprint not full-DOD PROVEN.
NEXT execution channel: Terminal Codex, proof then continuation PR for issue100.
