# Functional Interaction Matrix — 2026-08-23

Status: `PASS / DURABLE / SOURCE-CONTRACT`
Repository: `Asyraf2003/schoolai`
Runtime source checkpoint: `6e5c3ee690773ab138583e31c5dc695bbfa6bf9a`

## Purpose

Freeze the release-critical interaction contract before H2-H7 execution so Codex
can harden behavior without rediscovering routes, controls, input semantics, or
fallback expectations. This document records source-proven behavior and explicit
certification gaps. It does not claim browser/runtime PASS where no browser proof
has been recorded.

## Global invariant

Every primary action must remain semantically available when advanced motion or a
third-party enhancement fails. Pointer, touch, and keyboard paths may differ in
mechanics, but they must reach the same user outcome. Repeated open/close,
forward/reverse movement, resize, locale change, hidden-tab restoration, and
BFCache restoration must not accumulate contradictory state or duplicate
listeners.

## 1. Hero

Source owner: `resources/js/pages/welcome-hero.js`.

Primary actions:

- previous slide button;
- next slide button;
- slide dot selection;
- autoplay pause/resume;
- ArrowLeft / ArrowRight keyboard navigation;
- non-mouse pointer swipe gesture;
- automatic progression where media/runtime allows it.

Lifecycle/fallback contract:

- reduced-motion affects autoplay/motion behavior;
- visibility changes resynchronize media/playback;
- `pagehide` clears timers/transitions and pauses video;
- user interaction must not be intercepted when the pointer gesture starts on
  links/buttons/forms/role-link controls;
- Hero remains semantically usable without relying on swipe or autoplay.

Runtime certification still required for repeated manual navigation, video
failure, hidden/visible restoration, and touch swipe on target devices.

## 2. Desktop navigation + mega menus

Source owners:

- `resources/views/partials/site-navbar/header.blade.php`;
- `resources/js/pages/welcome-hero/mega-menu.js`;
- `resources/js/pages/welcome/navigation-state.js`.

Primary actions:

- normal links navigate normally;
- Home anchor links use smooth scroll and navbar-height compensation;
- active link follows section position;
- mega-menu trigger opens/closes by click;
- ArrowDown opens a mega menu and focuses its first link;
- Escape closes the active mega menu and restores focus to the trigger;
- outside click closes mega menus;
- opening language UI closes open mega state;
- mega links remain ordinary anchors.

Release contract:

- route links, anchors, focus restoration, `aria-expanded`, `aria-hidden`, and
  `inert` must remain synchronized;
- smooth-scroll failure must not make an anchor unreachable;
- navigation state must remain correct on reverse scroll and resize.

## 3. Mobile navigation

Source owners:

- `resources/js/pages/welcome/navigation-menus.js`;
- `resources/js/pages/mobile-navigation-cinematic.js`.

Primary actions:

- hamburger opens/closes the full-screen menu;
- enhancement is warmed by pointerenter/focus/touchstart;
- cinematic controller is dynamically imported only at <=1180px;
- import failure has a functional non-cinematic fallback controller;
- Escape closes and restores hamburger focus;
- explicit close controls close and restore focus;
- selecting an anchor closes the menu;
- resizing above 1180px forces immediate close;
- opening the language modal requests immediate mobile-menu close.

Release contract:

- body scroll lock must always be restored;
- `hidden`, `inert`, `aria-hidden`, `aria-expanded`, header open-state and focus
  must not diverge after rapid/repeated open-close;
- fallback controller is an accepted functional degradation, not a blank menu.

## 4. Locale switching

Source owners:

- `routes/web/public.php`;
- `resources/views/partials/site-navbar/language-modal.blade.php`;
- `resources/views/partials/site-navbar/behavior.blade.php`.

Primary contract:

- supported locales are exactly ID, EN, AR;
- switch is a CSRF-protected POST to `language.switch`;
- locale is stored in session plus one-year `site_locale` cookie;
- switch returns to the previous same-origin public page where safe;
- previous admin/login/auth locations intentionally redirect to Home;
- external previous URLs are rejected and fall back to Home;
- language dialog opens from desktop/mobile trigger;
- Escape/backdrop/close button closes it;
- focus returns to the prior trigger, or hamburger when opened from mobile menu;
- body scroll lock is restored on close.

H7 must prove ID/EN/AR switching from Home, Gallery, Article and PPDB plus LTR/RTL
layout and focus restoration.

## 5. Program detail interaction

Source owners:

- `resources/js/surfaces/home/program-journey/controller.js`;
- `resources/js/surfaces/home/program-journey/integration.js`;
- `resources/views/home/sections/featured-programs.blade.php`.

