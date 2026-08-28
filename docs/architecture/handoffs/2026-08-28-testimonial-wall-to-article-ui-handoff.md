# Testimonial Wall → Article UI Handoff — 2026-08-28

## Purpose

This handoff freezes the current Homepage Testimonial implementation and defines the entry point for the next session, which must focus on **Article UI**.

Repository: `Asyraf2003/schoolai`
Branch: `main`
Baseline before this handoff commit:

- `4beda6cd2702364d47efd79713f3d00687831471` — `Refine testimonial typography and byline alignment`

Do not reconstruct the Testimonial behavior from chat history. Use the contracts below unless browser proof shows a regression.

---

## 1. FACT — current homepage order

Current active Homepage order in `resources/views/welcome.blade.php` is:

```text
Hero
→ Vision / About
→ Program
→ Values
→ Gallery
→ Testimonial
→ Footer
```

Article is **not active in the Homepage DOM** at this baseline.

Testimonial is rendered by:

- `resources/views/home/sections/testimonials.blade.php`
- `resources/css/pages/welcome-testimonial-wall.css`
- `resources/js/pages/welcome/testimonial-wall.js`
- `app/Http/Controllers/Concerns/BuildsHomeSections.php`
- `lang/id/testimonials.php`
- `lang/en/testimonials.php`
- `lang/ar/testimonials.php`
- `tests/Feature/HomeTestimonialWallTest.php`

Do not alter Gallery, Program, Values, Hero, About, Footer, or their motion contracts while discussing Article unless the owner explicitly expands scope.

---

## 2. Testimonial presentation contract

### Section transition

Gallery currently ends on its green story surface. Testimonial owns the visual handoff at its own top edge rather than modifying Gallery scroll geometry.

Current transition:

- starts from `var(--gallery-story-bg, #6f9b72)`;
- fades vertically to `#f4f3ef`;
- uses `--testimonial-handoff-height: clamp(7rem, 14vw, 14rem)`;
- reaches the off-white Testimonial surface before the title/content begins;
- the former small uppercase `TESTIMONI` eyebrow was removed;
- only title + description remain in the intro.

Do not move this fade into Gallery JS unless visual proof demonstrates a real boundary defect.

### Rows and cards

Current structure:

- 3 horizontal rows;
- 7 cards per row;
- 21 cards total;
- row directions alternate;
- vertical page scroll remains the only primary interaction;
- enhanced mode disables manual horizontal swipe with `touch-action: pan-y`;
- no carousel/vendor dependency.

Responsive framing contract:

- phone: approximately `2 + 1/3` cards across the viewport;
- tablet: approximately `3 + 1/3` cards;
- desktop: approximately `5 + 1/3` cards;
- card aspect ratio is **2:1 on every viewport**;
- responsive differences change scale, not card shape.

This ratio was explicitly corrected after an earlier implementation accidentally made phone/tablet cards taller than desktop.

### Scroll-linked travel

The motion is genuinely scroll-linked, not autonomous marquee playback.

Current JS owner:

- `resources/js/pages/welcome/testimonial-wall.js`

Important contract:

- one requestAnimationFrame scheduler;
- IntersectionObserver is lifecycle/activation support, not autonomous playback;
- ResizeObserver refreshes measured travel;
- transforms use `translate3d`;
- RTL physically reverses the alternating motion;
- reduced motion exposes static readable content;
- the old travel cap of approximately `1.35` cards was removed;
- travel is now `overflow * 0.9`.

Reason: on phone/tablet, all 7 cards must participate in the journey. At the end of the section card 7 is allowed to remain only partially visible; it does not need to finish as a fully centered card.

Do not restore the old `Math.min(overflow * ..., cardWidth * 1.35 + gap)` cap.

---

## 3. Testimonial typography / locale contract

Each card now renders:

```text
quote

single byline
```

The previous stacked `name + role/status` presentation is retired.

Blade uses one byline value:

- primary: `name`;
- fallback: `role`.

Current visual contract:

- quote: white and bold (`font-weight: 700`);
- no black `-webkit-text-stroke`;
- no text-shadow outline treatment;
- nature media was intentionally selected dark enough to support clean white copy.

Alignment:

### ID / EN

- quote: left aligned;
- byline: occupies up to the right half of the card;
- byline aligned right.

### AR

- quote: RTL and right aligned;
- byline: occupies the opposite/left half;
- byline aligned left.

This opposite-edge byline composition is intentional. Do not normalize both quote and byline to the same text edge merely because the page is RTL.

---

## 4. Testimonial media / Cloudflare R2 state

The temporary four local Homepage images are no longer used by Testimonial.

Current Testimonial backgrounds are 21 distinct first-party R2 objects under:

```text
testimonials/backgrounds/testimonial-nature-01/<uuid>.webp
...
testimonials/backgrounds/testimonial-nature-21/<uuid>.webp
```

