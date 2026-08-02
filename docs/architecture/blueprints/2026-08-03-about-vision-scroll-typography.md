# About and Vision/Mission Scroll Typography

BLUEPRINT ID: `HOME-STORY-001`
STATUS: `IMPLEMENTING`
OWNER: Asyraf Mubarak
DATE: 2026-08-03
SOURCE MAIN SHA: `61f35579bff7dea348f520b109c29c148399466f`
ACTIVE ROUTE/SURFACE: homepage About and Vision/Mission
TARGET EXECUTION CHANNEL: Web AI GitHub direct `main`

## Goal and reference

Rebuild About and Vision/Mission from clean semantic source using the motion
principles of Codrops On-Scroll Typography Animations Set 2. About translates
reference effect 25: glyphs grow vertically with scroll progress inside a long
sticky blue scene. Vision/Mission uses related but varied word/glyph entrances.
The reference code, fonts, branding, smooth-scroll hijacking, and composition
are not copied.

## Facts and decisions

- About was disabled while its reel/video/canvas implementation remained in source.
- Vision/Mission used separate desktop/mobile controllers and interactive cards.
- Local decorative assets `public/media/home/9.png` through `12.png` exist.
- ID/EN animate Unicode characters; AR animates complete words so joining is preserved.
- Native requestAnimationFrame plus normal browser scrolling replaces GSAP,
  ScrollTrigger, Lenis, and Splitting.
- Semantic text remains visible without JavaScript. Reduced motion resolves to
  the final static state.

## Scope

Editable: both section partials, their dedicated CSS/JS owners, homepage/Vite
entries, focused tests, structure retirement handling, and current-state docs.
Forbidden: Hero, Testimonial, School Values, Programs, Gallery, Articles,
navigation, authentication, translations, database, and dependencies.

## Storyboard

About is a sticky blue stage across a bounded long-scroll track. Its heading
uses vertical scale reveal while four white-tinted school graphics drift at
separate depths. Vision/Mission is a sequence of readable full-height editorial
beats using rise, fan, focus, and stretch effects. Reverse scroll reverses the
progress. Resize recalculates geometry. Page hide and BFCache dispose/recreate
listeners.

## Responsive and locale contract

The same DOM serves XS through 2XL. Fluid clamp sizing and two composition
adapters cover <=767 and 768-1180; larger widths use the full editorial field.
ID/EN are LTR with character motion. AR is RTL, Cairo, normal tracking, and
word-level motion. Vertical scroll time and decorative rotation are not mirrored;
horizontal parallax follows logical direction.

## Accessibility and performance

No canvas, WebGL, external font, smooth-scroll library, dependency, or continuous
idle loop is added. Motion runs only in response to scroll/resize through one RAF
scheduler per surface. Text keeps an accessible label, decorative images have
empty alternatives, and reduced motion disables sticky travel and transforms.

## Proof gates

Required: `git diff --check`, structure check, Vite build, focused feature tests,
full PHP suite, six-tier ID/EN/AR review, Chromium/WebKit, keyboard/touch, 200%
zoom, reduced motion, fast/reverse scroll, resize/orientation, BFCache, and
PageSpeed comparison. Connector publication alone leaves runtime gates blocked.