Primary actions:

- six program triggers open corresponding detail dialogs;
- each detail has its own Back control;
- Escape closes the active detail;
- Tab is trapped inside the open detail;
- opening locks the underlying card region with `inert` and dialog page classes;
- closing restores focus to the originating trigger;
- reduced-motion uses the functional reduced controller;
- clicks made while GSAP is loading are queued/replayed;
- GSAP load failure falls back to reduced functional behavior;
- an interrupted opening timeline may be killed and safely closed.

Release contract:

- Program must remain fully operable without GSAP;
- repeated open-close across different cards must not leave body/dialog/card
  state locked;
- long ID/EN/AR titles must preserve the same semantic controls.

## 6. Values

Source owners:

- `resources/js/surfaces/home/values/controller.js`;
- `resources/js/surfaces/home/values/lifecycle.js`.

Values has no primary click action; its critical behavior is scroll-driven
presentation.

Lifecycle contract:

- scroll/resize schedule rendering;
- IntersectionObserver controls active state;
- visibility hidden cancels frame work and suspends spatial enhancement;
- pageshow/pagehide resume/suspend state;
- reduced-motion or unsupported capability disables enhancement and clears
  enhanced state;
- cleanup removes lifecycle listeners/observers;
- static semantic content remains the valid degraded state.

Values Spatial is currently disabled by runtime flag and therefore is not a
required active user interaction for the current runtime checkpoint.

## 7. Vision/Mission

Source owner: `resources/js/pages/welcome-vision-story.js` plus Vision controller.

Contract:

- semantic/static Vision/Mission renders without controller readiness;
- enhancement is deferred until after Hero presentation / idle scheduling;
- reduced-motion skips enhancement;
- failed controller import removes the motion-capable class rather than hiding
  content;
- later hardening may change preparation timing, but must preserve static-first
  semantic availability.

## 8. Homepage Gallery

Source owners:

- `resources/views/home/sections/gallery-depth.blade.php`;
- `resources/js/pages/welcome-depth-gallery.js`;
- `resources/js/surfaces/home/gallery-depth/controller.js`;
- `resources/js/components/gallery-route-transition.js`.

Primary contract:

- semantic fallback list exists before WebGL readiness;
- Gallery enhancement currently loads by proximity and may remain fallback;
- reduced-motion does not initialize WebGL enhancement;
- offscreen/hidden state stops engine work;
- `pagehide` stops or destroys based on BFCache persistence;
- `pageshow` can resume an active in-view engine;
- engine/init failure returns to functional fallback.

End CTA contract:

- CTA remains a real anchor to Gallery page;
- cinematic route exit only intercepts a plain primary click when Gallery is
  active and end-ready;
- modifier click, non-primary click, reduced-motion, unavailable WAAPI or
  non-ready state falls through to normal anchor navigation;
- transition failure still navigates;
- arrival animation is optional and consumes a session-storage marker once.

H1 slow-forward/reverse runtime acceptance remains separately pending and must
not be inferred PASS from this source matrix.

## 9. Gallery page

Source owners:

- `app/Http/Controllers/GalleryPageController.php`;
- `resources/views/pages/galeri.blade.php`;
- `resources/views/pages/partials/gallery-wall-card.blade.php`;
- `resources/js/pages/welcome/gallery-wall.js`.

Primary actions:

- cards are keyboard-focusable `role=button` controls;
- click, Enter, or Space opens the media lightbox;
- image opens as image; approved video embed opens in iframe;
- close button/backdrop closes;
- Escape closes;
- focus returns to the card/control that opened the lightbox;
- body scroll lock is removed on close;
- public controller rejects unsafe/unapproved media URLs and limits video hosts/
  embed paths.

Certification gap:

- source currently focuses the close button but no explicit dialog focus trap is
  proven. H7 must test keyboard escape/focus containment and either prove the
  current semantics sufficient or create a bounded accessibility fix.

## 10. Homepage Article story

Source owners:

- `resources/views/home/sections/articles.blade.php`;
- `resources/js/surfaces/home/article-story/controller.js`.

Contract:

- semantic article links exist independently of cinematic motion;
- cinematic horizontal/roll motion only enables at desktop >=1280px and when
  reduced-motion is off;
- IntersectionObserver limits active render scheduling;
- scroll/resize/pageshow/visibility changes request/recompute frames;
- final CTA becomes keyboard-focusable only when its cinematic state is ready;
- disabling enhancement resets transforms and CTA tabindex;
- disposer removes registered listeners/observer, though current bootstrap does
  not retain that disposer and H3/H4/H7 must prove non-accumulation.

## 11. Article index + article navigation

