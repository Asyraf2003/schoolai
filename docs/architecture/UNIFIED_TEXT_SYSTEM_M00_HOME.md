# Unified Text System — M00 Homepage Evidence Ledger

Status: IN_PROGRESS
Scope: public homepage only
Purpose: persist factual M00 evidence so future sessions continue from proof instead of repeating repository/runtime discovery
Update rule: append or revise this file after each completed M00 evidence batch
Implementation rule: M00 is discovery/documentation only; no visual styling changes, semantic-role implementation, or legacy typography cleanup

## 1. Authoritative baseline

Repository preflight: PASS
Preflight commit before M00 work: `e5f19131a481d979193588eaa03dcf97be16c599`
Local preflight sync proof: fast-forward `f15a034..e5f1913`

M00 homepage begins only after repository preflight PASS.

## 2. Homepage render tree

`resources/views/welcome.blade.php` renders:

1. shared site navbar;
2. Hero;
3. About / statistics reel;
4. Vision / Mission;
5. School Values;
6. Featured Programs;
7. Gallery;
8. Articles;
9. shared site footer.

Homepage entry view loads these Vite entry points:

- `resources/css/pages/welcome.css`
- `resources/css/pages/welcome-hero.css`
- `resources/css/pages/welcome-about-stats.css`
- `resources/css/arabic-typography.css`
- `resources/js/pages/welcome.js`
- `resources/js/pages/welcome-hero.js`
- `resources/js/pages/welcome-about-stats.js`

## 3. Content-source map

### 3.1 Base locale-backed homepage data

`app/Http/Controllers/Concerns/BuildsHomePage.php` builds homepage data from:

- `__('home')`
- `__('home_parity')`

with `array_replace_recursive()`.

This supplies at least:

- meta;
- Vision / Mission;
- School Values;
- Featured Programs;
- footer shell/copy;
- locale fallback content used by other builders.

### 3.2 Hero

`app/Http/Controllers/Concerns/BuildsHomeHero.php` normalizes locale-backed Hero data into a presentation contract.

Current source policy recorded by source comments:

- slides currently come from translation fallback data;
- a future database source must provide the same presentation keys before the render boundary;
- Hero media/link normalization happens in the builder;
- Hero presentation remains independent of typography ownership.

Visible Hero text includes:

- eyebrow;
- slide title;
- description;
- CTA label when present;
- accessibility labels/status.

Semantic target examples:

- Hero title -> `display`;
- Hero description -> `description`;
- Hero CTA -> `action`;
- Hero eyebrow -> `label` or `meta` depending on final role decision.

### 3.3 About reel

`resources/views/home/sections/about-statistics.blade.php` uses:

- locale keys under `home.about_stats_story.*` for headline and description;
- Hero media as the visual source/fallback.

Visible text:

- two-line About headline -> target `section-title`;
- About description -> target `description`.

### 3.4 Vision / Mission

`resources/views/home/sections/vision-mission.blade.php` renders presentation arrays from locale-backed homepage data.

Semantic observations:

- `.section-title` -> `section-title`;
- `.section-subtitle` -> `subtitle`;
- `.visi-card__title` -> `component-title`;
- `.visi-card__text` -> `body` / `description` according to final semantic use;
- `.misi-panel__head h3` -> `component-title`;
- `.misi-card__number` -> `meta`;
- `.misi-card__title` -> `component-title`;
- `.misi-card__text` -> `description`.

Important proof: `.misi-card__title` is a `span` inside a `button`, so semantic typography cannot be inferred from HTML heading tags alone.

### 3.5 School Values

`resources/views/home/sections/school-values.blade.php` renders locale-backed presentation arrays.

Semantic observations:

- section heading -> `section-title`;
- section subtitle -> `subtitle`;
- value code -> `meta` / `label`;
- `.nilai-card__title` -> `component-title`;
- `.nilai-card__summary` -> `subtitle` or `description` depending on final role contract;
- `.nilai-card__description` -> `description`.

