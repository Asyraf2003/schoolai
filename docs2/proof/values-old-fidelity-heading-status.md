# Values OLD fidelity / heading sequence — issue #78

Status: BLOCKED_BY_MISSING_EVIDENCE while the final browser/CI run completes.
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

## Proof method and gates

Reference pages serve archived CSS/controller and original typography via
Playwright interception, with the same translated content. No application route,
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
- Build and structure298sources≤200lines PASS; diff check PASS; pure JS11PASS.
- Scoped PHP Values/Program13tests264assertions PASS.
- Full PHP336tests:190pass,71fail,75error,1746assertions. Exact failure/error
  names equal fresh baseline; zero added or removed. Full suite remains FAIL.
- Program Chromium Arabic scroll restore2950vs2925 is reproduced unchanged
  on detached main. Existing60ms SVG test sleeps also fail on baseline; changed
  the test to await native scroll progress and paint, not production SVG code.

## Limitations

WebKit is Linux automation, not native Safari/iOS. CSS text expansion is not
native browser zoom. BFCache events are synthetic. Physical devices, Firefox,
Edge, assistive-technology walkthrough, PSI and field p75CWV are not certified.
Exact downloaded GSAP3.7.1 fixtures are used. No Lighthouse100 or field3/3 claim.
Final measured browser results, performance samples, CI and merged SHA pending.
