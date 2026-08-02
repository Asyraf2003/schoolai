# UI/UX Engineering — Current State and Progress Ledger

Status: BLOCKED_BY_MISSING_EVIDENCE
Updated: 2026-08-02
Repository: `Asyraf2003/schoolai`
Published auth/account main: `5d0c73b20c33dceb7a6e5592fd066a99f22e4815`
Active follow-up branch: `ai/login-choice-page-20260802`

Commit publication is source evidence only. It does not prove rendering,
browser parity, accessibility, performance, or lifecycle behavior.

## Active production batch

Blueprint: `blueprints/2026-08-02-auth-account-access.md`

- ID: `AUTH-ACCOUNT-001`
- State: `IMPLEMENTING`
- Surface: public Login entry, authentication, accounts, role navigation,
  minimal guru/murid dashboards, and PPDB access boundary
- Active follow-up: replace the direct navbar role submenu with one title-case
  `Login` link and a localized Guru/Murid choice page
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
- Follow-up owner decision: desktop and mobile navigation expose one localized
  title-case `Login` link to `/login`. That page offers Guru and Murid choices;
  the navbar no longer exposes role submenu links.
- Admin remains non-public at `/login/admin` while the internal route name
  `login` remains the default protected-admin guest redirect.
- Guru and Murid forms remain on `/login/guru` and `/login/murid`, and each form
  returns to the public choice page.
- PPDB links are absent from public menus. The open hero campaign links to
  `/ppdb`; the closed route returns a localized informational HTTP 404.
- Internal dashboards force Indonesian. Public auth and PPDB use ID/EN LTR and
  AR RTL through one semantic DOM per surface.

## Proof status

| Gate | Status | Evidence |
|---|---|---|
| Published auth/account/public targeted tests | `PASS` | 88 tests, 666 assertions at `5d0c73b` batch source |
| Login choice focused source contract | `BLOCKED_BY_MISSING_EVIDENCE` | tests updated on follow-up branch; Web AI cannot execute PHP/Vite commands |
| Migration engines | `PASS` | SQLite and MariaDB 12.3 fresh/rollback/re-apply at published batch |
| Vite production build | `BLOCKED_BY_MISSING_EVIDENCE` | follow-up CSS/Blade change has not been built in an execution environment |
| Scoped PHP formatting | `BLOCKED_BY_MISSING_EVIDENCE` | follow-up PHP files have not been run through Pint |
| Full PHP suite | `BLOCKED_BY_MISSING_EVIDENCE` | published batch had 185/186 with protected stale `HomeAboutReelTest`; follow-up not executed |
| Structure checker | `PRE-EXISTING_BASELINE_DEBT` | accepted Vision/Mission and `welcome-hero.css` items; follow-up delta not executed |
| Diff whitespace | `BLOCKED_BY_MISSING_EVIDENCE` | connector diff review pending; local `git diff --check` unavailable |
| WebKit runtime | `BLOCKED_BY_MISSING_EVIDENCE` | prior submenu proof does not prove the new choice-page flow |
| Chromium runtime | `BLOCKED_BY_MISSING_EVIDENCE` | no Chromium executable in prior environment and no follow-up run |

## Protected baseline gaps

### `TEST-GAP-001`

`HomeAboutReelTest` requires the intentionally disabled About surface. This
batch does not alter About source or its stale protected test.

### `STRUCTURE-GAP-001`

The checker previously reported only accepted Vision/Mission line-limit debt
and the pre-existing `welcome-hero.css` checksum mismatch. The follow-up does
not change or exclude those owners, but the checker must run again.

### `BROWSER-PROOF-GAP-001`

Prior WebKit proof covered the direct Login submenu. It cannot be reused to
claim the new link -> choice page -> role form sequence. Chromium remains
unavailable in the prior execution environment.

### `LOGIN-CHOICE-PROOF-GAP-001`

The follow-up branch contains the source, locale, responsive CSS, and focused
test contract for the new choice page. Required PHP, build, structure, and
runtime commands have not run in the Web AI connector channel.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| A01 account schema and roles | `PASS` | migration/model/database constraints |
| A02 Google and student auth | `PASS` | focused authentication tests |
| A03 authorization and sessions | `PASS` | strict matrix and version tests |
| A04 admin/murid reactive UI | `PASS` | source tests and prior WebKit no-reload mutation |
| A05 PPDB access boundary | `PASS` | source tests and prior WebKit open/closed proof |
| A06 public Login choice follow-up | `IMPLEMENTING` | branch source complete; execution proof pending |
| A07 final publication | `BLOCKED_BY_MISSING_EVIDENCE` | follow-up automated/runtime gates have not run |

## STATUS

- Published auth/account implementation: available on `main` at `5d0c73b`.
- Login choice follow-up: source implementation complete on the active branch,
  but not yet proven or published.
- Protected About/Testimonial/Vision/Mission: unchanged.
- New dependency or graphics runtime: none.

## NEXT VALID STEP

Execution channel: `owner/local terminal` or `Terminal Codex`.

Fetch branch `ai/login-choice-page-20260802`, then run the focused public-auth
test, Pint for touched PHP, `npm run check:structure`, `npm run build`, full
`php artisan test`, and responsive ID/EN/AR runtime proof at 360, 640, 1180,
1181, 1280, and 1536 in WebKit and available Chromium. If no new failure is
introduced, fast-forward `main` and record the remote SHA. Do not mark the
blueprint `PROVEN` from connector source review alone.
