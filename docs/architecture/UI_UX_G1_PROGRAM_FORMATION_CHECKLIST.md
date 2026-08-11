# G1 Program Formation → Character Handoff Checklist

Status: `IMPLEMENTED / PARTIALLY OWNER-VALIDATED / PROOF-OPEN`
Updated: 2026-08-12
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Current implementation head before docs sync: `5830576134ab69904800f6956dbe4be79f03c86e`
Blueprint: `blueprints/2026-08-11-g1-program-formation-character-handoff.md`
Parent release checklist: `UI_UX_RELEASE_READINESS_CHECKLIST.md`

This checklist records proof for G1 only. A checkbox becomes `[x]` only when the
required evidence is known. Source ownership facts may be closed from source
audit; runtime-behavior boxes remain open until representative runtime proof is
recorded.

## A — Source ownership audit

- [x] Current Program background owner identified.
- [x] Current Program card visibility/motion owner identified.
- [x] Current Program scroll-progress owner identified.
- [x] Program responsive geometry/spacing owners identified.
- [x] Shared Program → Values visual-world background owner identified.
- [x] Pondasi Karakter trigger/replay owner identified.
- [x] Reduced-motion owner identified.
- [x] Existing tests protecting Program open/detail/back and hit-layer behavior
      identified.
- [x] Smallest implementation owner selected from evidence, not preference.

Source owner summary:

- Program formation: `program-journey/formation.js`;
- Program detail/open/back: existing Program controller/integration;
- Program responsive geometry: existing Program journey CSS;
- white → blue visual world: `program-values-world.js` + `story-kinetic.css`;
- Pondasi heading replay: `values/heading-state.js`;
- Values story smoothing: `values/motion.js` + `values/controller.js`;
- Values desktop card choreography: `values/desktop-layout.js` +
  `values/desktop-keyframes.js`;
- Values worm/line/spatial: existing Values spatial owner.

## B — White empty Program field

- [ ] Program establishes a visually white field before card formation on the
      current tested head.
- [ ] Before reveal, cards are visually absent without being removed from the
      semantic DOM solely for animation.
- [ ] Hidden cards begin only slightly below final position; there is no dramatic
      off-screen throw.
- [ ] Hidden state causes no layout jump or horizontal overflow.
- [x] No forced document-scroll position is introduced by the G1 formation owner.

Source note: white start and card formation are implemented. Representative
current-head runtime proof is still required before the visual boxes above close.

## C — Sequential formation

- [ ] Card 1 reveals first in representative runtime proof.
- [ ] Card 2 reveals after card 1 has begun/established.
- [ ] Card 3 reveals next.
- [ ] Card 4 reveals next.
- [ ] Card 5 reveals next.
- [ ] Card 6 reveals last.
- [ ] Each card resolves into the existing final geometry rather than moving the
      grid itself.
- [ ] Downward visible state accumulates deterministically `0 → 1 → 2 → 3 → 4 → 5 → 6`.
- [x] Existing phone/tablet/desktop Program grid topology was not redesigned by
      the bounded G1 implementation.

## D — Reverse and rapid-scroll behavior

- [ ] Reverse scroll unwinds deterministically `6 → 5 → 4 → 3 → 2 → 1 → 0`.
- [ ] No reveal timeline keeps running against the current reverse target.
- [ ] Rapid down/up/down scroll does not leave stale opacity or transform state.
- [ ] No bounce, random replay, duplicate owner, or card flicker appears.
- [x] No wheel hijacking, automatic snap, projected landing, anchor settling,
      `window.scrollTo`, or one-wheel-notch-per-card behavior was introduced by
      G1 source.

## E — Full Program hold

- [ ] After card 6 completes, all six cards remain fully visible on white.
- [ ] A deliberate breathing interval exists before color transition starts.
- [ ] Blue does not begin at the same instant card 6 completes.
- [ ] Hold distance feels intentional on representative phone, tablet, and
      desktop rather than being tuned only for desktop.

## F — White → blue handoff

- [ ] Color handoff begins only after the complete Program hold on the current
      runtime head.
- [x] Source implements a continuous Program white → Character/Values `#2038ff`
      visual-world transition.
- [ ] No stripe staircase, sudden wipe, body-background flash, or accidental seam.
- [x] One shared visual-world owner controls the color handoff in source.
- [ ] Transition layers do not steal Program pointer events in current runtime.
- [ ] Reverse scroll returns blue → white deterministically.
- [ ] Rapid reverse does not leave a stale blue layer over Program.

