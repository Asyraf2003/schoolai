# Authentication, Account, and PPDB Access Blueprint

BLUEPRINT ID: `AUTH-ACCOUNT-001`
STATUS: `IMPLEMENTING`
OWNER ACCEPTED: 2026-08-02
SOURCE MAIN SHA: `af2621330ba07b757ca53de3564869f7caeb3c83`
FOLLOW-UP SOURCE SHA: `5d0c73b20c33dceb7a6e5592fd066a99f22e4815`
TARGET: `Asyraf2003/schoolai` `main` via Web AI with explicit owner authorization

## Goal and protected scope

Deliver pre-provisioned Google authentication for admin/guru, student-ID
authentication for murid, strict role isolation, one active session, account
management, minimal internal dashboards, public Login navigation, and PPDB
access owned by the open homepage campaign.

About, Testimonial, Vision/Mission, academic teacher/student features, unrelated
public sections, secrets, new packages, SPA conversion, and invasive device
fingerprinting are forbidden.

## FACT and resolved GAP

- Existing Socialite is stateful and checks explicit verified email claims.
- Existing Google login auto-creates role `user` and supports bootstrap admin.
- Existing roles are `admin` and `user`; database sessions and append-only
  security audit logs already exist.
- No guru/murid middleware, student login, single-session marker, reactive
  account UI, or realtime infrastructure exists.
- Public navigation has a PPDB CTA rather than a top-level PPDB item.
- The open PPDB hero already links its heading, description, and separate CTA
  to one registration destination.
- Owner decisions resolve student-ID format, password lifecycle, strict role
  isolation, legacy users, single-session scope, navigation, PPDB gating,
  polling, and pre-existing structure debt.
- Follow-up owner decision replaces the direct navbar Guru/Murid submenu with
  one title-case `Login` link to a dedicated account-type choice page.

## Semantic auth flows

### Public choice

`/login` renders one localized semantic choice surface. It contains Guru and
Murid choices only. Selecting Guru opens the Google teacher login page;
selecting Murid opens the student-ID/password form. The public page never starts
Google OAuth directly and never exposes Admin.

### Admin

`/login/admin` -> stateful Google -> explicit verified email -> pre-provisioned
active `admin` -> atomic one-time Google-ID binding when empty -> session
regeneration and version activation -> admin dashboard. The internal route name
`login` remains the guest redirect target for protected admin areas. Existing
valid bindings survive.

### Guru

Public choice -> `/login/guru` -> localized Google login -> the same
verified/pre-provisioned binding contract for active `guru` -> minimal
Indonesian landing containing safe identity and logout. Google profile names
never overwrite admin-owned names.

### Murid

Public choice -> `/login/murid` -> localized student login accepting student ID
and password. Student ID matches `^[A-Za-z0-9]{1,32}$`, preserves stored casing,
and uses a normalized lowercase value for uniqueness, lookup, search, and HMAC
rate keys. Neutral errors cover unknown ID, wrong password, inactive account,
and wrong role.

## Role and route contract

| Actor | Admin | Guru | Murid |
|---|---|---|---|
| admin | allow | deny | deny |
| guru | deny | allow | deny |
| murid | deny | deny | allow |
| guest/inert/inactive | deny | deny | deny |

Guest redirects are area-aware. Authorization is server-side through role
middleware and policy/validated action boundaries.

Routes add the public choice page, hidden admin login, guru/murid login and
dashboards, murid account/password, admin account CRUD/status/reset/revision
endpoints, while retaining shared logout and the existing `/ppdb`
compatibility path.

## Data and migration

Extend `users`; do not create a parallel account table:

- role nullable and cast to an `admin/guru/murid` enum;
- legacy `user` values become `null` and remain reviewable/inert;
- email becomes nullable for murid with normalized email storage;
- add display-preserving `student_id` plus lowercase `student_id_normalized`;
- add `password_changed_at` and unsigned `session_version`;
- retain password hash, `google_id`, `disabled_at`, and `last_login_at`.

Database uniqueness covers normalized student ID and normalized email behavior
on MySQL and SQLite. Migration is non-destructive and reverses safely without
deleting users. There is no `must_change_password` field.

## Google security

Remove bootstrap-admin behavior/config. Never create an OAuth callback user.
Lock candidate rows during first binding; bind only an empty Google ID for an
active pre-provisioned admin/guru with exact normalized verified email. Existing
ID/email conflicts and all unavailable accounts return one neutral public error.

