# UI/UX Engineering — Current State and Progress Ledger

Status: ACTIVE_WITH_FAILED_GATES
Updated: 2026-08-02
Repository: `Asyraf2003/schoolai`
Audited source main: `684328dd3c2f4eb962501aee9d4486cdc6d9ac6c`

Commit publication is source evidence only. It does not prove rendering,
browser parity, accessibility, performance, or lifecycle behavior.

## Active production batch

Blueprint: `blueprints/2026-08-02-homepage-hero-text-interactions.md`

- ID: `HOME-HERO-TEXT-001`
- State: `IMPLEMENTING`
- Surface: homepage hero only
- Owner decision: accepted in the 2026-08-02 implementation brief
- Forbidden/protected: About, Testimonial, navbar behavior/visuals, other
  homepage sections, unrelated admin/public routes, and meta ownership

## FACT and DECISION

- Translation fallback owns the required primary `flower.mp4` video.
- Database/article injection previously displaced that video and exposed its
  URL only through a generic CTA contract.
- The primary fallback video is now retained at index zero; database/article
  slides follow it.
- One presentation owner adds explicit primary, PPDB, PPDB URL/label, and
  article-only title URL fields after slide normalization.
- PPDB visibility comes only from `PpdbSetting::isRegistrationOpen()` and its
  safe `publicRegistrationUrl()` result.
- Blade emits article title anchors in initial HTML inside the existing one
  `h1`/subsequent `h2` hierarchy. JavaScript no longer infers title links from
  generic CTA URLs.
- Navbar roll remains untouched. Hero owns a small compatible PPDB roll module.
- Latin glow uses grapheme segmentation and bounded stagger. Arabic stays one
  shaped run with a mirrored gradient sweep.
- Decorative layers are `aria-hidden`; static semantic text, focus, links,
  reduced motion, and no-JS output remain usable.
- No dependency, request, WebGL/canvas, animation loop, or media policy changed.

## PROOF recorded on 2026-08-02

| Gate | Status | Evidence |
|---|---|---|
| Focused hero/navbar/hotfix tests | `PASS` | 15 tests, 262 assertions |
| Production Vite build | `PASS` | Vite 8.1.3, 86 modules |
| Diff whitespace | `PASS` | `git diff --check` clean |
| Full PHP suite | `FAIL` | 146/147; stale protected About test |
| Source structure | `FAIL` | baseline Vision/Mission sizes + Hero checksum |
| Chromium matrix | `PASS` | 50 state/locale/width cases |
| Chromium touch/reduced | `PASS` | single-tap navigation + static fallback |
| WebKitGTK matrix | `PASS` | 48 state/locale/width cases |
| WebKit touch/reduced | `BLOCKED_BY_MISSING_EVIDENCE` | driver lacks emulation |
| Real Safari/performance/field CWV | `BLOCKED_BY_MISSING_EVIDENCE` | not run |

The browser matrix covers PPDB open/closed at 360, 640, 768, 1024, 1180,
1181, 1280, and 1536 for ID, EN, and AR. It checks the first video, article
heading/link, viewport containment, horizontal overflow, CTA/arrow separation,
focus, motion count, and console errors. Representative and mid-motion captures
were inspected. WebKit evidence is WebKitGTK 2.52.5, not a Safari claim.

## Bounded accessibility/HTTPS hotfix

Blueprint: `docs/architecture/blueprints/2026-08-02-accessibility-https-hardening.md`

- The unsupported paragraph `aria-label` was replaced by one complete `.sr-only`
  text node while animated visual lines remain decorative.
- Production HTTP returned `200`; HTTPS returned `200` with working HSTS.
- `public/.htaccess` now redirects only the production host to canonical HTTPS,
  preserving local and staging hosts.
- Focused source-contract tests were added.
- COOP, Trusted Types, CSP legacy fallback, and hero video diagnosis remain
  documented gaps rather than unproved enforcement changes.

## Open GAP

### `TEST-GAP-001`

`HomeAboutReelTest` expects the intentionally disabled About surface. The hero
batch does not alter that protected surface or falsify the result.

### `STRUCTURE-GAP-001`

The official checker still reports three oversized Vision/Mission files and a
stale Hero source checksum. Those baseline owners were not changed here.

### `HERO-WEBKIT-INPUT-GAP-001`

WebKit touch and reduced-motion input need a capable driver/device. Layout,
pointer, focus, direction, and animation were proven in WebKitGTK.

### `A11Y-HTTPS-PROOF-GAP-001`

The hotfix is published but still requires focused/full tests, build/structure
proof, deployed HTTP `301`, retained HTTPS HSTS/CSP, a repeated accessibility
audit, and separate HTTPS byte-range proof for the reported hero MP4.

### `BROWSER-PERF-GAP-001`

Real Safari, Lighthouse/PageSpeed, zoom, orientation, short-height, BFCache,
long-task, transfer, and field CWV evidence remains incomplete.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G00 governance hardening | `PASS` | active architecture contracts |
| G01 execution foundation | `PASS` | migration, lab, and handoff rules |
| B00 current baseline | `COMPLETE_WITH_KNOWN_GAPS` | retained baseline evidence |
| N00 navbar media mapping | `PUBLISHED_NOT_RUNTIME_PROVEN` | local media paths committed |
| N01 navbar text-overlay removal | `PUBLISHED_NOT_RUNTIME_PROVEN` | overlay markup removed |
| N02 unified navbar 3D roll | `IMPLEMENTING` | source published; proof missing |
| A00 accessibility/HTTPS hotfix | `PUBLISHED_NOT_RUNTIME_PROVEN` | deploy proof missing |
| H00 hero presentation + motion | `FAIL` | implementation proven; gates red |
| E00 WebGL engine ADR | `BLOCKED_BY_MISSING_EVIDENCE` | no accepted WebGL scene |
| R00 PageSpeed/CWV acceptance | `BLOCKED_BY_MISSING_EVIDENCE` | lab and field evidence absent |

## STATUS

- Implementation source: complete locally.
- Release/push: `FAIL`; withheld because mandatory gates are not all green.
- Published accessibility/HTTPS hotfix: retained from current upstream.
- About/Testimonial/navbar protection: preserved by changed-file inspection.
- New graphics/runtime dependency: none.

## NEXT VALID STEP

Execution channel: `owner/local terminal`.

Resolve or formally retire `TEST-GAP-001` and `STRUCTURE-GAP-001` in separate
owner-accepted scopes. Then rerun:

```bash
git diff --check
npm run check:structure
npm run build
php artisan test --filter=HomeAccessibilityAndHttpsDeploymentTest
php artisan test --filter=PublicUnifiedNavigationTest
php artisan test
```

Obtain WebKit touch/reduced-motion evidence before pushing this hero batch.
Separately complete the published hotfix's deployed HTTPS/accessibility proof.