Owner runtime note: the owner explicitly reported that the transition itself is
visually OK. Full reverse/rapid/hit-layer proof remains open.

## G — Pondasi Karakter takeover

- [ ] Pondasi Karakter takes over only after Character/Values background ownership
      is established.
- [ ] Downward first-entry reveal follows the accepted behavior.
- [ ] Upward return from below preserves the accepted visible/static behavior.
- [ ] Returning far enough into Program may re-arm according to the existing
      contract.
- [x] G1 did not create a duplicate heading controller.

## H — Protected Program/Values behavior

- [x] Existing Program card layout was not redesigned by G1.
- [x] Existing Program copy/media/detail composition was not redesigned by G1.
- [ ] Program card open works by physical click/tap on current head.
- [ ] Program Back works by physical click/tap on current head.
- [ ] Program controls remain clickable near the Program → Values handoff.
- [ ] Existing Values sibling hit-layer protection remains effective on current
      runtime head.
- [x] Values worm/line/spatial renderer was not redesigned by G1.
- [x] Gallery and Article remained untouched by the bounded G1 visual work.

## I — Values desktop entry-card tuning guardrail

The owner requested a much tighter initial deck position while preserving the
accepted card animation.

Current source target at `5830576134ab...`:

- `hiddenPose`: `center.y + geometry.cardHeight * 0.012`;
- `deckPose`: `center.y + geometry.cardHeight * 0.008 + index * 2`.

These are center-relative pose ratios, not a literal physical pixel contract.

Regression history proved that spacing-only tuning must **not**:

- measure heading DOM position inside card choreography;
- replace center-relative card poses with heading/document-relative poses;
- read raw `window.scrollY` as a second card-motion driver;
- add `centerRelease()`/capture logic;
- change fan/flip phases merely to alter start spacing.

The original `hidden → deck → fan → preFlip → flip` choreography was restored
before the current bounded ratio tuning. The owner confirmed that restored motion
returned to the desired behavior.

- [ ] Owner visually confirms the current tight entry position is final.
- [ ] Current tight entry position preserves the restored animation under slow,
      normal, and rapid scroll.

## J — Reduced motion and failure resilience

- [ ] Reduced-motion mode remains readable and does not require pronounced card
      translation.
- [ ] All Program cards remain reachable/readable if motion initialization fails.
- [ ] White → blue ownership remains understandable in reduced-motion mode.
- [ ] JavaScript failure does not permanently hide Program content.

## K — Automated proof on current G1 head

- [ ] `git diff --check` PASS.
- [ ] Focused Program tests PASS.
- [ ] Focused Values/handoff tests PASS where affected.
- [ ] `npm run check:structure` PASS.
- [ ] `npm run build` PASS.
- [ ] `php artisan test` PASS with `0 failed` after G1 implementation/tuning.
- [ ] Exact tested source SHA recorded.

The previous owner-reported full PHP proof was `206 passed (1794 assertions)`,
but it predates the latest G1/tuning head and therefore does not close this
section.

## L — Representative runtime proof before G2/G3

- [ ] Initial white field captured/proven.
- [ ] 1 → 6 sequential reveal captured/proven.
- [ ] Six-card white hold captured/proven.
- [ ] White → blue transition captured/proven on the frozen G1 head.
- [ ] Pondasi takeover captured/proven.
- [ ] Reverse sequence captured/proven.
- [ ] Rapid scroll recovery captured/proven.
- [ ] Physical Program open/back interaction proven around the handoff.
- [ ] No horizontal overflow/layout jump in representative desktop evidence.
- [ ] Reduced-motion representative proof passes.

## Values worm/line boundary

The Values worm/line/spatial renderer is protected during G1 and is not a hidden
unfinished requirement of this checklist. If the owner wants line/worm visual
polish next, open a separate bounded scope after the current G1 state is frozen.
Do not mix line-renderer changes into G1 proof.

## Exit rule

G1 becomes `PASS` only after the remaining runtime and automated evidence above
is proven for one frozen source head. Full phone/tablet/desktop +
Chromium/Safari-WebKit + ID/EN LTR + AR RTL matrix remains the next G2/G3 gate
and is not implied by G1 PASS.
