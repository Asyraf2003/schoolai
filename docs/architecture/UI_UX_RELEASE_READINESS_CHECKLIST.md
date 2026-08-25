# UI/UX Release Readiness Checklist

Status: `OWNER-ACCEPTED / UNPROVEN`
Updated: 2026-08-11
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Source baseline: `ce1aa637c08cc70f13cbe254ed4da32e8bafd254`
Active blueprint: `blueprints/2026-08-11-release-readiness-program-values-hardening.md`

This file is a release checklist, not a claim of completion. A box may be changed
to `[x]` only when the required automated or runtime proof has been recorded.
Visual approval, automated proof, browser proof, locale proof, and security proof
must not substitute for one another.

## G0 — Clean automated baseline

- [x] `git diff --check` passes on the intended source head.
- [x] `npm run check:structure` passes.
- [x] `npm run build` passes.
- [x] `php artisan test` passes with `0 failed`.
- [x] Intended H7-G0 source/test head is recorded as `8f13f716`; runtime
      certification has not started.

No visual refactor starts while G0 is unknown or red unless the owner explicitly
opens a separate bounded incident.

## G1 — Program → Character visual contract

Owner goal:

- Program final visual field is white.
- Character/Values final visual field is the approved blue field, currently
  `#2038ff` unless a later owner decision changes the token.
- The Program → Character handoff is a deliberate transition from white to blue,
  not an accidental overlay, flash, stripe, or stacking artifact.

Checklist:

- [ ] Program background is visually white at the accepted resting state.
- [ ] Character/Values background is visually blue at the accepted resting state.
- [ ] Forward scroll transitions white → blue without visible seam, flash, or
      unintended intermediate layer.
- [ ] Reverse scroll transitions blue → white deterministically.
- [ ] Rapid forward/reverse scroll does not leave stale colors or hit layers.
- [ ] Program card open/detail/back interaction remains physically clickable.
- [ ] Character heading/card/worm behavior is not changed merely to satisfy the
      background transition.
- [ ] Reduced-motion fallback preserves readable white → blue state ownership.

## G2 — Responsive and browser runtime matrix

Use the canonical six tiers from `UI_UX_RESPONSIVE_LOCALE_MATRIX.md`.

- [ ] XS phone proof, including 360 and 390 px representatives.
- [ ] SM proof at 640 px.
- [ ] MD tablet proof at 768 px.
- [ ] LG proof at 1024 px.
- [ ] XL proof at 1280/1440 px.
- [ ] 2XL proof at 1536 px and a wide desktop representative.
- [ ] Boundary-sensitive proof is repeated where the changed surface crosses a
      declared breakpoint.
- [ ] No horizontal overflow, clipped essential copy, inaccessible control, or
      broken hit target exists at any required width.
- [ ] Chromium proof passes.
- [ ] Safari macOS proof passes when an Apple runtime is available.
- [ ] Safari iPhone/iPad proof passes when an Apple device/runtime is available.
- [ ] WebKit proof may be recorded as supporting evidence but must not be labeled
      as physical Safari proof when Safari itself was not run.

## G3 — Locale and direction proof

- [ ] Indonesian LTR passes.
- [ ] English LTR passes.
- [ ] Arabic RTL passes.
- [ ] One semantic DOM is preserved; no separate LTR/RTL page implementation is
      introduced for the same surface.
- [ ] RTL alignment, reading order, directional controls, and spatial placement
      are intentional rather than mechanically mirrored without need.
- [ ] LTR and RTL Program open/detail/back interactions are physically tested.
- [ ] Long translated titles/descriptions remain readable across the responsive
      matrix.

## G4 — Blade presentation boundary

“Blade contains no PHP” is defined technically as: production `.blade.php`
files contain no raw `<?php ... ?>`, no `@php ... @endphp`, and no business/data
access logic. Blade directives and escaped presentation expressions remain
allowed because Blade itself is a PHP templating system.

- [ ] No production Blade file contains raw `<?php` blocks.
- [ ] No production Blade file contains `@php` blocks.
- [ ] Blade files do not run database queries.
- [ ] Blade files do not call external services or storage APIs.
- [ ] Blade files do not read deployment secrets or `env()` directly.
- [ ] Business decisions, normalization, aggregation, and data preparation live
      in controllers/actions/services/view models/component classes as suitable.