Important proof: `.nilai-card__title` is also a `span` inside a `button`.

### 3.6 Featured Programs

`resources/views/home/sections/featured-programs.blade.php` renders locale-backed presentation arrays.

Semantic observations:

- section heading -> `section-title`;
- section subtitle -> `subtitle`;
- spotlight title -> `component-title`;
- spotlight subtitle -> `subtitle`;
- chip -> `label`;
- program code -> `meta`;
- program label -> `label`;
- `.program-card__title` -> `component-title`;
- program summary -> `subtitle` / `description` according to final role contract;
- program description -> `description`.

Important proof: `.program-card__title` is a `span` inside a `button`.

### 3.7 Gallery

`app/Http/Controllers/Concerns/BuildsHomeSections.php` calls `latestGalleryItems(6)`.

`app/Http/Controllers/Concerns/BuildsHomeArticlesAndGallery.php` uses `GalleryItem` when the table exists and locale-selects:

- title;
- type label;
- caption;
- category;
- translated date.

If the table is unavailable, locale-backed gallery fallback data is normalized instead.

`resources/views/home/sections/gallery.blade.php` supplies section shell/copy and CTA.

`resources/views/home/sections/gallery-story.blade.php` renders DB/fallback items.

Semantic observations:

- section heading -> `section-title`;
- section subtitle -> `subtitle`;
- gallery card title -> `component-title`;
- caption -> `description`;
- category/type/date -> `meta`;
- section CTA -> `action`.

DB content policy confirmed by this render path: a GalleryItem title is content; render location decides its typography role.

### 3.8 Articles

`app/Http/Controllers/Concerns/BuildsHomeArticlesAndGallery.php` explicitly clears static article items and uses published `Article` database rows only.

Locale-selected article fields include:

- title;
- description;
- author/display highlight;
- translated date;
- reading-time/runtime copy;
- localized link;
- thumbnail.

`resources/views/home/sections/articles.blade.php` renders the section shell plus DB-backed article cards.

Semantic observations:

- article section heading -> `section-title`;
- section subtitle -> `subtitle`;
- featured article title -> `component-title`;
- rail article title -> `component-title`;
- article description -> `description`;
- category/date/reading time -> `meta`;
- article CTA -> `action`.

DB content policy confirmed: the same Article title may later render as Hero `display`, homepage `component-title`, or reader `page-title`; presentation does not belong in the model fields.

### 3.9 Navbar

Shared navbar is composed from:

- `partials.site-navbar/data/context.php`
- `partials.site-navbar/data/menu.php`
- `partials.site-navbar/data/presentation.php`
- header, language modal, and behavior partials.

Content sources are mixed:

- locale-backed navbar arrays;
- runtime translation keys;
- presentation-generated locale-specific mega-menu copy using `match ($currentLocale)`;
- presentation-generated language modal copy using `match ($currentLocale)`.

Semantic observations:

- nav links -> `action`;
- navbar CTA -> `action`;
- mega eyebrow -> `label`;
- mega title -> `component-title`;
- mega description -> `description`;
- mega link labels -> `action` / `component-title` according to final contract;
- mega link descriptions -> `description`;
- language modal title -> `component-title`;
- language options -> `action`.

Known exception: some visible locale copy is in presentation PHP rather than `lang/*.php`. That is a content-source classification issue, not a typography ownership exception.

### 3.10 Footer

`resources/views/partials/site-footer.blade.php` combines:

- `home.footer`;
- `home_parity.footer`;
- supplied `$footerSection` when present.

Visible roles include:

- brand fallback/name -> `component-title` or `label`;
- brand description -> `description`;
- channel group titles -> `component-title` / `label`;
- channel label -> `action` / `label`;
- channel note -> `meta`;
- footer navigation group titles -> `component-title` / `label`;
- footer links -> `action`;
- copyright -> `meta`.

`resources/js/pages/welcome/navigation-menus.js` writes the current year with `textContent` into `#currentYear`. JS may populate this visible text, but does not own its typography.

