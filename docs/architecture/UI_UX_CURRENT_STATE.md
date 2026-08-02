# UI/UX Engineering — Current State and Progress Ledger

Status: BLOCKED_BY_MISSING_EVIDENCE
Updated: 2026-08-02
Repository: `Asyraf2003/schoolai`
Published main: `278dca9d9438e195a66bc7ea2f275b7394787b8e`
Merged pull request: `#30`

Commit publication is source evidence only. It does not prove rendering,
browser parity, accessibility, performance, or lifecycle behavior.

## Active production batch

Blueprint: `blueprints/2026-08-02-auth-account-access.md`

- ID: `AUTH-ACCOUNT-001`
- State: `IMPLEMENTING`
- Surface: public Login entry, authentication, accounts, role navigation,
  minimal guru/murid dashboards, and PPDB access boundary
- Published follow-up: replace the direct navbar role submenu with one
  title-case `Login` link and a localized Guru/Murid choice page
- Protected: About, Testimonial, Vision/Mission, unrelated public sections,
  academic features, and global redesign

## Current FACT and DECISION

- Google authentication never creates a user or assigns a role. Active,
  pre-provisioned admin/guru accounts may bind one verified Google ID once.
- Legacy `user` roles migrate to inert `null`; they receive no dashboard.
- Murid use case-insensitive normalized student IDs and Laravel password hashes.
- Admin, guru, and murid have strict server-side role isolation.
- Every role uses a versioned single active session; logout, disablement, reset,
  and password changes invalidate the required older sessions.
- Five failed student logins lock the normalized identity/source pair for 60
  seconds with HMAC cache keys and neutral responses.
- Admin Akun uses validated actions plus vanilla Fetch and ETag polling; no SPA,
  realtime package, token storage, or unsafe account `innerHTML` was added.
- Desktop and mobile navigation expose one localized title-case `Login` link to
  `/login`. That page offers Guru and Murid choices; the navbar no longer
  exposes direct role submenu links.
- Admin remains non-public at `/login/admin` while the internal route name
  `login` remains the protected-admin guest redirect.
- Guru and Murid forms remain on `/login/guru` and `/login/murid`, and each form
  returns to the public choice page.
- PPDB links remain absent from public menus. The open hero campaign links to
  `/ppdb`; the closed route returns a localized informational HTTP 404.
- Internal dashboards force Indonesian. Public auth and PPDB use ID/EN LTR and
  AR RTL through one semantic DOM per surface.

## Proof status

| Gate | Status | Evidence |
|---|---|---|
| Published auth/account/public targeted tests | `PASS` | 88 tests, 666 assertions at prior auth/account batch source |
| Login choice focused contract | `PASS` | 13 tests, 86 assertions in GitHub Actions run `30757503164` |
| Auth and translation regression | `PASS` | 55 tests, 373 assertions in run `30757503164` |
| Migration engines | `PASS` | SQLite and MariaDB 12.3 fresh/rollback/re-apply at prior auth/account batch |
| Vite production build | `PASS` | 92 modules transformed in run `30757503164` |
| Scoped PHP formatting | `PASS` | Pint passed for every touched PHP file in run `30757503164` |
| Full PHP suite | `PRE-EXISTING_BASELINE_DEBT` | 173 passed, 1 failed; only stale protected `HomeAboutReelTest` in run `30757561123` |
| Standard dependency audit | `PRE-EXISTING_BASELINE_DEBT` | Composer audit passed; npm audit stopped on existing PostCSS advisory `GHSA-r28c-9q8g-f849` before build/test |
| Structure checker | `PRE-EXISTING_BASELINE_DEBT` | accepted Vision/Mission and `welcome-hero.css` baseline; follow-up did not change those files |
| Diff scope | `PASS` | final PR contained only auth route/controller/view/CSS/locales/navbar test and architecture docs; no dependency or migration change |
| Publication | `PASS` | PR #30 squash-merged to `main` at `278dca9d9438e195a66bc7ea2f275b7394787b8e` |
| WebKit runtime | `BLOCKED_BY_MISSING_EVIDENCE` | prior submenu proof cannot prove the new choice-page sequence |
| Chromium runtime | `BLOCKED_BY_MISSING_EVIDENCE` | no Chromium executable in the prior environment and no follow-up run |

## Protected baseline gaps

### `TEST-GAP-001`

`HomeAboutReelTest` still requires the intentionally disabled About surface.
The full follow-up suite reproduced this as the sole failure: 173 tests passed,
one stale protected test failed. This batch does not alter About source or test.

### `DEPENDENCY-GAP-001`

The standard security workflow stops at the existing high-severity PostCSS
advisory `GHSA-r28c-9q8g-f849`. The follow-up changes neither `package.json` nor
the lockfile. Dependency remediation requires its own authorized scope.

### `STRUCTURE-GAP-001`

The checker previously reported only accepted Vision/Mission line-limit debt
and the pre-existing `welcome-hero.css` checksum mismatch. The follow-up does
not change or exclude those owners.

### `BROWSER-PROOF-GAP-001`

Prior WebKit proof covered the removed direct Login submenu. It cannot be reused
to claim the new link -> choice page -> role form sequence. Chromium remains
unavailable in the prior execution environment.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| A01 account schema and roles | `PASS` | migration/model/database constraints |
| A02 Google and student auth | `PASS` | focused authentication tests |
| A03 authorization and sessions | `PASS` | strict matrix and version tests |
| A04 admin/murid reactive UI | `PASS` | source tests and prior WebKit no-reload mutation |
| A05 PPDB access boundary | `PASS` | source tests and prior WebKit open/closed proof |
| A06 public Login choice follow-up | `PROVEN_AUTOMATED` | Pint, build, focused tests, auth regression, and full-suite isolation |
| A07 final publication | `PASS` | PR #30 merged and main SHA recorded |
| A08 follow-up browser proof | `BLOCKED_BY_MISSING_EVIDENCE` | WebKit and Chromium runtime not executed for the new flow |

## STATUS

- Login choice follow-up is published on `main` and proven at automated
  source/build level.
- Full suite adds no new regression; its sole failure is the protected stale
  About contract already outside scope.
- Protected About/Testimonial/Vision/Mission remain unchanged.
- No dependency, migration, package, or graphics runtime was added.
- Browser parity for the new route sequence remains unproven and must not be
  inferred from the prior submenu proof.

## NEXT VALID STEP

Pull `main` at or after `278dca9d9438e195a66bc7ea2f275b7394787b8e`,
then execute the new `Login -> choice -> Guru/Murid form` sequence in WebKit and
available Chromium across the required locale/direction and responsive matrix.
Record any runtime defect as a new bounded follow-up; do not reopen protected
About, Testimonial, or Vision/Mission scope.