Mapping is sequential:

```text
row 1 → nature 01–07
row 2 → nature 08–14
row 3 → nature 15–21
```

All processed sources are:

- WebP;
- 612 × 306;
- exact 2:1 ratio.

They were uploaded through the existing `App\Support\Media\R2MediaStorage` owner rather than an ad-hoc S3 command. The storage owner creates UUID keys, writes cache/content metadata, and verifies object existence after upload.

Canonical public host:

```text
https://media.almustaqbal.sch.id
```

Owner-provided public proof for object 01:

```text
HTTP/2 200
content-type: image/webp
content-length: 75884
cache-control: public, max-age=31536000, immutable
accept-ranges: bytes
server: cloudflare
```

`cf-cache-status` was `MISS` on that proof request; that is not an upload failure.

Known media GAP:

- technical R2 ownership and delivery are proven;
- licensing/provenance documentation for the temporary nature sources is not archived in the repository and must not be invented by a future session.

---

## 5. Relevant Testimonial commit trail

Ordered implementation trail:

1. `398d89756155244b03ecf22a59a48b554b12559d` — `Add scroll-linked testimonial wall`
2. `871ec89b7c6185ab6ebc3490c358a7701069300f` — `Refine testimonial card proportions`
3. `1586350118e8cb0d1cc9fca939c3d0a0344fc902` — `Blend gallery into testimonials`
4. `a28b74133aa72c07043fd1ca2ec2be7e00755ae3` — `Keep testimonial cards 2:1 across viewports`
5. `6361375856fcd34f9e0030d31503cfac26ce2779` — `Let testimonial tracks reach final card`
6. `77e8813f9ec6b6507f83b990dac03696acd7f5c9` — `Simplify testimonial byline and RTL alignment`
7. `1d2a894911df19f2d7488dbaed0f9d33b27c56c8` — `Use R2 testimonial nature backgrounds`
8. `4beda6cd2702364d47efd79713f3d00687831471` — `Refine testimonial typography and byline alignment`

There are unrelated owner commits interleaved in `main`; preserve them and use fast-forward-only mutation.

---

## 6. Validation state

### Proven

- Testimonial is active after Gallery and before Footer in current source.
- Owner pulled and visually reviewed the R2-backed result locally.
- R2 upload completed for all 21 objects through the application storage owner.
- public Cloudflare read for object 01 returned HTTP/2 200 with `image/webp` and immutable cache metadata.
- remote `main` was verified after each direct push in this packet.

### Not archived / do not fabricate

No exact final output is archived here for the complete repository DOD after the latest visual refinements:

```bash
git diff --check
npm run check:structure
npm run build
php artisan test
```

Therefore full-repo completion remains `BLOCKED_BY_MISSING_EVIDENCE` if a future session needs to claim complete DOD. The owner visually seeing the section is useful browser evidence, but it is not a substitute for the missing command logs.

---

## 7. ARTICLE UI — active starting state for next session

The next session must **discuss/map Article UI before implementation**.

### Current Homepage Article state

Article is intentionally disabled on Homepage at this baseline.

Evidence:

- `resources/views/welcome.blade.php` does not include `home.sections.articles`;
- `tests/Feature/HomepageArticleSectionTest.php` explicitly asserts that `data-article-story`, `data-article-journey`, and `data-article-final-cta` are absent;
- that test also asserts the Homepage response does not expose `articlesSection`;
- Article therefore must not be treated as an active Homepage surface merely because dormant files remain in the repository.

### Dormant Article implementation still present

Relevant historical/dormant owners include:

- `resources/views/home/sections/articles.blade.php`
- `app/View/Composers/HomeArticlesComposer.php`
- `resources/css/pages/welcome-article-story.css`
- `resources/css/surfaces/home/article-story/base.css`
- `resources/css/surfaces/home/article-story/closing.css`
- `resources/css/surfaces/home/article-story/desktop.css`
- `resources/css/surfaces/home/article-story/desktop-closing.css`
- `resources/css/surfaces/home/article-story/responsive.css`
- `resources/css/surfaces/home/article-story/debug-ruler.css`
- `tests/Feature/HomeArticleStorySourceTest.php`
- `tests/Feature/HomepageArticleSectionTest.php`

Important warning: `articles.blade.php` still contains development instrumentation such as:

- `@include('home.debug.article-ruler')`;
- `.article-debug-mark`;
- visible markers `tes1`, `tes2`, `tes3`, ...

Do not reactivate that Blade blindly.

### Historical blueprint

Historical reference:

- `docs/architecture/blueprints/2026-08-21-desktop-article-journey.md`

It describes a pinned desktop horizontal Article journey and records old Chromium proof, but its status is historical relative to the current Homepage where Article is disabled.

Use it as design/engineering evidence only. Do not assume its old layout is the desired next UI.

### Vite state

