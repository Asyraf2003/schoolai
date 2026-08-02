# Homepage Hero Text Interactions Blueprint

Blueprint ID: `HOME-HERO-TEXT-001`
Status: `IMPLEMENTING`
Owner: repository owner through the 2026-08-02 implementation, correction, and PPDB campaign decisions
Source before campaign patch: `79e6be26ab4d8b718d6cd225a5eb68b68440e09d`
Surface: `/` homepage hero
Execution channel: Web AI with explicit direct-main permission

## FACT and corrected decisions

- Active admin/article placements are the managed source of truth whenever at
  least one placement survives normalization.
- Translation slides remain fallback-only when no managed slide is available.
- Final index zero is the primary slide; no unmanaged video is inserted ahead of
  admin ordering.
- `PpdbSetting` is the sole registration-open source of truth.
- The owner rejected a mixed first slide where the article title remained linked
  while only its description/CTA became PPDB.
- When PPDB is open, the primary video keeps its managed media but becomes one
  complete localized PPDB campaign presentation.
- When PPDB is closed, the complete article presentation returns.
- Navbar, About, Testimonial, other slides, admin ordering, upload behavior, and
  database schema remain protected.

## Goal and scope

Use the first managed video as the visual background for two mutually exclusive
server-rendered states:

```text
PPDB open
-> PPDB eyebrow
-> PPDB h1
-> PPDB description
-> no article title link
-> one registration CTA

PPDB closed
-> article eyebrow
-> linked article h1
-> article description
-> normal article CTA
```

Editable owners:

- `app/Support/HomeHeroPresentation.php`;
- `resources/views/home/sections/hero.blade.php`;
- ID/EN/AR runtime locale copy;
- focused hero interaction tests;
- this blueprint and `UI_UX_CURRENT_STATE.md`.

Forbidden: media/order composition, navbar, About, Testimonial, other homepage
surfaces, article records, admin controls, schema, uploads, and dependencies.

## Presentation contract

Every final slide exposes `is_primary_slide`, `show_ppdb_cta`, `ppdb_url`,
`ppdb_label`, and `title_href` through `HomeHeroPresentation`.

For the primary rendered video while registration is open:

- `eyebrow`, `title`, and `description` are replaced from runtime locale keys;
- `title_href` is explicitly `null`;
- the normal slide `cta` is replaced by an empty array;
- `ppdb_url` is `PpdbSetting::publicRegistrationUrl()`;
- Blade renders campaign description and PPDB CTA together;
- media, poster, focal point, overlay, article relation, and ordering are not
  mutated.

A later server request with registration closed decorates the original managed
slide without campaign overrides, restoring its article link and copy.

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

The copy source changes but layout ownership does not:

| Tier | Result |
|---|---|
| XS 360–639 | wrapped campaign copy and reachable touch CTA |
| SM 640–767 | fluid campaign copy with no reserved article residue |
| MD 768–1023 | existing media/copy composition retained |
| LG 1024–1279 | existing composition; navigation 1180/1181 untouched |
| XL 1280–1535 | existing desktop composition retained |
| 2XL 1536+ | bounded readable copy retained |

## Accessibility, motion, and fallback

- Exactly one `h1` remains on the first slide.
- Open PPDB state has no misleading article anchor.
- Campaign title, description, and CTA exist in initial server HTML.
- Static/no-JS output remains complete.
- Reduced motion changes no content or link state.
- Existing glow and PPDB roll remain decorative and `aria-hidden` where
  applicable.
- Video poster/loading behavior is unchanged by this copy patch.

## Proof status

Published source includes:

- full campaign decoration for open PPDB;
- campaign description rendered alongside the CTA;
- localized ID/EN/AR campaign copy;
- focused tests for campaign replacement, absent article link, restored closed
  article state, semantics, glow, and poster contract.

GitHub publication does not prove PHP tests, structure, build, responsive layout,
Chromium, or WebKit/Safari runtime. Those remain
`BLOCKED_BY_MISSING_EVIDENCE` until local proof runs. Known unrelated About and
structure baseline debt remains separate.

## Next valid step

Execution channel: `owner/local terminal`.

Pull current `main`, run the focused hero tests, then visually compare PPDB open
and closed in ID, EN, and AR. Confirm the open state has only campaign intent and
the closed state restores the linked article.
