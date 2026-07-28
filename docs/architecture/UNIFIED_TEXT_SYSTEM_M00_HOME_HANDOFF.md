# Unified Text System — M00 Homepage Handoff

Status: PASS
Scope: public homepage only
Purpose: compact continuation state for future sessions. Read this before doing any homepage text-system work. Do not repeat completed discovery/runtime work.

## Authoritative evidence

Primary historical evidence ledger:

- `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_HOME.md`

This handoff records the final homepage-M00 closure state and supersedes earlier pending lines in the historical ledger.

Homepage M00 is discovery/documentation only. No visual styling change, `data-text-role` implementation, shared text-system implementation, or legacy typography cleanup was performed.

## Completed evidence

Repository/source evidence completed:

- repository preflight PASS;
- homepage render tree mapped;
- Blade/lang/DB/presentation-PHP/JS content sources classified;
- initial semantic roles mapped;
- DB presentation separation confirmed;
- JS-created/populated visible text paths identified;
- 598 homepage/Arabic typography declaration hits recorded;
- homepage CSS import/cascade order recorded;
- Brave headless + CDP + Node built-in WebSocket runtime harness proven;
- real application language switch flow proven through `POST /bahasa/{locale}` and locale middleware.

Runtime evidence completed for all required public widths:

- EN/LTR: 1440 / 768 / 390;
- ID/LTR: 1440 / 768 / 390;
- AR/RTL: 1440 / 768 / 390.

All representative selectors were present in the valid ID/AR parity run.

## Locale switch proof

Public locale flow:

- route: `POST /bahasa/{locale}`;
- accepted locales: `id`, `en`, `ar`;
- session key: `locale`;
- cookie: `site_locale`;
- middleware resolution order: session -> cookie -> `config('app.locale')`;
- selected locale applied with `App::setLocale()`;
- navbar language modal renders real CSRF-protected POST forms.

A prior cookie-preload CDP attempt was invalid because all six runs remained EN/LTR with missing selectors. That failed batch is evidence about the harness only and must never be used for locale parity conclusions.

The corrected run switched locale through the real application form and waited for the requested rendered `<html lang>` plus a known homepage selector before measuring.

## Final homepage role inventory

| Surface group | Representative selector / element | Content source | Canonical role | Locale | Responsive |
|---|---|---|---|---|---|
| Navbar navigation | `.navbar__menu .nav-link` | locale/presentation PHP | `action` | ID/EN/AR | 390/768/1440 |
| Navbar mega eyebrow | `.nav-mega__eyebrow` | presentation PHP | `label` | ID/EN/AR | public |
| Navbar mega title | `.nav-mega__title` | presentation PHP | `component-title` | ID/EN/AR | public |
| Navbar mega description | `.nav-mega__description` | presentation PHP | `description` | ID/EN/AR | public |
| Language modal title | `.language-modal__title` | presentation PHP | `component-title` | ID/EN/AR | public |
| Language option | `.language-modal__option` / label | presentation PHP | `action` | ID/EN/AR | public |
| Hero eyebrow | `.hero-cinema__eyebrow` | locale-backed Hero presentation | `label` | ID/EN/AR | public |
| Hero title | `.hero-cinema__title` | locale-backed Hero presentation | `display` | ID/EN/AR | 390/768/1440 |
| Hero description | `.hero-cinema__description` | locale-backed Hero presentation | `description` | ID/EN/AR | 390/768/1440 |
| Hero CTA when present | `.hero-cinema__cta` | locale-backed Hero presentation | `action` | ID/EN/AR | public |
| About headline | `.about-reel__headline` | lang | `section-title` | ID/EN/AR | 390/768/1440 |
| About description | `.about-reel__description` | lang | `description` | ID/EN/AR | 390/768/1440 |
| Section heading | `.section-title` | locale presentation arrays | `section-title` | ID/EN/AR | public |
| Section lead | `.section-subtitle` | locale presentation arrays | `subtitle` | ID/EN/AR | public |
| Vision/Mission local heading | `.visi-card__title`, `.misi-panel__head h3`, `.misi-card__title` | locale presentation arrays | `component-title` | ID/EN/AR | public |
| Mission index | `.misi-card__number` | locale presentation arrays | `meta` | ID/EN/AR | public |
| Mission description | `.misi-card__text` | locale presentation arrays | `description` | ID/EN/AR | public |
| School value code | `.nilai-card__code` | locale presentation arrays | `meta` | ID/EN/AR | public |
| School value title | `.nilai-card__title` | locale presentation arrays | `component-title` | ID/EN/AR | public |
| School value description | `.nilai-card__description` | locale presentation arrays | `description` | ID/EN/AR | public |
| Program chip/label | program chip / `.program-card__label` | locale presentation arrays | `label` | ID/EN/AR | public |
| Program code | `.program-card__code` | locale presentation arrays | `meta` | ID/EN/AR | public |
| Program title | `.program-card__title` | locale presentation arrays | `component-title` | ID/EN/AR | 390/768/1440 |
| Program description | program description nodes | locale presentation arrays | `description` | ID/EN/AR | public |
| Gallery section heading | gallery `.section-title` | locale section shell | `section-title` | ID/EN/AR | public |
| Gallery card title | `.galeri-story-card__content h3` | DB `GalleryItem` or locale fallback | `component-title` | ID/EN/AR | 390/768/1440 |
| Gallery caption | gallery card caption | DB `GalleryItem` or fallback | `description` | ID/EN/AR | public |
| Gallery category/type/date | `.galeri-story-card__meta` | DB/runtime locale | `meta` | ID/EN/AR | 390/768/1440 |
| Gallery lightbox close | JS-created control | localized data attribute -> JS | `action` | ID/EN/AR | public |
| Article section heading | article `.section-title` | locale section shell | `section-title` | ID/EN/AR | public |
| Article title | `.artikel-digest__hero-title`, rail title | DB `Article` | `component-title` | ID/EN/AR | 390/768/1440 |
| Article description | `.artikel-digest__hero-description`, rail description | DB `Article` | `description` | ID/EN/AR | 390/768/1440 |
| Article category/date/read time | `.artikel-digest__meta` and equivalents | DB/runtime locale | `meta` | ID/EN/AR | 390/768/1440 |
| Article CTA | article links/section CTA | locale/runtime | `action` | ID/EN/AR | public |
| Footer brand description | `.footer-brand__description` | locale/footer presentation | `description` | ID/EN/AR | 390/768/1440 |
| Footer group heading | footer group heading | locale/footer presentation | `component-title` | ID/EN/AR | public |
| Footer links | `.footer-links a` | locale/footer presentation | `action` | ID/EN/AR | 390/768/1440 |
| Footer current year | `#currentYear` | JS-populated | `meta` | all | public |

