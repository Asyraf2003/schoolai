# UI/UX Engineering — Current State and Progress Ledger

Status: `FAIL`
Updated: 2026-08-04
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-03-home-values-card-story.md`
Raw evidence: `measurements/2026-08-03-home-values-reference-motion-raw.md`
Failed runtime source: `c24e4d73fd57659df9f18eeb732d3ec755031743`
Prior implementation: `fbbc83b6672053652ac4551aea3970b825df0fcc`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, performance, or lifecycle completion.

## Active production batch

- ID: `HOME-VALUES-001-R2`
- State: `OWNER_ACCEPTED`
- Surface: homepage Values section `#nilai`
- Owner feedback date: 2026-08-04
- Scope: Values-owned DOM, CSS, JS, locale keys, focused test, transitions, and
  related architecture records
- Protected: Hero, Vision/Mission content, Programs content, Gallery, Articles,
  navigation, footer, DB/admin/routes, About, and Testimonial

## Runtime FACT from owner evidence

Four SchoolAI screenshots from current `main` prove:

- the header description overlaps the oversized title on wide layouts;
- an unwanted eyebrow remains visible;
- the title reveal/composition does not match the requested vertical masks;
- a long transition/empty interval separates the preceding section and Values;
- the card phase is reached after the header rather than being present with it.

The resupplied Lusion forward/reverse videos prove the target chronology:

- all four backs already exist in the deck state;
- fan formation precedes overlapping flips;
- the next card begins during the preceding card's early turn;
- the front overshoots by roughly `17-20deg`;
- the complete final row exits upward and returns intact on reverse scroll.

Fresh live computed evidence at `lusion.co/about`, Chrome `1363x936`:

| Item | Measured result |
|---|---|
| content field | `90vw`, inset `5vw` |
| card | `21vw`, aspect about `.717` |
| column gap | `2vw` |
| perspective | `936px = 100vh` |
| title | `12vw` |
| supporting copy | about `18vw` |
| section height | about `375.88vh` |

These are reference measurements, not permission to copy Lusion source or
absolute-layout architecture.

## Source root causes at failed head

| Concern | Current owner/failure |
|---|---|
| final Grid | structurally correct, but capped to `92rem` and card/gap maxima |
| card presence | ready and hidden states set opacity to zero |
| exit | Grid opacity fades during `0.94-1` |
| perspective | width-driven `54-66rem`, flatter than height-driven reference |
| heading | JS writes X travel; owner requires opposite vertical Y masks |
| description | side composition starts at 640px and desktop width is `29vw` |
| responsive flip | whole-root progress, not card visible fraction |
| desktop overlap | nominal stagger exists, but slow early easing hides it |
| overshoot | only `-8deg` |
| float | only `-3px..3px` and reads as static |
| pacing | `600-640svh`, materially longer than reference evidence |
| eyebrow | still rendered from locale `title` |

The semantic DOM, CSS Grid final slots, separate pose/float/flip wrappers, fixed
structural z-order, one route path, cached geometry, and single RAF controller
remain valid owners. The failing logic inside them must be replaced, not covered
by a later override.

## Owner-accepted revision

- Header line one enters from below; line two enters from above.
- Line two shifts inward on MD/LG/XL/2XL and mirrors for RTL.
- Description is hidden on XS/SM, below the title on MD, and at logical side on
  LG+.
- Cards use identical geometry and motion in LTR/RTL; content direction changes.
- Horizontal layouts use the exact accepted ratios:
  - XS: `8 / 84 / 8%`;
  - SM/MD: `4.1667 / 43.75 / 4.1667 / 43.75 / 4.1667%`;
  - LG+: `5 / 21 / 2 / 21 / 2 / 21 / 2 / 21 / 5%`.
- Every desktop back is opaque from the initial deck.
- Desktop flips overlap at roughly 25–35%, retain fan tilt through edge-on, pass
  the front by about 18deg, then settle.
- XS/SM/MD flip begins at half-card visibility and ends at full visibility.
- The final Grid never fades; sticky release carries it upward intact.
- The single trail completes before exit.
- “Nilai yang Menjadi Arah Tumbuh Anak” and locale equivalents are removed from
  visible output.

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| Current GitHub main | `PASS_SOURCE` | verified at `c24e4d73` before audit |
| Mandatory docs | `PASS_SOURCE` | full required chain read |
| Owner screenshots/video | `FAIL_RUNTIME` | failures above are visible |
| Lusion live measurements | `PASS_REFERENCE` | read-only computed geometry captured |
| Revised blueprint | `OWNER_ACCEPTED` | explicit owner specification recorded |
| Revised source | `PENDING` | next active step |
| structure/build/PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | no complete checkout |
| corrected Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | not yet available |
| PageSpeed/accessibility | `BLOCKED_BY_MISSING_EVIDENCE` | not run |

## STATUS

The published implementation is runtime `FAIL`. The root causes and revised
blueprint are now evidence-backed and owner accepted. No corrected runtime claim
exists yet.

## NEXT VALID STEP

Implement `HOME-VALUES-001-R2` only in the mapped Values owners, run every
available source proof, fast-forward `main` without force, then record the
resulting source SHA and all still-unavailable runtime gates.