- [ ] Blade remains responsible for semantic presentation, localization,
      iteration, conditional presentation, slots/components, and escaped output.
- [ ] Automated structure checks guard the boundary so raw PHP does not creep
      back into views later.

## G5 — Cloudflare media ownership

Target: all website content media is delivered through the approved Cloudflare
media origin/CDN instead of being served from Laravel `public/` or third-party
hotlinks.

“Content media” includes logos, photographs, illustrations, thumbnails, gallery
media, article media, Program/Values media, audio, and video. Vite-built CSS/JS
and other non-media application artifacts are not part of this migration.

- [ ] Inventory every content-media reference in Blade, CSS, JS, database seed,
      admin-managed content, and configuration.
- [ ] Every required media object exists in the approved Cloudflare storage/origin.
- [ ] Application media URLs resolve through the approved Cloudflare domain.
- [ ] No content media still depends on `/public` paths.
- [ ] No content media still hotlinks Unsplash or another third-party origin unless
      the owner explicitly records an exception.
- [ ] CSP `img-src`, `media-src`, and related directives authorize only the exact
      Cloudflare origins that are actually required.
- [ ] Cache headers and immutable/versioned URL behavior are verified where
      appropriate.
- [ ] Missing-media/failure behavior does not collapse layout or expose internal
      storage paths.
- [ ] Upload/admin flows, if they own media, store or publish to Cloudflare rather
      than silently recreating local-public ownership.

## G6 — Login and data security gate

This gate complements `blueprints/2026-08-02-auth-account-access.md`; it does not
replace its detailed contract.

Authentication and authorization:

- [ ] Admin, guru, murid, guest, inactive, and wrong-role paths are tested against
      every protected area they can reach.
- [ ] Google callback accepts only the intended pre-provisioned account contract.
- [ ] Student-ID/password login rejects unknown ID, wrong password, inactive user,
      malformed ID, and wrong role without leaking account existence.
- [ ] Login throttling and expiry behavior are proven.
- [ ] Session regeneration prevents fixation after login.
- [ ] Single-session/session-version invalidation works as designed.
- [ ] Logout invalidates the active authenticated session.
- [ ] CSRF protection covers state-changing web actions.
- [ ] Direct URL access cannot bypass role middleware/policies.

Data protection:

- [ ] Passwords are stored only as Laravel-supported hashes and never returned to
      views, JSON, logs, audit payloads, or browser state.
- [ ] OAuth credentials, API keys, session IDs, cookies, reset material, and
      secrets never appear in HTML, JS, logs, or committed source.
- [ ] IDOR/BOLA checks prove one role/account cannot read or mutate another
      account’s protected data by changing an identifier.
- [ ] Validation and authorization happen server-side for account create/update,
      enable/disable, password reset/change, and other sensitive mutations.
- [ ] Mass-assignment boundaries are explicit for sensitive models.
- [ ] XSS-sensitive output is escaped or deliberately sanitized before rendering.
- [ ] SQL/user-controlled query inputs use framework parameterization and allowed
      filters rather than raw concatenation.
- [ ] Security/audit logs contain useful events without raw secrets or unnecessary
      sensitive identifiers.
- [ ] Disabled/deleted-account behavior does not leave a usable stale session.
- [ ] Database backup/restore and destructive migration rollback expectations are
      documented before production data is treated as recoverable.

Security proof:

- [ ] Focused auth/security test suites pass.
- [ ] Full `php artisan test` remains green after security work.
- [ ] Manual negative-path testing is recorded for login, role isolation, IDOR,
      session invalidation, and sensitive mutations.
- [ ] Production security headers/CSP are inspected in the deployed environment.

## G7 — Final release gate

- [ ] G0 through G6 are green with evidence linked to a source/deploy SHA.
- [ ] No unresolved regression is relabeled as “cosmetic” without owner decision.
- [ ] Final Chromium + Safari smoke pass is recorded after deployment.
- [ ] Final ID/EN/AR smoke pass is recorded after deployment.
- [ ] Login/data negative-path smoke pass is recorded after deployment.

## Deferred surfaces

The following are intentionally not part of the active visual implementation
until the owner opens them as separate scopes:

- [ ] Gallery section visual/refactor work — `DEFERRED`.
- [ ] Article section visual/refactor work — `DEFERRED`.

They may still be included in global security, media, or regression inventories
when those cross-cutting gates require repository-wide proof.