## Session and password security

Every successful login atomically advances `session_version`, regenerates the
session, and stores the current version. Middleware rejects older versions.
Logout invalidates the session. A self password change advances the version,
regenerates the current session, and keeps only that session valid. Admin reset
advances the version so every old student session becomes invalid.

Admin supplies and confirms initial/reset passwords. Students may retain them.
Self-change requires current/new/confirmation. Only Laravel hashes persist;
passwords never enter responses, DOM repopulation, JS state, logs, or audits.

## Login throttling

Use Laravel RateLimiter with a cache key derived from HMAC(normalized student
ID) plus request IP. Five failures lock that source/account combination for 60
seconds. The fifth failure activates the lock, refresh cannot clear it, time
travel proves expiry, and success clears the limiter. JavaScript countdown is
advisory only.

## Account management and reactive behavior

Admin Akun supports list/search/filter, guru/murid creation, allowed edits,
enable/disable, student reset, status, and last login. Requests validate exact
role-specific fields and avoid broad mass assignment.

Blade forms retain server fallbacks; vanilla Fetch adds loading, disabled,
success, validation, empty, and failure states without unsafe `innerHTML`.
Visible tabs poll revisions about every 30 seconds with ETag/304; hidden tabs
stop or slow. No Redis, WebSocket, Reverb, Echo, Livewire, Alpine, SSE, or new
package is introduced.

## Public navigation and PPDB

Replace the existing PPDB navbar CTA with one localized title-case `Login` link
in desktop and mobile navigation. It opens `/login`, where the user chooses Guru
or Murid before reaching the appropriate form. Navbar navigation must not expose
direct Guru/Murid submenu links. Admin is never public. Remove every other
public PPDB navigation link, including Education submenu.

When PPDB is open, the homepage campaign heading or description links
semantically to the internal `/ppdb` page and the separate arrow CTA is removed.
When closed, normal article copy returns and `/ppdb` responds with a localized
closed information page and HTTP 404. Both route and hero read `PpdbSetting`.

## Locale, responsive, and accessibility

Public choice/Login/PPDB copy supports ID/EN/AR; ID/EN use Inter/LTR and AR uses
Cairo/RTL with one semantic DOM per surface. Internal screens force Indonesian.
Prove 360, 390, 640, 768, 1024, 1180, 1181, 1280, 1536, and >1536; keyboard,
pointer, focus, errors, reduced motion, Chromium, and WebKit where available.

The choice surface is one column on XS and may use two equal choices from SM
upward. Focus order remains Guru then Murid in all directions and widths.

## Audit events

Record safe account create/update/enable/disable, student reset/self-change,
successful and failed login, session invalidation, Google binding success, and
binding conflict. Never record passwords, OAuth credentials, cookies, tokens,
raw session IDs, or raw login identifiers.

## Execution and proof

1. PROVEN: model/migration/role/session foundations and Google hardening.
2. PROVEN: guru/murid login, dashboard, password, limiter, and middleware.
3. PROVEN: admin account management, Fetch, revision/ETag, polling, and audit.
4. PROVEN_AUTOMATED: direct navbar role submenu replaced by a localized Login
   choice page while preserving hidden admin login and existing role forms.
5. PROVEN_AUTOMATED: scoped Pint, Vite build, 13 public-auth tests with 86
   assertions, and 55 auth/translation tests with 373 assertions passed.
6. PRE-EXISTING_BASELINE_DEBT: full PHP suite produced 173 passes and only the
   stale protected `HomeAboutReelTest` failure; standard CI also reproduced the
   existing PostCSS advisory before build/test.
7. PENDING: final diff revalidation, expected-head merge, remote SHA recording,
   and fresh WebKit/Chromium runtime proof for the new choice flow.

Pre-existing Vision/Mission line-limit failures are `PRE-EXISTING_BASELINE
DEBT`. Do not change/exclude those files. No new structure failure is accepted.
Browser proof remains `BLOCKED_BY_MISSING_EVIDENCE` until the new navigation and
choice flow run in the required engines; automated proof must not be relabeled
as browser proof.

Rollback restores the former navbar submenu, removes the public choice route and
view, and returns admin login to `/login`. Existing linked accounts and all auth
security state remain untouched. Publication is fast-forward only and never
substitutes for runtime proof.
