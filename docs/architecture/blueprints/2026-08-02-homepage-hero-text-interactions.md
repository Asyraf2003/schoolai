# Homepage Hero Text Interactions Blueprint

Blueprint ID: `HOME-HERO-TEXT-001`
Status: `IMPLEMENTING`
Owner: repository owner through the 2026-08-02 implementation, correction, and PPDB campaign decisions
Campaign-link batch base: `867954734fa91b8301a07bbadf23e160087c28de`
Surface: `/` homepage hero
Execution channel: Web AI with explicit direct-main permission

## FACT and corrected decisions

- Active admin/article placements are the managed source of truth whenever at
  least one placement survives normalization.
- Translation slides remain fallback-only when no managed slide is available.
- Final index zero is the primary slide; no unmanaged video is inserted ahead of
  admin ordering.
- `PpdbSetting` is the sole registration-open source of truth.
- When PPDB is open, the primary video keeps its managed media but becomes one
  complete localized PPDB campaign presentation.
- The owner requires the campaign heading, description, and CTA to navigate to
  the same normalized registration URL.
- No open-campaign target may navigate to the underlying article.
- When PPDB is closed, the complete article presentation returns.
- Navbar, About, Testimonial, other slides, admin ordering, upload behavior, and
  database schema remain protected.

## Goal and scope

Use the first managed video as the visual background for two mutually exclusive
server-rendered states:

```text
PPDB open
-> PPDB eyebrow
-> linked PPDB h1 -> publicRegistrationUrl()
-> linked PPDB description -> publicRegistrationUrl()
-> PPDB CTA -> publicRegistrationUrl()
-> no article destination

PPDB closed
-> article eyebrow
-> linked article h1
-> article description
-> normal article CTA
```

Editable owners:

- `app/Support/HomeHeroPresentation.php`;
- `resources/views/home/sections/hero.blade.php`;
- `resources/css/pages/welcome-hero/text-interactions.css`;
- ID/EN/AR runtime locale copy;
- focused hero interaction tests;
- this blueprint and `UI_UX_CURRENT_STATE.md`.

Forbidden: media/order composition, navbar, About, Testimonial, other homepage
surfaces, article records, admin controls, schema, uploads, and dependencies.

## Presentation contract

Every final slide exposes `is_primary_slide`, `show_ppdb_cta`, `ppdb_url`,
`ppdb_label`, `title_href`, and `description_href` through
`HomeHeroPresentation`.

For the primary rendered video while registration is open:

- `eyebrow`, `title`, and `description` are replaced from runtime locale keys;
- `ppdb_url`, `title_href`, and `description_href` all equal
  `PpdbSetting::publicRegistrationUrl()`;
- the normal slide `cta` is replaced by an empty array;
- Blade renders the heading anchor, description anchor, and PPDB CTA as three
  separate valid links with one destination;
- media, poster, focal point, overlay, article relation, and ordering are not
  mutated.

A later server request with registration closed decorates the original managed
slide without campaign overrides. `description_href` becomes `null`, while the
article heading link and normal article CTA return.

## Locale copy

| Locale | Direction | Campaign behavior |
|---|---|---|
| ID | LTR | Indonesian PPDB eyebrow, invitation title, description, and CTA |
| EN | LTR | English admissions campaign copy |
| AR | RTL | Arabic campaign copy with intact shaping and RTL layout |

The same semantic DOM and current typography owners remain active. The title
continues using the existing directional glow: LTR for ID/EN and one shaped RTL
run for AR.

## Six-tier contract

The copy/link source changes but layout ownership does not:

| Tier | Result |
|---|---|
| XS 360–639 | wrapped campaign copy and reachable touch links |
| SM 640–767 | fluid campaign copy with no reserved article residue |
| MD 768–1023 | existing media/copy composition retained |
| LG 1024–1279 | existing composition; navigation 1180/1181 untouched |
| XL 1280–1535 | existing desktop composition retained |
| 2XL 1536+ | bounded readable copy retained |

## Accessibility, motion, and fallback

- Exactly one `h1` remains on the first slide.
- The campaign uses separate anchors rather than nested interactive elements.
- Heading, description, and CTA have the same destination but independent
  keyboard focus targets.
- Title and description focus states remain visible.
- Campaign content and links exist in initial server HTML.
- Static/no-JS output remains complete.
- Reduced motion changes no content or link state.
- Existing glow and PPDB roll remain decorative and `aria-hidden` where
  applicable.
- Video poster/loading behavior is unchanged by this link patch.

## Proof status

Published source includes:

- full campaign decoration for open PPDB;
- one normalized registration destination for heading, description, and CTA;
- localized ID/EN/AR campaign copy;
- focused tests requiring exactly three matching registration `href` values;
- restored closed article state, semantics, glow, and poster contract.

GitHub publication does not prove PHP tests, structure, build, responsive layout,
Chromium, or WebKit/Safari runtime. Those remain
`BLOCKED_BY_MISSING_EVIDENCE` until local proof runs. Known unrelated About and
structure baseline debt remains separate.

## Next valid step

Execution channel: `owner/local terminal`.

Pull current `main`, run the focused hero tests and Vite build, then visually
compare PPDB open and closed in ID, EN, and AR. Confirm all three open-state
links share one registration destination and the closed state restores the
linked article.
