# UI/UX Engineering — Current State and Progress Ledger

Status: ACTIVE_WITH_KNOWN_GAPS
Updated: 2026-08-02
Repository: `Asyraf2003/schoolai`
Hero campaign-link batch base: `867954734fa91b8301a07bbadf23e160087c28de`

Commit publication is source evidence only. It does not prove rendering,
browser parity, accessibility, performance, or lifecycle behavior.

## Active production batch

Blueprint: `blueprints/2026-08-02-homepage-hero-text-interactions.md`

- ID: `HOME-HERO-TEXT-001`
- State: `IMPLEMENTING`
- Surface: homepage hero only
- Protected: About, Testimonial, navbar, other homepage sections, unrelated
  routes, database schema, admin ordering, and media upload behavior

## Current FACT and DECISION

- Active admin/article placements are authoritative when available.
- Translation slides are used only when no managed slide survives.
- Final index zero is the primary slide; the public order matches admin order.
- PPDB state remains owned only by `PpdbSetting`.
- PPDB open turns the managed primary video into a complete localized campaign:
  PPDB eyebrow, heading, description, and registration CTA.
- The PPDB heading, description, and CTA are three valid interactive targets
  that all use the same normalized `publicRegistrationUrl()` destination.
- No open-campaign target routes to the underlying article.
- PPDB closed restores the original article eyebrow, linked heading,
  description, and normal CTA on the next server render.
- Media, poster, focal point, overlay, ordering, and article records remain
  unchanged.
- Automatic title glow continues on every active-slide event across all six
  tiers; ID/EN use LTR grapheme motion and AR stays one shaped RTL run.
- Navbar, About, and Testimonial remain untouched.

## Published campaign files

- `app/Support/HomeHeroPresentation.php`
- `resources/views/home/sections/hero.blade.php`
- `resources/css/pages/welcome-hero/text-interactions.css`
- `lang/id/runtime.php`
- `lang/en/runtime.php`
- `lang/ar/runtime.php`
- `tests/Feature/HomeHeroInteractionTest.php`
- active blueprint and this ledger

## Proof status

| Gate | Status | Evidence |
|---|---|---|
| GitHub publication | `PASS` | direct fast-forward commits on `main` |
| Source ownership inspection | `PASS` | one presentation owner and one Blade branch |
| Open PPDB source contract | `PASS` | heading, description, and CTA share `ppdb_url` |
| Closed PPDB source contract | `PASS` | article link/copy returns without PPDB links |
| Focused PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | owner must pull and run locally |
| Production Vite build | `BLOCKED_BY_MISSING_EVIDENCE` | CSS changed; local build required |
| ID/EN/AR runtime layout | `BLOCKED_BY_MISSING_EVIDENCE` | visual proof required |
| Chromium/WebKit interaction | `BLOCKED_BY_MISSING_EVIDENCE` | rerun after pull |
| Full PHP suite | `KNOWN_BASELINE_FAIL` | stale protected About test |
| Structure checker | `KNOWN_BASELINE_FAIL` | Vision/Mission and equivalence debt |

## Open GAP

### `HERO-PPDB-CAMPAIGN-PROOF-001`

After pulling, prove PPDB open and closed:

- open state retains admin placement `01` media;
- eyebrow/heading/description/CTA are campaign copy in ID, EN, and AR;
- heading, description, and CTA all navigate to the exact same registration URL;
- no open-campaign target navigates to the article;
- closed state restores article heading link, copy, and CTA;
- keyboard focus is visible on heading, description, and CTA;
- no layout overflow or collision occurs at 360, 640, 768, 1024, 1180, 1181,
  1280, and 1536;
- reduced motion changes only decoration, not content or navigation.

### `TEST-GAP-001`

`HomeAboutReelTest` still expects the intentionally disabled protected About
surface. This hero patch does not alter that unrelated owner.

### `STRUCTURE-GAP-001`

The checker still has known Vision/Mission size and source-equivalence debt.
Do not conceal it inside the hero scope.

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
| H00 hero presentation + motion | `PUBLISHED_AWAITING_LOCAL_PROOF` | unified PPDB links published |
| R00 PageSpeed/CWV acceptance | `BLOCKED_BY_MISSING_EVIDENCE` | lab and field evidence absent |

## STATUS

- Unified PPDB campaign-link source: `PUBLISHED` to `main`.
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

Then open `/` with PPDB active and inactive in ID, EN, and AR. Verify the three
open-state targets share one registration URL and the closed state restores the
complete linked article presentation.
