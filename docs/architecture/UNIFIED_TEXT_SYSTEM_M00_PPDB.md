# Unified Text System — M00 PPDB Evidence

Status: PASS
Scope: public `/ppdb` page only
Purpose: factual baseline for later M07 implementation. M00 performs discovery/documentation only and makes no typography or layout changes.

## 1. Route, controller, and render tree

- `GET /ppdb` -> `PpdbPageController`.
- Controller: `app/Http/Controllers/PpdbPageController.php`.
- Main view: `resources/views/pages/ppdb.blade.php`.
- Controller supplies current `PpdbSetting` and ordered `PpdbShowcaseItem` rows.
- `PpdbSetting` controls registration/information URLs and open/closed state. It does not own typography.

Rendered PPDB sections:

1. Hero.
2. DB-backed showcase/journey when audience items exist.
3. Admission steps.
4. Programs.
5. Documents and timeline.
6. FAQ.
7. Final CTA.
8. Closed-registration modal.

The public layout also loads PPDB journey CSS/JS.

## 2. Content-source contract

### Locale-backed page shell

`__('pages.ppdb')` supplies Hero, showcase shell, steps, programs, documents, timeline, FAQ, and final CTA copy for ID/EN/AR.

### Runtime translation labels

Runtime keys supply registration/guide/final CTA labels, closed-modal copy, audience labels, and showcase fallback UI text.

### DB-backed showcase

`PpdbShowcaseItem` supplies localized title/description and media state. DB values are content only. Render location determines semantic role.

### PPDB settings

`PpdbSetting` decides action destination/state only. No font family, font size, CSS class, breakpoint styling, or typography role is stored in DB.

## 3. Semantic role inventory

### Hero

| Render location | Selector | Source | Role |
|---|---|---|---|
| Page heading | `#ppdb-title` | lang | `page-title` |
| Hero subtitle | `.public-hero__subtitle` | lang | `subtitle` |
| Registration button | `.btn--ppdb-register` | runtime lang | `action` |
| Guide button | `.btn--ppdb-guide` | runtime lang | `action` |
| Hero note | `.public-note` | lang | `description` |
| Statistic value | `.public-stat-row strong` | lang | `display` / `meta` pending final token decision |
| Statistic label | `.public-stat-row span` | lang | `label` |
| Mini-card title | `.ppdb-mini-card h2` | lang | `component-title` |
| Mini-card text | `.ppdb-mini-card p` | lang | `description` |

### Showcase / journey

| Render location | Selector | Source | Role |
|---|---|---|---|
| Audience tab | `.ppdb-liftoff__tab` | runtime lang | `action` |
| Showcase heading | `.ppdb-liftoff__top h2` | lang | `section-title` |
| Showcase subtitle | `.ppdb-liftoff__top p` | lang | `subtitle` |
| Step number | `.ppdb-liftoff-step` | render index | `meta` |
| DB card title | `.ppdb-liftoff-card__text h3` | DB | `component-title` |
| DB card description | `.ppdb-liftoff-card__text p` | DB | `description` |
| Showcase CTA note | `.ppdb-liftoff__cta p` | lang | `description` |
| Showcase CTA link | `.ppdb-liftoff__cta .btn` | lang | `action` |

Fallback visual copies remain component/UI duplicates and are not a new semantic role.

### Steps / programs / information / FAQ / final CTA

| Render location | Selector | Role |
|---|---|---|
| Admission section heading | `#alur-ppdb .public-section-head h2` | `section-title` |
| Admission section subtitle | `#alur-ppdb .public-section-head p` | `subtitle` |
| Admission step number | `.public-step-card__number` | `meta` |
| Admission step title | `.public-step-card h3` | `component-title` |
| Admission step description | `.public-step-card p` | `description` |
| Program age | `.program-public-card__age` | `meta` / `label` |
| Program title | `.program-public-card h3` | `component-title` |
| Program description | `.program-public-card > p:not(.program-public-card__age)` | `description` |
| Document item | `.document-list li` | `body` / `label` |
| Timeline date | `.timeline-card > span` | `meta` |
| Timeline title | `.timeline-card h3` | `component-title` |
| Timeline description | `.timeline-card p` | `description` |
| FAQ question | `.faq-card summary` | `action` / `component-title` |
| FAQ answer | `.faq-card p` | `description` |
| Final CTA heading | `.public-final-cta__box h2` | `section-title` |
| Final CTA copy | `.public-final-cta__box p` | `description` |
| Final CTA button | `.public-final-cta__box .btn` | `action` |
| Closed modal heading | `.ppdb-closed-modal__panel h2` | `component-title` |
| Closed modal copy | `.ppdb-closed-modal__panel p` | `description` |
| Closed modal close | `.ppdb-closed-modal__close` | `action` |

