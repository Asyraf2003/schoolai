# UI/UX Engineering — Current State and Progress Ledger

Status: BLOCKED_BY_MISSING_EVIDENCE
Updated: 2026-08-02
Repository: `Asyraf2003/schoolai`
Auth/account batch base: `af2621330ba07b757ca53de3564869f7caeb3c83`

Commit publication is source evidence only. It does not prove rendering,
browser parity, accessibility, performance, or lifecycle behavior.

## Active production batch

Blueprint: `blueprints/2026-08-02-auth-account-access.md`

- ID: `AUTH-ACCOUNT-001`
- State: `IMPLEMENTING`
- Surface: authentication, accounts, role navigation, minimal guru/murid
  dashboards, navbar Login, and PPDB access boundary
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
- Public navigation exposes localized Login > Guru/Murid and no Admin choice.
- PPDB links are absent from public menus. The open hero campaign links to
  `/ppdb`; the closed route returns a localized informational HTTP 404.
- Internal dashboards force Indonesian. Public auth and PPDB use ID/EN LTR and
  AR RTL through one semantic DOM per surface.

## Proof status

| Gate | Status | Evidence |
|---|---|---|
| Auth/account/public targeted tests | `PASS` | 88 tests, 666 assertions |
| Migration engines | `PASS` | SQLite and MariaDB 12.3 fresh/rollback/re-apply; normalized unique indexes verified |
| Vite production build | `PASS` | `npm run build`, 92 modules |
| Scoped PHP formatting | `PASS` | Pint passes for every new or touched PHP file |
| Full PHP suite | `BLOCKED_BY_MISSING_EVIDENCE` | 185/186, 1273 assertions; only protected stale `HomeAboutReelTest` |
| Structure checker | `PRE-EXISTING_BASELINE_DEBT` | exact accepted Vision/Mission and `welcome-hero.css` items; no new item |
| Diff whitespace | `PASS` | `git diff --check` |
| WebKit runtime | `PASS` | 30 locale/viewport combinations and nine required flows |
| Chromium runtime | `BLOCKED_BY_MISSING_EVIDENCE` | no Chromium executable in environment |

## Protected baseline gaps

### `TEST-GAP-001`

`HomeAboutReelTest` requires the intentionally disabled About surface. This
batch does not alter About source or its stale protected test.

### `STRUCTURE-GAP-001`

The checker still reports only the accepted Vision/Mission line-limit debt and
the pre-existing `welcome-hero.css` checksum mismatch. This batch does not
change or exclude those owners.

### `BROWSER-PROOF-GAP-001`

WebKitGTK proves Login menus, login forms, admin Akun reactivity, murid password
flow, PPDB open/closed, Arabic RTL, and exact 1180/1181 behavior. Chromium is
unavailable, so cross-engine proof remains incomplete and must not be claimed.

Runtime proof also found and resolved two in-scope defects: an obsolete 1200px
hamburger owner conflicting with the 1180/1181 contract, and account mutation
fields being disabled before the Fetch `FormData` snapshot.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| A01 account schema and roles | `PASS` | migration/model/database constraints |
| A02 Google and student auth | `PASS` | focused authentication tests |
| A03 authorization and sessions | `PASS` | strict matrix and version tests |
| A04 admin/murid reactive UI | `PASS` | source tests and WebKit no-reload mutation |
| A05 Login navigation and PPDB | `PASS` | source tests and WebKit locale/boundary/open/closed proof |
| A06 final publication | `BLOCKED_BY_MISSING_EVIDENCE` | Chromium unavailable; protected stale full-suite test lacks owner disposition |

## STATUS

- Source implementation: `IMPLEMENTING`; targeted, build, and WebKit proof pass.
- Protected About/Testimonial/Vision/Mission: unchanged.
- New dependency or graphics runtime: none.
- Publication: not yet committed or pushed.

## NEXT VALID STEP

Execution channel: `owner-authorized local terminal`.

Complete final source/security diff review. Publication requires an owner
disposition for the stale protected `HomeAboutReelTest`: waive it as an accepted
baseline debt or authorize a bounded test-only correction. Chromium remains an
environment proof gap. Do not mark the blueprint `PROVEN` or publish while the
full-suite requirement is unresolved.