## 4. JS-created visible text

Observed homepage JS-visible text paths include:

1. Footer current year via `#currentYear.textContent`.
2. Homepage Gallery story lightbox dynamically creates close controls and assigns localized/fallback close text from `data-close-label`.
3. Gallery lightbox may create an iframe/image and derives iframe title from the card title or fallback video title.

M00 rule remains: JS may populate text, but responsive/component font sizing must not be implemented in JS.

## 5. Semantic-role conclusion from source discovery

Source discovery proves that typography cannot be mapped by HTML tag alone.

Examples:

- `.misi-card__title` -> `span` -> `component-title`;
- `.nilai-card__title` -> `span` -> `component-title`;
- `.program-card__title` -> `span` -> `component-title`;
- article rail title -> `span` -> `component-title`;
- Gallery card title -> `h3` -> `component-title`.

Therefore the target contract remains render-location based and should later be expressed with canonical semantic role markers such as `data-text-role`, while existing component classes continue to own layout/behavior.

No `data-text-role` markers are added during M00.

## 6. Typography source discovery

Local read-only command audited these properties across homepage and Arabic CSS:

- `font-family`
- `font-size`
- `font-weight`
- `line-height`
- `letter-spacing`

Result:

`M00_HOME_TYPOGRAPHY_SOURCE_LINES=598`

This count is declaration hits, not unique visible text nodes and not proof of a winning cascade rule.

Important source examples include:

- Hero title declarations across multiple Hero cascade modules;
- About headline/description declarations across base, desktop, mobile, and RTL modules;
- many section/card/footer declarations across the 47 welcome modules;
- Arabic base and type-scale overrides.

## 7. CSS import/cascade evidence

### 7.1 `resources/css/pages/welcome.css`

Imports 47 ordered modules:

`001` through `047`, including multiple files explicitly named `welcome-cascade-*` plus scoped typography/rhythm and later feature patches.

Conclusion: source declaration existence alone cannot prove the winning runtime style.

### 7.2 `resources/css/pages/welcome-hero.css`

Imports 9 ordered Hero modules:

`001` through `009`.

### 7.3 `resources/css/pages/welcome-about-stats.css`

Imports 6 ordered About modules:

1. base;
2. desktop reel;
3. mobile static;
4. RTL motion;
5. reduced motion;
6. warp layer.

### 7.4 `resources/css/arabic-typography.css`

Imports:

- Cairo Arabic 500/600/700;
- Lateef Arabic 400/700;
- `arabic-typography-base.css`;
- `arabic-type-scale.css`.

Target architecture remains:

- Cairo for display/heading/UI roles;
- Lateef for prose/description/longform roles;
- Arabic CSS becomes a locale adapter, not a competing component typography system.

## 8. Runtime environment proof

Homepage runtime:

- URL: `http://127.0.0.1:8000/`
- HTTP: `200`
- active locale during current runtime audit: `en`
- direction: `ltr`

Browser tooling proof:

- Brave Browser `150.1.92.144` available at `/usr/local/bin/brave`;
- headless CDP works;
- CDP Protocol `1.3`;
- Node runtime `v26.5.0`;
- Node built-in `WebSocket=function`;
- Python `websocket` and `websockets` modules are absent;
- no Playwright/Puppeteer dependency is present or required.

Decision: continue runtime audit through Brave headless + CDP + Node built-in WebSocket. Do not install browser-test dependencies for M00.

## 9. Computed-style proof — EN / 1440

Runtime viewport:

- locale: `en`
- dir: `ltr`
- width: `1440`
- height: `1000`
- DPR: `1`

Representative computed typography:

| Semantic observation | Selector | Family summary | Size | Weight | Line-height | Letter-spacing |
|---|---|---|---:|---:|---:|---:|
| nav action | `.navbar__menu .nav-link` | system-ui | 12.48px | 760 | 19.968px | 0.1872px |
| Hero display | `.hero-cinema__title` | ui-rounded | 43.2px | 760 | 41.472px | -1.944px |
| Hero description | `.hero-cinema__description` | system-ui | 13.44px | 820 | 20.16px | normal |
| About section title | `.about-reel__headline` | ui-rounded | 82.08px | 850 | 74.6928px | -5.3352px |
| About description | `.about-reel__description` | system-ui | 18.88px | 480 | 33.04px | normal |
| section title | `#visi-misi .section-title` | ui-rounded | 47.6px | 800 | 49.98px | -2.142px |
| section subtitle | `#visi-misi .section-subtitle` | system-ui | 17.28px | 400 | 29.0304px | normal |
| mission component title | `.misi-card__title` | ui-rounded | 16.96px | 950 | 18.9952px | normal |
| program component title | `.program-card__title` | ui-rounded | 26.4px | 950 | 26.928px | -1.188px |
| gallery component title | `.galeri-story-card__content h3` | ui-rounded | 41.92px | 700 | 44.4352px | -1.6768px |
| gallery meta | `.galeri-story-card__meta span` | system-ui | 12px | 900 | 19.2px | 1.56px |
| article component title | `.artikel-digest__hero-title` | system-ui | 20.88px | 900 | 22.5504px | -0.7308px |
| article description | `.artikel-digest__hero-description` | system-ui | 15.68px | 400 | 24.304px | normal |
| article meta | `.artikel-digest__meta span` | system-ui | 13.12px | 750 | 20.992px | normal |
| footer description | `.footer-brand__description` | system-ui | 15.2px | 400 | 26.144px | normal |
| footer action | `.footer-links a` | system-ui | 14.72px | 400 | 23.552px | normal |

Hero CTA was not present in the active runtime content and was recorded as `found=false`; this is not an audit failure.

### 9.1 EN / 1440 conclusion

Current runtime proves the typography system is component-owned rather than role-owned.

Especially important: elements that all map to `component-title` have materially different winning typography:

- Mission title: 16.96px / 950 / ui-rounded;
- Program title: 26.4px / 950 / ui-rounded;
- Gallery title: 41.92px / 700 / ui-rounded;
- Article title: 20.88px / 900 / system-ui.

This is direct runtime evidence for the Unified Text System problem statement.

Hero description is also only 13.44px at weight 820, while the general section subtitle is 17.28px at weight 400, another example of per-component typography evolution.

## 10. Computed-style proof — EN / 768

Runtime viewport:

- locale: `en`
- dir: `ltr`
- width: `768`
- height: `1024`

| Observation | Size | Weight | Line-height | Letter-spacing | Family summary |
|---|---:|---:|---:|---:|---|
| nav action | 17.92px | 780 | 28.672px | 0.2688px | system-ui |
| Hero display | 36.096px | 760 | 34.6522px | -1.62432px | ui-rounded |
| Hero description | 13.44px | 820 | 20.16px | normal | system-ui |
| About title | 74.496px | 850 | 67.7914px | -4.84224px | ui-rounded |
| About description | 17.856px | 480 | 31.248px | normal | system-ui |
| section title | 32.48px | 800 | 34.104px | -1.4616px | ui-rounded |
| section subtitle | 16.5504px | 400 | 27.8047px | normal | system-ui |
| mission component title | 16.96px | 950 | 18.9952px | normal | ui-rounded |
| program component title | 23.68px | 950 | 24.1536px | -1.0656px | ui-rounded |
| gallery component title | 27.936px | 700 | 30.1709px | -1.11744px | ui-rounded |
| gallery meta | 12px | 900 | 19.2px | 1.56px | system-ui |
| article component title | 20.48px | 900 | 22.1184px | -0.7168px | system-ui |
| article description | 15.68px | 400 | 24.304px | normal | system-ui |
| article meta | 13.12px | 750 | 20.992px | normal | system-ui |
| footer description | 15.2px | 400 | 26.144px | normal | system-ui |
| footer action | 14.72px | 400 | 23.552px | normal | system-ui |

