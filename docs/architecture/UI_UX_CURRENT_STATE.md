# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-09
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-007-DETAIL-MEDIA-REPLAY-SPLIT`
Source baseline: `4bb8787dc3b56e289856de48782bd01b5624fd0e`
Active blueprint: `blueprints/2026-08-08-home-program-kinetic-type-transition.md`

## Latest owner decision

- Keep the Codrops kinetic transition when a Program card is opened.
- Detail state must stay visually clean but restore one large selected Program image.
- Detail contains only triple-chevron Back, selected Program image, selected Program title and one description.
- Back labels remain `Kembali`, `Back`, and `العودة`; the visual mark is `<<<` and mirrors under RTL.
- Do not restore number, eyebrow/category, secondary intro/summary, next-program code or other detail metadata.
- Reduce the detail title to roughly one-third of the prior oversized implementation while retaining the section-heading editorial font grammar.
- Program section Center Split must have a small physical gap between the two masks and replay whenever the heading leaves and re-enters the viewport from either direction.
- Keep idle cards, eleven-step Visi/Misi -> Program handoff, Visi/Misi, card geometry and other homepage sections unchanged.

## Implemented clean detail contract

- Blade renders six detail articles with one image, one `h3` title and one `.program-kinetic__detail-description` each.
- Detail image reuses the selected Program's existing development Unsplash source; idle-card media remains unchanged.
- Detail metadata remains absent: no number, eyebrow, intro, next-code or duplicate summary.
- `detailParts()` collects title + description and the restored detail image wrapper/image.
- GSAP restores the Codrops-style image wrapper/image vertical reveal while keeping the simplified two-element copy reveal.
- Desktop detail uses clean copy + image columns; RTL mirrors the column ownership.
- Compact tiers stack the image before the copy.
- Detail title is now `clamp(2.25rem, 3vw, 4.25rem)` in LTR with display weight `360`, tight `-.055em` tracking and uppercase presentation.
- Arabic detail title uses Cairo, weight `500`, normal Arabic casing/tracking and an Arabic-appropriate scale/line-height.
- Description remains limited to a readable `42rem` measure.
- Back uses a static `<<<` mark plus localized label; RTL reverses layout and mirrors the mark rather than relying on a Unicode arrow.

## Program heading Center Split correction

Previous source behavior:

- `IntersectionObserver` disconnected after the first reveal, so the heading could not replay after scrolling away and back;
- two line masks used only `.035em` row gap plus negative block margins, making the reveal zones visually collide;
- hidden transforms used `108%`, leaving little safety margin around large glyphs.

Implemented correction:

- observer remains mounted for the Program lifecycle;
- reveal class is toggled from intersection state, so leaving resets and re-entry from above or below replays;
- threshold contract is `[0, 0.16]` with the existing bottom root margin;
- row gap is `.12em`;
- line masks use zero block margin and `.04em` internal padding;
- hidden travel is `+114%` for the first line and `-114%` for the second line;
- no horizontal heading translation was added;
- reduced-motion still resolves immediately.

## Existing Program contract retained

- Program idle cards remain desktop `2 + 4`, tablet `3 + 3`, mobile `2 + 2 + 2`.
- Idle media remains landscape `4 / 3` with the Codrops-inspired stagger geometry.
- Idle card copy remains title + description only.
- `Program Kami` / `Our Programs` remains visually uppercase in LTR.
- Arabic Program heading remains Cairo without Latin uppercase behavior.
- One Program kinetic type field spans the content-driven Program height.
- The field renders 20 lines from locale-owned Islamic vocabulary.
- Program background remains `#e7f5ff`; kinetic type remains `#397aa6` at resting opacity `.16`.
- The eleven-step handoff remains unchanged.
- GSAP 3.7.1 remains lazy-loaded from jsDelivr.
- Open/close restores kinetic resting opacity and clears inline transform/opacity state.
- No wheel hijacking, document `scrollTo`, WebGL or continuous page RAF was introduced.

## Locale contract

Public Program content remains locale-owned in:

- `lang/id/home_program.php`
- `lang/en/home_program.php`
- `lang/ar/home_program.php`

Back labels remain:

- ID: `Kembali`
- EN: `Back`
- AR: `العودة`

Existing item metadata remains in locale data but is not rendered in the clean detail state.

## Scope proof

Changed implementation surfaces are limited to:

- Program detail Blade markup;
- Program detail DOM collection and GSAP targets;
- Program detail CSS presentation in HUD/wide/compact modules;
- Program heading Center Split CSS/observer behavior;
- focused Program regression test;
- Program blueprint/current-state docs.

Idle card geometry rules, handoff source, Visi/Misi source and unrelated homepage sections were not changed in this batch.

## Focused source tests

`HomeProgramJourneyTest.php` now locks:

- six localized cards/details;
- one Program kinetic type field with 20 lines;
- twelve Program image occurrences: six idle + six detail;
- six detail image wrappers and six detail images;
- localized short Back labels plus `<<<` mark source;
- exactly six detail descriptions;
- absence of number/eyebrow/intro/next markup;
- restored detail-image JS/CSS targets;
- reduced section-style detail-title scale;
- replayable Center Split intersection contract;
- `.12em` split gap, zero negative mask margin and `±114%` hidden travel;
- existing kinetic transition/state restoration;
- existing handoff and responsive idle-card geometry contracts.

## Proof state

Owner-local proof from before this batch remains only a baseline:

- Laravel suite: `197 passed (1659 assertions)` in `13.87s`.

No post-batch local build/browser proof is available through the GitHub-only execution channel.

Required local proof against current `main`:

```bash
git diff --check
git status --short
php artisan test --filter=HomeProgramJourneyTest
php artisan test
npm run check:structure
npm run build
```

Runtime proof required:

- each of the six cards opens the matching image/title/description;
- detail visibly contains only `<<< Back`, image, title and one description;
- title is materially smaller than the previous screenshot and retains editorial display character;
- ID/EN/AR detail remains readable across responsive tiers;
- RTL Back mark points toward the return direction and detail columns mirror correctly;
- scroll into Program from Visi/Misi triggers Center Split;
- scroll past the heading, then return from below, triggers Center Split again;
- the two heading lines remain visually separated during the reveal and do not collide;
- repeated open -> Back cycles restore the same kinetic resting state;
- Escape, Tab containment and focus restoration still work;
- reduced motion remains usable;
- 360, 390, 640, 768, 1024, 1180/1181, 1280, 1440, 1536 and 1920 widths;
- Chromium and WebKit.

STATUS: `BLOCKED_BY_MISSING_EVIDENCE`

NEXT EXECUTION CHANNEL: `owner/local terminal`

NEXT VALID STEP: pull current `main`, run `HomeProgramJourneyTest`, then verify one desktop ID detail plus two Program-heading viewport entries: once from above and once from below.
