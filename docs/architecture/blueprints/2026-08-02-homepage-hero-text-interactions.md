# Homepage Hero Text Interactions Blueprint

Blueprint ID: `HOME-HERO-TEXT-001`
Status: `IMPLEMENTING`
Owner: repository owner through the 2026-08-02 implementation and correction briefs
Correction parent SHA: `1035427daf4e00349a7d833c0121ab6cacb21ccb`
Surface: `/` homepage hero
Execution channel: Web AI with explicit direct-main permission

## FACT and corrected decision

- Translation data contains a demo `flower.mp4` slide.
- Active admin/article placements are the public hero's managed source of truth.
- The earlier implementation prepended the translation demo video to managed
  placements, so the admin displayed five placements while the homepage rendered
  an additional unmanaged sixth slide.
- Database/article output now replaces translation fallback as one complete list
  when at least one slide survives normalization.
- Translation slides remain only when no database/article slide is available.
- The first final slide owns `is_primary_slide`; no hidden primary slide is
  inserted ahead of the admin order.
- `title_href` remains article-only and is never inferred from generic CTA data.
- PPDB visibility remains sourced only from `PpdbSetting` and applies only when
  the first final slide renders as video.
- Navbar markup, styles, scripts, and motion remain untouched.

## Goal and scope

The first active admin placement must be the first public slide. In the owner's
current data that placement is the uploaded article video. When PPDB is open,
that video's normal description/CTA is replaced by the localized PPDB CTA. When
closed, its normal article presentation returns.

Editable owners:

- `InjectsDatabaseHero` composition;
- hero media and glow JS modules;
- directly related hero/admin tests;
- this blueprint and `UI_UX_CURRENT_STATE.md`.

Forbidden: About, Testimonial, navbar, other homepage surfaces, article content,
admin ordering controls, database schema, media upload behavior, and new
runtime dependencies.

## Presentation contract

```text
managed slides available
-> normalize managed slides
-> first managed slide is primary
-> render managed list only

no managed/article/legacy slides available
-> retain translation fallback list
```

Every final slide exposes `is_primary_slide`, `show_ppdb_cta`, `ppdb_url`,
`ppdb_label`, and `title_href` through `HomeHeroPresentation`.

No public slide may exist outside the ordering/activation contract visible to
admin when managed placements are active.

## Video fallback contract

- Keep the server-rendered poster attribute while the video hydrates and starts.
- Do not remove the poster merely because a slide becomes active.
- The browser replaces the poster naturally when video frames become available.
- Failed or delayed playback therefore retains meaningful media instead of
  exposing only the dark-green media background.

## Semantic and motion contract

- Keep exactly one `h1`; later slides remain `h2`.
- Article anchors remain server-rendered inside their heading.
- Plain title text remains the semantic/no-JS source.
- JS adds only an `aria-hidden` decorative overlay.
- Every `hero:slide-active` event schedules one title sweep after 140ms on every
  viewport and input mode, including initial load, autoplay, arrow navigation,
  keyboard navigation, dot selection, and swipe.
- Pointer hover and keyboard focus may replay the same bounded effect.
- ID/EN use grapheme segmentation and left-to-right energy.
- AR remains one shaped run with a mirrored right-to-left gradient sweep.
- Reduced motion keeps the static title and focus treatment without glow/roll.

## Six-tier contract

| Tier | Managed first slide | Automatic glow |
|---|---|---|
| XS 360–639 | admin order, touch-safe | once per activation |
| SM 640–767 | admin order, touch-safe | once per activation |
| MD 768–1023 | admin order | once per activation |
| LG 1024–1279 | admin order; preserve 1180/1181 nav boundary | once per activation |
| XL 1280–1535 | admin order | once per activation |
| 2XL 1536+ | admin order, bounded copy | once per activation |

The correction changes no layout breakpoint, navigation behavior, heading
geometry, or locale architecture.

## Capability, performance, and accessibility

- Static semantic HTML remains fully usable.
- Motion uses bounded Web Animations API calls and one short activation timer.
- No RAF loop, canvas, WebGL, dependency, network request, or per-character
  listener is added.
- Decorative layers remain pointer-inert and hidden from assistive technology.
- The poster remains available during media loading/failure.

## Proof status

Published source correction includes:

- removal of fallback/admin list concatenation;
- automatic active-slide glow for every viewport;
- retention of video posters during hydration/playback;
- updated focused contracts for article placement, database fallback, admin raw
  video, title semantics, PPDB, glow, and poster stability.

Connector publication does not run local PHP, Vite, structure, browser, or
Safari proof. Those gates remain `BLOCKED_BY_MISSING_EVIDENCE` until the owner
pulls and runs them. Previously recorded baseline failures for the protected
About test and Vision/Mission/source-equivalence structure debt remain separate.

## Next valid step

Execution channel: `owner/local terminal`.

Pull current `main`, run the focused hero/admin tests and production build, then
visually verify that the admin's placement `01` is the first public slide and
that title glow replays on initial load plus every manual/automatic transition.
