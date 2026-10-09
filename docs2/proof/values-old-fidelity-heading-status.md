# Values OLD fidelity / heading sequence — issue #78

Status: PASS scoped local implementation/runtime; repository baseline remains FAIL.
CI PASS: [run37993186545](https://github.com/Asyraf2003/schoolai/actions/runs/37993186545), source/test commit c95f6542.
Chromium18/18 and WebKit18/18. Final documentation-only head retains identical code.
[PR #79](https://github.com/Asyraf2003/schoolai/pull/79) records CI and the merged SHA.
Baseline main:2b879e944c93b19244829287ab42d51936f4a04d.
Branch:fix/values-old-fidelity-heading. Blueprint:MAP-V2-15.

## Audit and implementation

Read OLD Blade, complete Values CSS/JS owners, blueprint/handoffs, archived
semantic/Latin/Arabic typography and V2 Blade/CSS/JS/Vite/locale owners first.
Current OLD source overrides historical storyboard numbers. Ported its pure
spring/keyframe functions; no OLD controller or runtime import reaches V2.

Cards restore84vw/91.6667vw/90vw rows, .717 ratio, colors/type/ornament,
slot→pose→float→flip→faces, shared desktop perspective, split/stack/fan,
staggered180° flip with−18° overshoot, upright hold and upward exit. Native
compact rows use OLD visibility-based flip and gap-limited skew. Desktop timing
maps to the accepted V2 field; no670svh runway copied. Text can grow the card;
insufficient height switches to readable natural flow. Card-only Cairo family
uses OLD500/600/700 assets without changing other V2 typography.

Heading states idle→revealing→shifting→complete. Both900ms WAAPI promises must
finish before lower-line880ms animation begins. Scroll only starts idle state;
leaving/reversing cannot reset, skip or retime it. Generation guards invalidate
stale completions. Reduced/missing capability produces readable static content;
synthetic BFCache pauses/resumes the active sequence.

SVG geometry/timeline/runway, Program, Header/Hero/About,4.5s background rows,
color morph and localized content sources are unchanged. WebKit's stale3D
scrollable overflow after desktop→compact resize is bounded by the card track's
horizontal clip, following OLD's clipping boundary while allowing vertical float.
WPE also reproduced mirrored backface painting in OLD and V2 despite correct
CSS backface visibility. V2 explicitly paints the face selected by the same flip
angle, retaining semantic front content and all rotations. This is a documented
engine correction, not a simplified animation.

## Proof method and gates

Reference pages serve archived CSS/controller and original typography via
Playwright interception, with the same translated content and preloaded OLD fonts (its optional Inter otherwise
selects a cold-load fallback). Each reference uses its own browser context. No application route,
production import or build input serves the harness. Screenshots compare equal
native-scroll phases; reference omits other OLD sections. V2 retains its accepted
SVG and background, so whole screenshots intentionally differ outside cards.

- Chromium153.0.8010.52 / Linux and WebKit26.6 / WPE Linux, Playwright1.58.2.
-14widths360–1920 including both sides of affected breakpoints; ID/EN/AR.
- Desktop10phases×3locales; compact390/768/1024×3visibility phases×3locales.
- Heading stopped/fast/reverse/slow scroll; actual completion ordering, fixed
  timing, one-shot state, locale reload, reduced-motion and synthetic BFCache.
- Short landscape heights,200% CSS text expansion, no-JS/IO/CSS3D/GSAP/media,
  offscreen float pause and resume without duplicate animations.
- Local Values runtime24/24 PASS; OLD desktop/compact comparisons4/4 PASS.
- Final targeted intro/reveal/fallback rerun6/6 PASS across both engines.
- Build and structure298sources≤200lines PASS; diff check PASS; pure JS11PASS.
- Scoped PHP Values/Program13tests264assertions PASS.
- Full PHP336tests:190pass,71fail,75error,1746assertions. Exact failure/error
  names equal fresh baseline; zero added or removed. Full suite remains FAIL.
- Program Chromium Arabic scroll restore2950vs2925 is reproduced unchanged
  on detached main. Existing60ms SVG test sleeps also fail on baseline; changed
  the test to await native scroll progress and paint, not production SVG code.
- CI exposed native scroll/resize races in the test harness: wait through the
  actual geometry/refresh settling, trigger reverse only after reveal starts,
  and pause the next WAAPI in its state-change microtask before sampling. No
  assertion was removed or duration/geometry tolerance widened for these races.
- Repository npm audit remains FAIL: unchanged lockfile resolves shell-quote /
  concurrently (critical) and source-map-js (high),3 findings. No dependency edit.

## Measured frame work

Three isolated samples per revision/profile, Chromium153/Linux, warm local
assets,900px height,3s native scroll, no network throttle. AMD Ryzen AI7 445 /
Radeon840M; Mesa26.2.4. CDP confirms headed GPU compositing and rasterization.
At390px CPU4×, V2 p95 median16.7ms/worst16.8ms; baseline16.8/16.8ms.
At1440px CPU1×, V2 p95 median16.7ms/worst16.8ms; baseline16.7/16.7ms.
Worst individual V2 frame33.4ms compact/33.3ms desktop. LayoutDuration delta0
in all samples. Accessibility snapshot retains all four articles and headings.

Headless software compositing is a separate constrained profile: desktop p95
median50.1ms versus33.4ms baseline, which misses the60fps target. GPU was
explicitly reported disabled by CDP. This cost is retained in the measured
record; it is not a hardware-device PASS or a reason to discard OLD choreography.

## Limitations

WebKit is Linux automation, not native Safari/iOS. CSS text expansion is not
native browser zoom. BFCache events are synthetic. Physical devices, Firefox,
Edge, assistive-technology walkthrough, PSI and field p75CWV are not certified.
Exact downloaded GSAP3.7.1 fixtures are used. No Lighthouse100 or field3/3 claim.
CI merge gate is tracked in PR #79. No native Safari or field-performance claim.

## Reviewable screenshots

Desktop fan: [OLD](values-fidelity-chromium-old-0.118.png) /
[V2](values-fidelity-chromium-v2-0.118.png).
Desktop front hold: [OLD](values-fidelity-chromium-old-0.65.png) /
[V2](values-fidelity-chromium-v2-0.65.png).
Arabic phone: [OLD](values-compact-chromium-ar-390-old.png) /
[V2](values-compact-chromium-ar-390-v2.png).
Matrix and hashes: [measured record](values-old-fidelity-heading-79.json).
