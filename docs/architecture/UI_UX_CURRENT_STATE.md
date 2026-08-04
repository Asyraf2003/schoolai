# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-04
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-03-home-values-card-story.md`
Raw evidence: `measurements/2026-08-03-home-values-reference-motion-raw.md`
Failed runtime source: `c24e4d73fd57659df9f18eeb732d3ec755031743`
Prior implementation: `fbbc83b6672053652ac4551aea3970b825df0fcc`
Revision blueprint checkpoint: `e56b00a455848772905def3c3ab63016dd24c303`
Revision source head: `c1c45381d7b1824ab72ca7d6d85fed77889e58ad`
Focused correction blueprint: `0521c9aa1641c8d547099f8a7c758f943a32a0ae`
Focused correction source head: `95b3b78f91d42d536881324eda6d453c7139a015`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, performance, or lifecycle completion.

## Active production batch

- ID: `HOME-VALUES-001-R3`
- State: `IMPLEMENTED_SOURCE`
- Surface: homepage Values section `#nilai`
- Owner feedback date: 2026-08-04
- Scope: focused Values DOM, CSS, JS, feature test, and architecture records
- Protected: Hero, Vision/Mission content, Programs content, Gallery, Articles,
  navigation, footer, DB/admin/routes, About, and Testimonial

## Runtime FACT from owner evidence

Four SchoolAI screenshots from failed baseline `c24e4d73` prove:

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
- Description is hidden on XS/SM, below the title on MD and portrait LG, at the
  logical side on landscape LG, and at the logical side on XL+.
- Cards use identical geometry and motion in LTR/RTL; content direction changes.
- Horizontal layouts use the exact accepted ratios:
  - XS/SM: `8 / 84 / 8%`;
  - MD/LG: `4.1667 / 43.75 / 4.1667 / 43.75 / 4.1667%`;
  - XL+: `5 / 21 / 2 / 21 / 2 / 21 / 2 / 21 / 5%`.
- Every desktop back is opaque from the initial deck.
- Desktop flips overlap at roughly 25–35%, retain fan tilt through edge-on, pass
  the front by about 18deg, then settle.
- XS/SM/MD/LG flip begins at half-card visibility and ends at full visibility.
- The final Grid never fades; sticky release carries it upward intact.
- The single trail completes before exit.
- “Nilai yang Menjadi Arah Tumbuh Anak” and locale equivalents are removed from
  visible output.

## Focused correction R3 FACT and decision

Latest owner runtime feedback proves the R2 source still failed the desired
presentation:

- desktop line two began at the wrong inline position and shifted the wrong way;
- the desktop heading stayed in the sticky stage instead of leaving immediately;
- front faces contained duplicate code/logo/footer/rule decoration and copy was
  too small at phone, tablet, and desktop widths;
- card backs read as a logo/orbit rather than an Islamic geometric field;
- float was perpetual instead of three bounded bounces before upward exit;
- XS/SM/MD/LG inherited the desktop overshoot-and-return flip instead of one
  smooth back-to-front half turn;
- tablet/phone heading entry was not reliably visible.

R3 keeps the six-tier geometry and neutral LTR/RTL card chronology. It changes
only the proven Values owners: logical heading composition, natural heading
exit, front hierarchy, geometric back art, card-relative type scale, bounded
desktop bounce/exit, responsive pure flip, and focused DOM assertions.

## Smoothness audit boundary

Read-only live evidence from `lusion.co/about` at `1363x936` shows
`html/body overflow:hidden`, a viewport-fixed UI approximately `24760px`
high, and fixed canvases. Lusion therefore routes input through one virtual
scroll/render pipeline. SchoolAI uses native document scroll plus separate
surface-local RAF controllers; its Values spring smooths only Values progress,
not the whole page. Large transition blur/backdrop layers can add paint cost.

This is a global architecture difference, not an asset-download gap. R3 does
not alter global scroll, other RAF owners, dependencies, canvas, or WebGL.

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| Current GitHub main | `PASS_SOURCE` | verified at `95b3b78f` after non-force fast-forward |
| Mandatory docs | `PASS_SOURCE` | full required chain read |
| Baseline owner screenshots/video | `FAIL_RUNTIME` | failures apply to superseded `c24e4d73` |
| Lusion live measurements | `PASS_REFERENCE` | read-only computed geometry captured |
| Revised blueprint | `PASS_SOURCE` | accepted contract at `e56b00a4` |
| Revised source/scope | `PASS_SOURCE` | atomic 16-file R2 patch at `c1c45381` |
| Focused R3 blueprint | `PASS_SOURCE` | owner-accepted checkpoint at `0521c9aa` |
| Focused R3 source/scope | `PASS_SOURCE` | atomic 11-file Values/test patch at `95b3b78f` |
| JavaScript syntax | `PASS_LOCAL_PATCH` | every changed R3 Values module passed `node --check` |
| CSS parse | `PASS_LOCAL_PATCH` | all four changed R3 CSS owners parsed with Lightning CSS |
| PHP syntax only | `PASS_LOCAL_PATCH` | focused Pest source parsed with `php-parser`; not a Laravel test |
| six-tier ratio math | `PASS_LOCAL_PATCH` | 84vw; 43.75vw/4.1667vw; 21vw/2vw relationships verified |
| R3 motion curves | `PASS_LOCAL_PATCH` | responsive 180/90/0deg, desktop 34.59deg overlap, three bounce cycles, heading travel from progress >0, exit 0->1 |
| source line limit | `PASS_LOCAL_PATCH` | every Values source owner remains <=200 lines |
| structure/build/PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | no complete checkout |
| corrected Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | corrected source not rendered in this channel |
| PageSpeed/accessibility | `BLOCKED_BY_MISSING_EVIDENCE` | not run |

## STATUS

The focused R3 Values source is published on `main`. Source-level contracts
pass where this channel can execute them. Runtime remains unpromoted: owner
feedback makes R2 a runtime `FAIL`, while `95b3b78f` remains
`BLOCKED_BY_MISSING_EVIDENCE` until rendered and reviewed.

## NEXT VALID STEP

Pull current `main`, verify source commit `95b3b78f` is in its ancestry,
render the Values section, and collect owner feedback plus the required
Chromium/WebKit matrix without promoting unobserved gates.