FAQ proves again that role cannot be inferred from tag name alone: `summary` is interactive wording and a local title at once.

## 4. Accessibility / JS behavior

`.ppdb-journey-status` is `aria-live="polite"`, visually clipped to 1px, and updated by journey JS from active step number/title.

Classification:

- accessibility-relevant dynamic status;
- not visible typography;
- content derived from existing rendered step content;
- JS does not set font sizing.

Journey JS may toggle panel visibility, inert state, opacity, transforms, blur, pointer state, and CTA visibility. These are interaction/layout concerns, not typography ownership.

## 5. Runtime proof

Read-only Brave/CDP proof completed against the real `/ppdb` route and real language-switch forms.

Required matrix completed:

- ID: 1440 / 768 / 390, `dir=ltr`.
- EN: 1440 / 768 / 390, `dir=ltr`.
- AR: 1440 / 768 / 390, `dir=rtl`.

All representative selectors were present in the current runtime.

### Current state facts

- `showcasePresent=true` at all tested locales/widths.
- `journeyNative=true` at 1440 and `false` at 768/390. This matches the existing desktop enhancement threshold (`min-width: 901px`) and is behavior evidence, not a typography bug.
- Registration currently resolves directly to `https://forms.gle/1huqPo24Et6pgUNh6` with `_blank`.
- Guide currently resolves directly to `https://almustaqbal.sch.id/ppdb`.
- Closed modal exists in DOM but is hidden in the current open-registration state.
- `.document-list li` was present but measured with `opacity:0` because reveal animation had not intersected during the audit. It is not a missing text node.

## 6. Winning typography baseline

### ID / EN parity

ID and EN resolve to the same numeric typography for the sampled roles at equivalent widths.

Representative values:

- page title: 60.48px at 1440, 33.6px at 768, 50.7px at 390;
- Hero subtitle: 19.2px / 18.304px / 17.17px;
- registration/guide actions: 15.68px;
- showcase heading: 57.6px / 33.792px / 32px;
- showcase card title: 46.08px / 28.8px / 28.8px;
- admission step title: 18.72px;
- ordinary step/program/timeline/FAQ descriptions: 16px;
- final CTA heading: 43.2px / 28.8px / 28.8px.

Important baseline anomaly:

- public page title sizing is non-monotonic: mobile 390 resolves to 50.7px while tablet 768 resolves to 33.6px. M00 records this only; later semantic-token implementation decides the intended scale.

### Arabic adapter baseline

Arabic correctly resolves `dir=rtl` and uses Cairo/Lateef, but the current selector cascade is inconsistent by role.

Observed examples:

- page title: Cairo, same numeric size as ID/EN;
- register/guide/final actions: generally Cairo 16px;
- Hero subtitle, Hero note, mini-card description, admission step descriptions, program descriptions, document items, timeline descriptions, FAQ answers, and final CTA description: Lateef 36px / 63px line-height;
- showcase heading/card title: Cairo but still inherits negative letter-spacing from PPDB component CSS (`-4.032px`, `-2.9952px`, responsive variants);
- showcase subtitle/card description stay much smaller than the generic Arabic prose scale because PPDB-specific selectors win (`17.28px` desktop, 16px smaller widths for showcase subtitle; 18.56px desktop, 16px smaller widths for card description);
- statistic label uses Lateef at only 14.72px;
- timeline date uses Lateef 16px;
- closed-modal action currently resolves to Lateef 16px rather than the usual Cairo action family.

These are M07 implementation targets/evidence, not reasons to alter code during M00.

## 7. CSS ownership evidence

Current PPDB typography can be won by several layers:

1. shared public/welcome CSS;
2. PPDB page-scoped inline CSS;
3. `resources/css/pages/ppdb-journey.css`;
4. Arabic typography adapter.

Source declarations alone therefore do not prove the winner. The computed-style matrix above is the authoritative baseline.

## 8. M00 conclusion

FACT:

- source paths, content origins, semantic roles, locale behavior, responsive behavior, current state, and representative computed typography are proven for `/ppdb`.

GAP:

- no material PPDB baseline gap remains for M00.

GOAL:

- preserve this evidence for M07 implementation.

IMPACT:

- none in runtime; documentation only.

DECISION:

- close `/ppdb` M00.

EXECUTION:

- documentation updated only.

PROOF:

- runtime ID/EN/AR at 1440/768/390 completed;
- showcase and action-state presence confirmed;
- computed typography recorded.

STATUS: PASS

NEXT VALID STEP:

- continue M00 with the remaining non-public surfaces: M08 admin desktop baseline, then M09 article canvas/editor baseline.
- do not repeat PPDB source discovery or runtime proof unless later code changes invalidate this baseline.