Known role decisions retained for implementation review rather than blocking M00:

- some summary/lead nodes may resolve between `subtitle` and `description` during their migration batch based on exact rendered intent;
- compact identity text may resolve between `label` and `component-title` where the render context requires it;
- this does not leave a major visible homepage text group unclassified.

## Runtime parity findings

### ID vs EN

For the representative measured nodes, ID and EN resolved to the same typography values at corresponding viewports.

Examples at 1440px:

- Hero display: 43.2px / 760 / ui-rounded in both;
- About title: 82.08px / 850 / ui-rounded in both;
- section title: 47.6px / 800 / ui-rounded in both;
- Mission component title: 16.96px / 950 / ui-rounded in both;
- Program component title: 26.4px / 950 / ui-rounded in both;
- Gallery component title: 41.92px / 700 / ui-rounded in both;
- Article component title: 20.88px / 900 / system-ui in both.

The same parity pattern held at 768px and 390px.

### AR adapter proof

Correct runtime contract:

- `lang="ar"`;
- `dir="rtl"`;
- heading/UI roles resolve to Cairo;
- prose/description roles resolve to Lateef;
- letter-spacing resolves to `normal` for measured Arabic nodes.

Representative AR 1440px evidence:

- nav action: Cairo 16px / 700;
- Hero display: Cairo 43.2px / 700;
- Hero description: Lateef 36px / 400 / 63px line-height;
- About title: Cairo 95.04px / 760;
- About description: Lateef 23.2px / 400;
- section title: Cairo 47.6px / 700;
- section subtitle: Lateef 44px / 400 / 77px line-height;
- Mission component title: Cairo 16.8px / 700;
- Program title: Cairo 26.4px / 700;
- Gallery title: Cairo 41.92px / 700;
- Article title: Cairo 20.88px / 900;
- Article description: Lateef 36px / 400 / 63px line-height;
- footer description: Lateef 36px / 400 / 63px line-height;
- footer action: Cairo 16px / 700.

AR responsive behavior was also measured at 768px and 390px.

## Baseline problems proven for later migration

M00 records these as facts; it does not fix them.

1. Typography ownership is component-driven rather than semantic-role-driven.
2. `component-title` surfaces have unrelated current winning typography.
3. ID/EN parity is numerically stable, but that stability comes from existing component CSS rather than a shared role contract.
4. Arabic family adaptation works, but Arabic optical scale is highly uneven across semantic roles.
5. Arabic prose can approach or exceed nearby heading scale numerically. Examples include section subtitle 44px and several descriptions at 36px.
6. These Arabic values may be intentional optical compensation in some contexts, but equivalent hierarchy must be explicitly normalized/verified during M01-M04 rather than assumed.
7. Hero description on ID/EN falls to 11.84px at 390px while retaining weight 820, another baseline issue for implementation review.
8. No DB typography fields are needed; the problem is presentation ownership/cascade.

## Exclusions / non-rendered evidence

- `$stats` is passed by the homepage controller/build path but current About Blade does not visibly render statistics text; do not invent visible stats rows for this homepage baseline.
- `resources/js/pages/welcome/public-content.js` contains generic lightbox text mutation code, but no matching current homepage `data-public-lightbox` markup was proven; do not treat it as a current visible homepage group without new evidence.
- Hero CTA was absent in the measured active runtime slide; source contract still classifies it as `action` when rendered.

## Homepage M00 closure

Homepage M00 gate: PASS

Closure reason:

- major visible/accessibility-relevant homepage text groups are classified;
- content source ownership is mapped;
- representative winning typography is proven where cascade ambiguity matters;
- required public widths are covered;
- ID, EN, and AR are covered;
- AR RTL and family adapter behavior are proven;
- known exceptions are documented;
- no styling or product behavior was changed.

This PASS means only **M00 homepage surface** is complete. It does not mean the overall M00 milestone or Unified Text System project is complete.

## Do not repeat

Future sessions must not redo:

- homepage render/source discovery;
- homepage 598-declaration scan;
- homepage import-order audit;
- browser/CDP capability probes;
- EN/ID/AR homepage computed-style baseline at 390/768/1440;
- failed cookie-preload locale harness investigation.

Reopen homepage M00 only if a concrete missing visible text group or contradictory runtime fact is discovered.

## Next valid scope

Continue M00 baseline inventory on the next rendered public surface. Per locked route/migration coverage, the next surface is `/galeri` (public gallery page).

Do not start M01 implementation until the M00 baseline inventory for the required rendered surfaces is complete.