Source owners:

- `app/Http/Controllers/ArticlePageController.php`;
- `resources/views/pages/artikel.blade.php`;
- `resources/js/pages/welcome/public-content.js`.

Primary actions:

- server-side category links use `?kategori=` and case-insensitive tag matching;
- search input filters currently rendered article cards client-side;
- native articles navigate to local slug routes;
- external approved articles open in a new tab with `noopener`;
- unsafe external/internal-admin-like article URLs are rejected by public URL
  normalization.

Release contract:

- category + locale combinations must preserve correct content and links;
- search must remain keyboard/input-driven with no navigation requirement;
- empty-category and no-article states remain usable semantic output.

## 12. PPDB public flow

Source owners:

- `app/Http/Controllers/PpdbPageController.php`;
- `app/Services/PpdbAccess.php`;
- `resources/views/pages/ppdb/showcase.blade.php`;
- `resources/js/pages/ppdb-journey.js`.

Availability contract:

- active registration renders PPDB page;
- inactive registration intentionally renders `ppdb-closed` with HTTP 404;
- PPDB settings remain the authority for public registration/information URLs.

Journey contract:

- enhanced journey only activates at >=901px with reduced-motion off;
- smaller/reduced-motion modes remain non-pinned normal document flow;
- audience buttons switch parent/school panels;
- disabled audience tab cannot activate an empty audience;
- when the enhanced stage is pinned, vertical wheel and keyboard
  ArrowUp/ArrowDown/PageUp/PageDown/Space advance/reverse journey state;
- wheel is not consumed for horizontal-dominant gesture or ctrl/meta zoom;
- input is attached only while pinned and detached when released;
- journey eventually releases normal page scrolling;
- CTA remains an ordinary anchor to `#alur-ppdb`.

Certification gap:

- buttons use ARIA `role=tab`, but source does not prove roving-focus or
  ArrowLeft/ArrowRight tab navigation. H7 must test the current keyboard
  experience and make only a bounded accessibility correction if required.

## 13. Authentication/session routes

Source owner: `routes/web/auth.php`.

Release-critical contract:

- guest chooser plus admin/guru/murid login routes remain reachable;
- Google OAuth redirect/callback routes remain throttled;
- authenticated `/dashboard` redirects by account role;
- logout is POST;
- active-account and active-session middleware protect authenticated flows;
- role middleware protects admin/guru/murid destinations.

H2-H6 must not accidentally change these route/session semantics.

## 14. Admin media/content mutation surface

Route owners include:

- `routes/admin/gallery.php`;
- `routes/admin/articles.php`;
- `routes/admin/ppdb.php`;
- related protected admin routes.

H5/H6 must preserve these user outcomes:

- Gallery item create/show/edit/update/delete/restore/publish-toggle/reorder;
- Gallery page-section create/show/edit/update/delete/restore/toggle;
- Gallery section-media create/show/edit/update/delete/restore/toggle;
- Article create/edit/update/delete/restore;
- Article Canvas start/autosave/image upload/Unsplash search/publish;
- PPDB settings update/toggle;
- PPDB showcase create/edit/update/delete/restore/reorder.

All admin routes are inside auth + active-account + active-session + admin +
admin-locale middleware. H6 media migration must change storage ownership without
silently changing the existing admin CRUD outcome or authorization boundary.

## Cross-flow proof matrix for H7

Every release-critical interactive surface must be exercised where applicable
under these conditions:

| Condition | Required proof |
|---|---|
| Pointer/click | primary action reaches correct semantic outcome |
| Touch | no hover dependency; gesture does not block native action |
| Keyboard | Tab/focus, Enter/Space where control-like, Escape for dialogs, documented arrows |
| Repeat | open-close/open-close or activate-deactivate does not accumulate state |
| Fast forward scroll | no blank/fallback corruption/handoff skip |
| Reverse scroll | prepared/enhanced state restores deterministically |
| Resize/orientation | state and geometry reconcile without duplicate listeners |
| Reduced motion | semantic/static behavior remains complete |
| Delayed/failed enhancement | primary content/action remains usable |
| Hidden/visible tab | continuous runtime stops where appropriate and resumes safely |
| BFCache | pagehide/pageshow do not duplicate runtime ownership |
| Locale ID/EN/AR | action semantics and routes remain equivalent; AR RTL remains usable |
| Chromium/WebKit | no engine-specific interaction regression |

## D4 conclusion

The release-critical functional surface is now bounded. H2-H7 do not need a
fresh route/control rediscovery before implementation. The remaining work is not
more interaction cataloging; it is D5 proof-contract completion and D6 freezing
baseline + executable H2-H7 packets.
