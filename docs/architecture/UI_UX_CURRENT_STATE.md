# UI/UX Engineering — Current State and Progress Ledger

Status: BLOCKED_BY_MISSING_EVIDENCE
Updated: 2026-08-03
Repository: `Asyraf2003/schoolai`
Source main before active batch: `61f35579bff7dea348f520b109c29c148399466f`

Commit publication proves source state only. It does not prove rendering,
responsive parity, accessibility, performance, or browser lifecycle.

## Active production batch

Blueprint: `blueprints/2026-08-03-about-vision-scroll-typography.md`

- ID: `HOME-STORY-001`
- State: `IMPLEMENTING`
- Surface: homepage About and Vision/Mission
- Reference: Codrops On-Scroll Typography Animations, Set 2, effect 25 for About
- Protected: Hero, Testimonial, School Values, Programs, Gallery, Articles,
  navigation, authentication, translations, database, and dependencies

## Current FACT and DECISION

- About is activated again with one semantic blue sticky scroll scene.
- About uses local `public/media/home/9.png` through `12.png` as white-tinted
  decorative depth layers.
- The retired About reel/video/SVG/canvas/WebGL warp source and controllers are
  removed from the active source graph.
- Vision/Mission is rebuilt as one editorial sequence using rise, fan, focus,
  and vertical stretch effects; the previous card interaction and separate
  desktop/mobile heading controllers are removed.
- ID and EN animate Unicode characters. Arabic animates complete words so
  joining and shaping remain intact.
- One shared native RAF controller owns both surfaces. No GSAP, ScrollTrigger,
  Lenis, Splitting, WebGL, external font, or package change is introduced.
- No-JS content remains server-rendered. Reduced motion resolves to static final
  content and removes the About sticky travel.
- Retired split-source entries may remain as historical manifest records; the
  structure checker validates only entries whose source entry still exists.

## Source proof status

| Gate | Status | Evidence |
|---|---|---|
| Scope diff | `PASS` | connector compare contains only About/Vision source, entries, focused tests, structure retirement handling, and architecture docs |
| Legacy standalone owners | `PASS` | old About reel/warp and Vision desktop/mobile CSS/JS files removed in branch diff |
| New source line limit | `PASS` | local line count: every new/replaced source file <= 118 lines |
| New JavaScript syntax | `PASS` | local `node --check` passed for entry, controller, split module, and Vite config |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector channel cannot run repository command |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | full repository runtime unavailable in connector channel |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | full repository runtime unavailable in connector channel |
| Focused PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | tests updated but not executed in connector channel |
| Full PHP suite | `BLOCKED_BY_MISSING_EVIDENCE` | not executed in connector channel |
| Chromium/WebKit runtime | `BLOCKED_BY_MISSING_EVIDENCE` | no browser run yet |
| Lighthouse/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | no comparable runtime run yet |

## Prior completed auth/account baseline

- Public `Login` links to the localized Guru/Murid choice page.
- Admin remains non-public at `/login/admin`; Guru and Murid role isolation and
  PPDB access boundaries remain unchanged by this batch.
- Prior automated auth publication remains at `278dca9d9438e195a66bc7ea2f275b7394787b8e`.
- Follow-up Chromium/WebKit proof for that route sequence remains unproven.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| H01 source ownership audit | `PASS` | Blade, CSS, JS, Vite, tests, locale copy, and assets inspected |
| H02 blueprint acceptance | `PASS` | owner explicitly authorized the described implementation on `main` |
| H03 semantic About rebuild | `IMPLEMENTED_SOURCE` | new active partial and blue sticky scene |
| H04 semantic Vision/Mission rebuild | `IMPLEMENTED_SOURCE` | new editorial sequence and four motion variants |
| H05 locale/motion controller | `IMPLEMENTED_SOURCE` | shared controller with Arabic word adapter and cleanup |
| H06 legacy retirement | `PASS_SOURCE` | standalone reel/warp/card/heading owners removed from branch diff |
| H07 automated proof | `BLOCKED_BY_MISSING_EVIDENCE` | repository commands have not run |
| H08 runtime matrix | `BLOCKED_BY_MISSING_EVIDENCE` | six tiers, ID/EN/AR, Chromium/WebKit, reduced motion untested |
| H09 publication | `IMPLEMENTING` | direct fast-forward to `main` pending final ref check |

## STATUS

The requested production source replacement is complete on the authorized work
ref. Source inspection confirms the intended owners and legacy removals. Build,
test, browser, accessibility, and PageSpeed claims remain blocked until their
actual gates run.

## NEXT VALID STEP

Revalidate remote `main`, compare the complete work ref against the recorded
source SHA, then fast-forward `main` without force. After publication, pull the
published SHA locally and run the mandatory automated gates before visual review.
