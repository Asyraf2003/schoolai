# Homepage Accessibility and HTTPS Hardening

Blueprint ID: `HOME-A11Y-HTTPS-001`
Status: `IMPLEMENTING`
Owner: Asyraf
Date: 2026-08-02
Source main SHA: `c698348e38eefc1104ee39a330fc2c328d7c769d`
Target channel: Web AI with explicit direct-write permission to `main`

## Owner goal

Remove the proven homepage accessibility-tree failure, force production HTTP
traffic to the canonical HTTPS origin, and preserve uncertain security findings
as explicit gaps instead of enabling risky headers without runtime proof.

## FACT

- The editorial heading partial rendered `aria-label` on a semantic `<p>` while
  hiding all visual child lines from assistive technology.
- Lighthouse reported that paragraph as using a non-permitted ARIA attribute.
- Production proof on 2026-08-02 showed:
  - `http://almustaqbal.sch.id/` returned `200 OK`;
  - `https://almustaqbal.sch.id/` returned `200 OK`;
  - HTTPS already returned `Strict-Transport-Security: max-age=31536000`;
  - both origins returned the nonce-based CSP and existing security headers.
- `public/.htaccess` had no HTTP-to-HTTPS redirect.
- `AddSecurityHeaders` already owns CSP, HSTS, nosniff, referrer policy,
  permissions policy, and frame denial.

## Scope

### In

- `resources/views/home/partials/editorial-section-heading.blade.php`
- `public/.htaccess`
- focused source-contract tests
- this durable blueprint/note

### Out

- About and Testimonial
- navbar behavior or visuals
- OAuth behavior
- production media replacement
- site-wide CSP redesign
- WebGL, responsive layout, or typography redesign

## Decision

### Editorial description

Keep the animated split lines decorative with `aria-hidden="true"`. Remove the
unsupported paragraph `aria-label` and add one complete `.sr-only` text node
inside the paragraph. This preserves native paragraph semantics and prevents
screen-reader duplication.

### HTTPS redirect

Add Apache rewrite rules before the Laravel front-controller rules:

- canonicalize `www.almustaqbal.sch.id` to `https://almustaqbal.sch.id`;
- redirect the production non-www host when `%{HTTPS} !=on`;
- restrict both rules to the production host so local and staging hosts remain
  usable.

The application HSTS behavior remains unchanged because HTTPS proof already
shows it is working.

## Deferred gaps

### `SEC-COOP-001`

`Cross-Origin-Opener-Policy` is absent. Do not enforce `same-origin` until Google
OAuth popup/redirect behavior, third-party embeds, and opener dependencies are
proved. Candidate policies must be tested rather than selected from Lighthouse
severity text alone.

### `SEC-TRUSTED-TYPES-001`

Trusted Types is absent. Before enforcement, inventory application and
third-party DOM XSS sinks, introduce a report-only phase where supported, and
prove that navigation, editor, embeds, and admin interactions remain functional.

### `SEC-CSP-LEGACY-001`

Lighthouse suggested adding `'unsafe-inline'` to `script-src` for old-browser
compatibility. The current nonce policy remains unchanged because weakening the
fallback is not required for supported Chromium/WebKit targets and the finding
is unscored.

### `MEDIA-HERO-VIDEO-001`

Lighthouse logged `ERR_CONNECTION_FAILED` for one hero MP4 while auditing the
HTTP origin. After the HTTPS redirect is deployed, prove the exact HTTPS media
URL with normal and byte-range requests before changing media source, storage,
or playback code.

### `BASELINE-FEATURES-001`

The Baseline Features list is informational. Existing capability fallbacks must
be reviewed only when a supported Chromium/WebKit runtime reproduces a defect.

## Automated proof required

```bash
git diff --check
npm run check:structure
npm run build
php artisan test --filter=HomeAccessibilityAndHttpsDeploymentTest
php artisan test
```

## Production proof required after deployment

```bash
curl -sS -I http://almustaqbal.sch.id/
curl -sS -I https://almustaqbal.sch.id/
```

Acceptance:

- HTTP returns `301` with `Location: https://almustaqbal.sch.id/`;
- HTTPS returns `200` and retains HSTS plus CSP;
- Lighthouse no longer reports the paragraph ARIA violation;
- no redirect loop occurs;
- the hero video is retested separately over HTTPS.

## Status

Source publication may be completed by this batch. Automated, deployed-server,
Lighthouse, Chromium, and WebKit proof remain `BLOCKED_BY_MISSING_EVIDENCE`
until the commands and runtime checks above are executed.
