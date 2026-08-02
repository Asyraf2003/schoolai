# UI/UX Engineering — Current State and Progress Ledger

Status: ACTIVE_WITH_KNOWN_GAPS
Updated: 2026-08-02
Repository: `Asyraf2003/schoolai`
Hero correction published through parent: `db177e3fb12c381e458cedf7296bf3421ce8be6f`

Commit publication is source evidence only. It does not prove rendering,
browser parity, accessibility, performance, or lifecycle behavior.

## Active production batch

Blueprint: `blueprints/2026-08-02-homepage-hero-text-interactions.md`

- ID: `HOME-HERO-TEXT-001`
- State: `IMPLEMENTING`
- Surface: homepage hero only
- Protected: About, Testimonial, navbar, other homepage sections, unrelated
  routes, database schema, and media upload behavior

## Corrected FACT and DECISION

- Active admin/article placements are authoritative when available.
- The prior implementation incorrectly prepended the translation demo video to
  managed placements, creating an unmanaged extra slide absent from admin.
- Managed slides now replace translation fallback as one complete list.
- Translation slides remain only when no managed/article/legacy slide survives.
- Final index zero is the primary slide. The owner's current placement `01` is
  therefore the uploaded article video shown first on the public homepage.
- PPDB state remains owned by `PpdbSetting` and applies only when final index zero
  renders as video.
- Article title links remain server-rendered and article-only.
- Video posters are retained while media hydrates or playback is delayed/blocked.
- Every active-slide event triggers one delayed title sweep on all six tiers;
  pointer hover and focus remain optional replay triggers.
- ID/EN keep grapheme-based LTR motion. AR remains one shaped RTL run.
- Reduced motion keeps semantic/static output.
- Navbar source and behavior remain unchanged.

## Published correction files

- `app/Providers/Concerns/InjectsDatabaseHero.php`
- `resources/js/pages/welcome-hero/title-glow.js`
- `resources/js/pages/welcome-hero/slider-media.js`
- `tests/Feature/HeroArticlePlacementTest.php`
- `tests/Feature/HeroDatabaseFallbackTest.php`
- `tests/Feature/Admin/HeroSlideAdminTest.php`
- `tests/Feature/HomeHeroInteractionTest.php`
- active blueprint and this ledger

## Proof status

| Gate | Status | Evidence |
|---|---|---|
| GitHub publication | `PASS` | direct fast-forward commits on `main` |
| Source ownership inspection | `PASS` | admin/fallback concatenation root cause removed |
| Focused PHP tests after correction | `BLOCKED_BY_MISSING_EVIDENCE` | owner must run locally |
| Production Vite build after correction | `BLOCKED_BY_MISSING_EVIDENCE` | owner must run locally |
| Runtime visual/admin parity | `BLOCKED_BY_MISSING_EVIDENCE` | owner screenshot required |
| Chromium transition/glow | `BLOCKED_BY_MISSING_EVIDENCE` | rerun after pull |
| WebKit/Safari transition/glow | `BLOCKED_BY_MISSING_EVIDENCE` | rerun after pull |
| Full PHP suite | `KNOWN_BASELINE_FAIL` | stale protected About test |
| Structure checker | `KNOWN_BASELINE_FAIL` | Vision/Mission and equivalence debt |

## Bounded accessibility/HTTPS hotfix

The earlier accessibility and canonical-HTTPS source correction remains
published. Deployment proof is still required for HTTP `301`, HTTPS HSTS/CSP,
repeated accessibility audit, and hero MP4 byte-range behavior.

## Open GAP

### `HERO-CORRECTION-PROOF-001`

Pull current `main`, run focused tests/build, then prove:

- admin placement `01` equals public slide `01`;
- no translation demo slide is inserted while managed placements exist;
- PPDB open/closed presentation applies to the managed primary video;
- poster remains visible while video loads or autoplay is blocked;
- glow runs on initial load and every autoplay/arrow/keyboard/swipe activation;
- reduced motion remains static;
- ID/EN/AR remain correct.

### `TEST-GAP-001`

`HomeAboutReelTest` still expects the intentionally disabled protected About
surface. This hero correction does not alter that unrelated owner.

### `STRUCTURE-GAP-001`

The checker still has known Vision/Mission size and source-equivalence debt.
Do not conceal it inside the hero correction.

### `BROWSER-PERF-GAP-001`

Real Safari, complete WebKit input/reduced-motion, Lighthouse/PageSpeed, zoom,
orientation, short-height, BFCache, long-task, transfer, and field CWV remain
incomplete.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G00 governance hardening | `PASS` | active architecture contracts |
| B00 current baseline | `COMPLETE_WITH_KNOWN_GAPS` | retained baseline evidence |
| N02 unified navbar 3D roll | `PUBLISHED_NOT_FULLY_PROVEN` | navbar untouched here |
| A00 accessibility/HTTPS hotfix | `PUBLISHED_NOT_DEPLOYED_PROVEN` | deployment proof missing |
| H00 hero presentation + motion | `PUBLISHED_AWAITING_LOCAL_PROOF` | corrected admin ownership |
| R00 PageSpeed/CWV acceptance | `BLOCKED_BY_MISSING_EVIDENCE` | lab and field evidence absent |

## STATUS

- Corrected hero source: `PUBLISHED` to `main`.
- Runtime completion: `BLOCKED_BY_MISSING_EVIDENCE` until local proof.
- About/Testimonial/navbar protection: preserved.
- New dependency or graphics runtime: none.

## NEXT VALID STEP

Execution channel: `owner/local terminal`.

```bash
git pull --ff-only origin main
php artisan test --filter='HomeHeroInteractionTest|HeroArticlePlacementTest|HeroDatabaseFallbackTest|HeroSlideAdminTest'
npm run build
```

Then reload `/` and `/admin/hero` without production deployment and compare the
first placement, poster behavior, PPDB state, and automatic glow transitions.
