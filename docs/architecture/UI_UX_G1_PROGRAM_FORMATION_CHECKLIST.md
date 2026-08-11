# G1 Program Formation → Character Handoff Checklist

Status: `OWNER-ACCEPTED / UNPROVEN`
Updated: 2026-08-11
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Blueprint: `blueprints/2026-08-11-g1-program-formation-character-handoff.md`
Parent release checklist: `UI_UX_RELEASE_READINESS_CHECKLIST.md`

This checklist records proof for G1 only. A checkbox may become `[x]` only when
its required source/runtime evidence has been recorded against an exact source
head.

## A — Source ownership audit

- [ ] Current Program background owner identified.
- [ ] Current Program card visibility/motion owner identified.
- [ ] Current Program scroll-progress owner identified.
- [ ] Program responsive geometry/spacing owners identified.
- [ ] Shared Program → Values visual-world background owner identified.
- [ ] Pondasi Karakter trigger/replay owner identified.
- [ ] Reduced-motion owner identified.
- [ ] Existing tests protecting Program open/detail/back and hit-layer behavior
      identified.
- [ ] Smallest implementation owner selected from evidence, not preference.

## B — White empty Program field

- [ ] Program establishes a visually white field before card formation.
- [ ] Before reveal, cards are visually absent without being removed from the
      semantic DOM solely for animation.
- [ ] Hidden cards begin only slightly below final position; there is no dramatic
      off-screen throw.
- [ ] Hidden state causes no layout jump or horizontal overflow.
- [ ] No forced document-scroll position is introduced.

## C — Sequential formation

- [ ] Card 1 reveals first.
- [ ] Card 2 reveals after card 1 has begun/established.
- [ ] Card 3 reveals next.
- [ ] Card 4 reveals next.
- [ ] Card 5 reveals next.
- [ ] Card 6 reveals last.
- [ ] Each card resolves into the existing final geometry rather than moving the
      grid itself.
- [ ] Downward visible state accumulates deterministically `0 → 1 → 2 → 3 → 4 → 5 → 6`.
- [ ] Existing phone/tablet/desktop Program grid topology is unchanged.

## D — Reverse and rapid-scroll behavior

- [ ] Reverse scroll unwinds deterministically `6 → 5 → 4 → 3 → 2 → 1 → 0`.
- [ ] No reveal timeline keeps running against the current reverse target.
- [ ] Rapid down/up/down scroll does not leave stale opacity or transform state.
- [ ] No bounce, random replay, duplicate owner, or card flicker appears.
- [ ] No wheel hijacking, automatic snap, projected landing, anchor settling,
      `window.scrollTo`, or one-wheel-notch-per-card behavior is introduced.

## E — Full Program hold

- [ ] After card 6 completes, all six cards remain fully visible on white.
- [ ] A deliberate breathing interval exists before color transition starts.
- [ ] Blue does not begin at the same instant card 6 completes.
- [ ] Hold distance feels intentional on representative phone, tablet, and
      desktop rather than being tuned only for desktop.

## F — White → blue handoff

- [ ] Color handoff begins only after the complete Program hold.
- [ ] Program white transitions continuously toward Character/Values `#2038ff`.
- [ ] No stripe staircase, sudden wipe, body-background flash, or accidental seam.
- [ ] One visual owner controls the handoff at each scroll state.
- [ ] Transition layers do not steal Program pointer events.
- [ ] Reverse scroll returns blue → white deterministically.
- [ ] Rapid reverse does not leave a stale blue layer over Program.

## G — Pondasi Karakter takeover

- [ ] Pondasi Karakter takes over only after Character/Values background ownership
      is established.
- [ ] Downward first-entry reveal follows the accepted behavior.
- [ ] Upward return from below preserves the accepted visible/static behavior.
- [ ] Returning far enough into Program may re-arm according to the existing
      contract.
- [ ] G1 does not create a duplicate heading controller.

## H — Protected Program behavior

- [ ] Existing Program card layout is unchanged.
- [ ] Existing Program copy/media/detail composition is unchanged.
- [ ] Program card open works by physical click/tap.
- [ ] Program Back works by physical click/tap.
- [ ] Program controls remain clickable near the Program → Values handoff.
- [ ] Existing Values sibling hit-layer protection remains effective.
- [ ] Values cards and worm/line renderer are not redesigned by G1.
- [ ] Gallery and Article remain untouched.

## I — Reduced motion and failure resilience

- [ ] Reduced-motion mode remains readable and does not require pronounced card
      translation.
- [ ] All Program cards remain reachable/readable if motion initialization fails.
- [ ] White → blue ownership remains understandable in reduced-motion mode.
- [ ] JavaScript failure does not permanently hide Program content.

## J — Automated proof

- [ ] `git diff --check` PASS.
- [ ] Focused Program tests PASS.
- [ ] Focused Values/handoff tests PASS where affected.
- [ ] `npm run check:structure` PASS.
- [ ] `npm run build` PASS.
- [ ] `php artisan test` PASS with `0 failed` after G1 implementation.
- [ ] Exact tested source SHA recorded.

## K — Representative runtime proof before G2/G3

- [ ] Initial white field captured/proven.
- [ ] 1 → 6 sequential reveal captured/proven.
- [ ] Six-card white hold captured/proven.
- [ ] White → blue transition captured/proven.
- [ ] Pondasi takeover captured/proven.
- [ ] Reverse sequence captured/proven.
- [ ] Rapid scroll recovery captured/proven.
- [ ] Physical Program open/back interaction proven around the handoff.
- [ ] No horizontal overflow/layout jump in representative desktop evidence.
- [ ] Reduced-motion representative proof passes.

## Exit rule

G1 becomes `PASS` only after sections A through K are proven for the bounded G1
implementation. Full phone/tablet/desktop + Chromium/Safari-WebKit + ID/EN LTR +
AR RTL matrix remains the next G2/G3 gate and is not implied by G1 PASS.