## 11. Computed-style proof — EN / 390

Runtime viewport:

- locale: `en`
- dir: `ltr`
- width: `390`
- height: `844`

| Observation | Size | Weight | Line-height | Letter-spacing | Family summary |
|---|---:|---:|---:|---:|---|
| nav action | 16px | 780 | 25.6px | 0.24px | system-ui |
| Hero display | 30.42px | 760 | 29.8116px | -1.2168px | ui-rounded |
| Hero description | 11.84px | 820 | 17.168px | normal | system-ui |
| About title | 55.38px | 850 | 52.611px | -3.5997px | ui-rounded |
| About description | 16.155px | 480 | 28.2712px | normal | system-ui |
| section title | 32.76px | 800 | 35.3808px | -1.4742px | ui-rounded |
| section subtitle | 15.68px | 400 | 26.3424px | normal | system-ui |
| mission component title | 16.96px | 950 | 18.9952px | normal | ui-rounded |
| program component title | 20.16px | 950 | 20.5632px | -0.9072px | ui-rounded |
| gallery component title | 27.3px | 700 | 29.484px | -1.092px | ui-rounded |
| gallery meta | 12px | 900 | 19.2px | 1.56px | system-ui |
| article component title | 20.48px | 900 | 22.1184px | -0.7168px | system-ui |
| article description | 15.68px | 400 | 24.304px | normal | system-ui |
| article meta | 13.12px | 750 | 20.992px | normal | system-ui |
| footer description | 15.2px | 400 | 26.144px | normal | system-ui |
| footer action | 14.72px | 400 | 23.552px | normal | system-ui |

## 12. EN responsive baseline conclusion

EN runtime evidence now exists for all required public proof widths:

- 1440px;
- 768px;
- 390px.

Observed facts:

1. Semantic hierarchy is not centralized.
2. Several component-title surfaces use unrelated winning font sizes/weights/families.
3. Some responsive rules change typography substantially, while other roles remain fixed across widths.
4. Hero description drops to 11.84px on 390px while retaining weight 820.
5. Mission component title remains 16.96px / 950 at all three measured widths.
6. Gallery title shifts from 41.92px at 1440 to about 27px at 768/390.
7. Article title remains about 20.5px at 768/390 and uses system-ui, unlike several other component-title surfaces using ui-rounded.
8. Footer description/action remain numerically stable across the measured widths.

These differences are baseline evidence only. M00 does not yet judge final target numeric values or change CSS.

## 13. M00 evidence status

Completed:

- repository preflight sync;
- homepage render-tree discovery;
- homepage content-source classification;
- DB/lang/presentation/JS ownership classification;
- initial semantic-role mapping;
- typography source declaration discovery;
- CSS import-order evidence;
- browser/CDP runtime harness proof;
- EN computed-style runtime proof at 1440px;
- EN computed-style runtime proof at 768px;
- EN computed-style runtime proof at 390px.

Pending:

- ID runtime parity proof at required widths;
- AR/RTL runtime parity proof at required widths;
- consolidate final homepage role inventory table with locale applicability and known exceptions;
- record only truly ambiguous winning-style notes that remain after runtime proof;
- close homepage M00 gate before moving to the next surface.

Approximate M00 homepage progress after EN responsive runtime proof: 90%.

This percentage is for M00 homepage only, not the entire M00 milestone and not the entire Unified Text System project.

## 14. Next valid step

Do not repeat source discovery, import-order discovery, browser capability checks, or EN 1440/768/390 computed-style audits.

Next required evidence:

**ID and AR locale parity runtime proof using the same representative selectors and required viewport widths.**

Order:

1. ID 1440 / 768 / 390;
2. AR 1440 / 768 / 390, confirming `dir=rtl` and Arabic font-family adapter behavior;
3. compare semantic hierarchy across EN / ID / AR;
4. finalize homepage M00 inventory and mark homepage M00 PASS only if the evidence is complete.

No implementation changes before this evidence gate closes.