`resources/css/pages/welcome-article-story.css` remains registered as a Vite input, but current `welcome.blade.php` does not load it in the active Homepage `@vite([...])` list.

The aggregate Article CSS still imports `debug-ruler.css`, so reactivation requires an ownership/cleanup decision first.

---

## 8. ARTICLE UI — GAPs to resolve before writing code

The next session must establish these decisions with the owner before mutation:

1. Is the requested UI the **Homepage Article section**, the `/artikel` listing/reader UI, or both?
2. If Homepage Article returns, where does it belong relative to Testimonial and Footer?
3. Is the old horizontal/pinned journey retained, simplified, or replaced entirely?
4. How many Article items should Homepage show?
5. What is the phone/tablet/desktop composition contract?
6. What is the AR/RTL choreography contract?
7. What should happen under reduced motion / failed media / slow network?
8. Which dormant debug/source files are reusable and which should be deleted rather than revived?
9. Does Article use the current DB-backed article records directly, and which media owners are allowed?
10. What browser proof is required before the section is activated in `welcome.blade.php`?

Do not change Article CRUD/security, Article Canvas, Gallery, Testimonial, or unrelated Homepage motion while these UI decisions are still unresolved.

---

## 9. Recommended next-session protocol

Use the repository governance sequence:

```text
FACT
→ GAP
→ GOAL
→ IMPACT
→ DECISION
→ BLUEPRINT
→ ACTIVE STEP
→ EXECUTION
→ PROOF
→ PROGRESS
→ STATUS
→ NEXT VALID STEP
```

First action in the Article session should be **read-only mapping**, not code mutation.

Minimum files to inspect before proposing UI:

```text
AGENTS.md
docs/architecture/handoffs/2026-08-28-testimonial-wall-to-article-ui-handoff.md
docs/architecture/blueprints/2026-08-21-desktop-article-journey.md
resources/views/welcome.blade.php
resources/views/home/sections/articles.blade.php
app/View/Composers/HomeArticlesComposer.php
resources/css/pages/welcome-article-story.css
resources/css/surfaces/home/article-story/*
tests/Feature/HomepageArticleSectionTest.php
tests/Feature/HomeArticleStorySourceTest.php
```

Also map the active Article listing/detail/reader owners if the owner confirms that `UI artikel` includes those surfaces.

---

## 10. Ready-to-paste prompt for the next AI session

```text
Kita lanjut project GitHub `Asyraf2003/schoolai` branch `main`.

Fokus sesi ini khusus membahas MASALAH UI ARTIKEL. Jangan ubah kode dulu.

Sebelum diskusi atau implementasi, baca dan petakan state repo saat ini, terutama:

- `AGENTS.md`
- `docs/architecture/handoffs/2026-08-28-testimonial-wall-to-article-ui-handoff.md`
- `docs/architecture/blueprints/2026-08-21-desktop-article-journey.md`
- `resources/views/welcome.blade.php`
- `resources/views/home/sections/articles.blade.php`
- `app/View/Composers/HomeArticlesComposer.php`
- `resources/css/pages/welcome-article-story.css`
- seluruh `resources/css/surfaces/home/article-story/`
- `tests/Feature/HomepageArticleSectionTest.php`
- `tests/Feature/HomeArticleStorySourceTest.php`
- owner Article listing/detail/reader yang aktif bila relevan.

FACT penting: Homepage Article saat ini DISABLED dan tidak dirender di `welcome.blade.php`. Test juga mengunci ketiadaannya. Implementasi Article lama masih tersisa di repo dan mengandung debug ruler / marker `tes1`, `tes2`, dst. Jangan menganggap implementation lama sebagai kontrak aktif dan jangan mengaktifkannya kembali secara buta.

Testimonial yang baru selesai adalah protected scope. Jangan ubah rasio, motion, R2 media, Gallery→Testimonial transition, atau locale alignment-nya saat kita membahas Artikel.

Tujuan awal sesi:
1. bedakan dengan jelas UI Homepage Article vs listing/detail/reader Article;
2. tunjukkan state lama yang reusable dan yang sebaiknya dibuang;
3. petakan masalah UI sekarang berdasarkan source, bukan asumsi;
4. beri opsi desain/arsitektur A/B/C beserta plus-minus;
5. sepakati responsive PC/tablet/HP, ID/EN/AR, reduced-motion, media behavior, dan posisi Article di homepage sebelum menulis kode.

Gunakan alur FACT → GAP → GOAL → IMPACT → DECISION → BLUEPRINT. Setelah mapping, berhenti di DECISION/BLUEPRINT sampai saya memilih arah UI.
```

---

## 11. Handoff status

Testimonial implementation: `IMPLEMENTED_WITH_PARTIAL_PROOF`.

Article UI: `READY_FOR_DISCUSSION`, not active Homepage production scope yet.

Next valid step: pull this handoff from `main`, open a new session with the prompt above, and perform read-only Article UI mapping before any code change.
