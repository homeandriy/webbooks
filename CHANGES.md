## 2.1.10 / 2026-10-15
- Portfolio: load the native cookie-consent interface on the portfolio template, keep its visual styling isolated from the legacy portfolio stylesheet, and retain the consent choice controls.
- Assets: preserve the portfolio Vite entry as an ES module through common JavaScript optimizers, preventing module-import errors and restoring consent interactions.

## 2.1.9 / 2026-10-13
- Privacy: replace Cookie Law Info with a lightweight native consent flow that blocks known optional analytics and advertising scripts until an explicit choice; visitors can revise that choice from the sidebar.
- Interface: add a compact, keyboard-accessible cookie-settings control below the sidebar copyright and localize its consent interface for Ukrainian, English, Polish and Russian.

## 2.1.8 / 2026-10-13
- Frontend: complete the native, importable JavaScript migration, preserve the `type="module"` fallback runtime after optimizer rewrites, and harden search, download and comment flows with bounded input, nonces and safer output handling.
- Mobile: fix the offcanvas scroll, modal/search layout shifts and map containment; improve focused search feedback and translated minimum-query guidance.
- Typography and assets: keep only the self-hosted Inter variable font as the critical preload; remove the unused Sladkoeshka font and replace the legacy wordmark with an accessible inline SVG book logo and reduced-motion-safe hover interaction.
- Homepage: compact the action-to-content spacing and replace repetitive catalogue/article labels with concise, localized editorial copy.
- Tooling: make ESLint fall back to the supported Node 22 Docker image, add PHP coding-standard tooling, update Composer development dependencies and keep Vite font assets individually named for safe preloading.

## 2.1.7 / 2026-10-12
- Support links: add an accessible heart-beat interaction to the header, sidebar and portfolio navigation; the header icon has a localized tooltip, and mobile uses a quieter five-second pulse.
- Sidebar: reduce navigation-label size and keep the project-support label on one line.
- Search: centralize client/server limits, provide visible minimum-query feedback in desktop and mobile search, and retain the existing accessible live status.

## 2.1.6 / 2026-10-12
- Frontend: replace the jQuery runtime with importable native ES modules for AJAX, search, downloads and the portfolio; remove the jQuery dependency and legacy adapters.
- CSS: split page and language-switcher rules from `style.css` into focused asset modules, introduce semantic design tokens, and remove obsolete Pace, preloader, legacy countdown and Zoom-gallery rules.
- Assets: remove unused Materialize and Material Design Icons stylesheets; migrate remaining inactive Bootstrap 3 visibility selectors to Bootstrap 5 utilities.

## 2.1.5 / 2026-10-12
- Books: promote the purchase link to a larger, accessible primary action with a clear focus state and secure external-link handling.

## 2.1.4 / 2026-10-12
- Homepage: remove redundant English promotional copy; keep concise Ukrainian section headings and links to all books and articles.
- Articles: restore the desktop and tablet two-column reading layout with a 75% content column and 25% contextual sidebar.

## 2.1.3 / 2026-10-12
- Search: restore desktop autocomplete visibility with Bootstrap 5 and start both client- and server-side suggestions at three characters.
- Navigation: populate the “Read books” menu from the same localized category tree as the sidebar instead of obsolete hard-coded slugs.
- Homepage: present the six newest books and six newest articles in separate responsive sections with links to their archives.
- Empty states: add clear, actionable states for empty home feeds and book/article categories.
- Support page: theme verification now leaves existing author-support pages, including their publication status, content and template, untouched.

## 2.1.2 / 2026-10-12
- Typography: replace the incorrectly mapped M.Video/Questrial font with a locally bundled Inter variable font that includes Ukrainian Cyrillic; remove external Google Fonts requests and use `font-display: swap`.
- Articles: remove the redundant gallery from article pages, keep related materials in the main content flow, exclude the current post and restore the global post state after the related query.
- Mobile UX: focus the search field when its dialog opens, improve the dialog and search-result surface, and make the sidebar offcanvas independently scrollable.
- Navigation: align the signed-in account label and restore the multilingual Andrii Beznosko portfolio link.

## 2.1.1 / 2026-10-12
- Sidebar: restore the established Books and Articles category roots when a localized production taxonomy has no configured `books`/`articles` slug, while retaining Polylang resolution.
- Sidebar: restore the default CityHost partner banner when no custom banner values are configured.
- Header: preserve the historical dark logo panel and light logo text even if CSS custom properties are unavailable.

## 2.1.0 / 2026-10-12
- Release: package the theme exclusively under the `webbooks/` directory, matching `wp-content/themes/webbooks/` so WordPress replaces the installed theme instead of adding a second one.
- Validation: reject a release archive unless it has exactly that one root directory, includes the Vite manifest and compiled CSS/JS, and its packaged `style.css` version matches the theme version.

## 2.0.10 / 2026-10-12
- Build: Docker fallback now bind-mounts the repository at `/workspace`, verifies that `dist/` contains the Vite manifest plus JavaScript and CSS, and confirms that the output is readable from the host project directory.
- Cache: every successful Vite build writes a new `dist/.vite/build-tag`; enqueued Vite CSS and JavaScript use this tag as their cache version even when asset content hashes do not change.
- Release: packaging requires and includes the build tag, preventing a release ZIP from containing stale or incomplete generated assets.
- Verification: the 2.0.10 release bundle contains the freshly generated Vite CSS/JS and manifest, with no Slick Carousel or Fancybox references.

## 2.0.9 / 2026-10-12
- Merge: reconcile main with the 1.9.6 master foundation, preserving author-support payments, Telegram support, Polylang REST sync, editorial article cards, progressive loading and code highlighting from 2.0.8.
- Frontend: retain native Bootstrap 5.3.8, working Offcanvas, two-column catalogs, Swiper Zoom and static 404; remove Slick/Fancybox.
- Security/performance: retain bounded search, optional ACF metadata, comment cooldowns, protected downloads, atomic counters and related-post caching.
- Packaging: deliver one webbooks-theme-release.zip and synchronize main/master history.

## 1.9.6 / 2026-10-12
- Performance: cache related-post IDs for ten minutes through WordPress transients, including installations without persistent object caching; invalidate on post updates/deletion and taxonomy changes, then hydrate only currently public posts.
- Packaging: use webbooks-theme-release.zip as the default archive name and remove obsolete local release ZIPs.

## 1.9.5 / 2026-10-12
- Release: package the complete classic theme with Bootstrap 5.3.8, Swiper carousels/Zoom, default covers and cleaned runtime assets in one ZIP.
- Catalog: retain two columns from 768px upward, including HD and Full HD, and one column on narrow phones; preserve this layout after AJAX filtering.
- Workflow: update README Stable tag automatically alongside the three theme version constants/headers during npm run theme.
- Validation: JavaScript lint, eight UI tests, 34 WordPress/PHP 8.5 integration checks and responsive browser verification.

## 1.9.4 / 2026-10-12
- Catalog: omit the author row when no author is provided, removing empty metadata and redundant table dividers.
- Bootstrap: replace the embedded Bootstrap 3 stylesheet with Bootstrap 5.3.8 from npm/Vite; migrate grid breakpoints, utilities, cards, form selects, dropdowns, tab triggers, modal controls and pagination.
- Layout: preserve classic typography, colors, spacing and responsive thresholds; remove manual JavaScript column switching, height arithmetic and sliding-sidebar code.
- Catalog: enforce two columns from 768px through HD/Full HD and wider screens in homepage, categories, archives, search and all-post lists; one column on narrow phones. Keep the same row wrapper for AJAX results.
- Navigation: native responsive offcanvas with backdrop, focus handling, Escape and explicitly targeted close button; remove legacy data-attribute/state adapters.
- Assets: remove unused Glyphicons and provide synchronized local Bootstrap 5 backups for missing-manifest fallback.
- Validation: eight JavaScript tests including actual responsive-offcanvas dismissal, WordPress/PHP 8.5 integration and browser checks of desktop/mobile layouts and native controls.

## 1.9.3 / 2026-10-12
- Catalog buttons: center the More label vertically and horizontally, use a stronger font weight and remove header-specific button styling.
- Navigation: align sidebar icons and widget content, reduce initial menu spacing, remove duplicate sidebar search, compact catalog action buttons and keep expanded panels visible with Bootstrap 5.
- Covers: generated a lightweight WebP default book cover; use it for missing images in the catalog, search, carousel, galleries, related books, downloads and book social previews.
- Carousels: replace Roller with Swiper 14.3.0, shared linked cards, navigation and pausable autoplay; honor reduced motion.
- Gallery: replace Fancybox/Slick with Swiper thumbnails, native Zoom and an accessible expanded dialog; preserve ACF/meta/featured-image sources.
- Bootstrap: import only Bootstrap 5.3.8 JS components through Vite; adapt existing theme markup/states and remove standalone Bootstrap 3 assets. The theme's existing layout CSS remains.
- Assets: recursively enqueue CSS from shared Vite imports and deduplicate stylesheet handles; remove obsolete vendor CSS, fonts and sprites.
- Validation: 7 JavaScript tests and 30 isolated WordPress/PHP 8.5 checks; desktop/mobile gallery, Zoom, thumbnails, dialog/Escape, tabs, modals and category navigation verified in the browser.

## 1.9.2 / 2026-10-12
- JavaScript: remove 13 unreferenced vendor/legacy files (399,652 bytes), the unloaded legacy adapter's no-op callers and obsolete ESLint entries.
- Dependencies: document active callers, local versions, verified upstream releases and migration limits in JS_DEPENDENCIES.txt; retain gallery, catalog and portfolio dependencies.
- Validation: JavaScript lint, seven UI regression tests and production Vite build passed after cleanup.

## 1.9.1 / 2026-10-12
- Release: build and package the catalog UX, search, accessibility and 404 improvements.
- Search: prevent late filter responses from replacing a newer search URL; restore the filter panel from shared URLs.
- Mobile: fix Bootstrap clearfix interference with the card grid; hide empty format rows.
- Localization: translate new search feedback and filter instructions into Ukrainian, Polish and Russian.
- Validation: seven JavaScript regression tests, 28 isolated WordPress checks, PHP syntax, lint and responsive browser checks.

## 1.9.0 / 2026-10-12
- 404: centered responsive book illustration, expressive localized recovery copy and homepage link; no sidebar widgets or post recommendations. Verified HTTP 404 and desktop/mobile layout.
- Search: repeatable filters, stale-response guards, network/JSON failure recovery, retry and expired-session reload; preserve results during loading.
- Navigation: optional independent filters, reset, shareable URL state and Back/Forward restoration; explicit short searches.
- Accessibility: named search controls, instructions, live counts, busy states, visible keyboard focus and reduced-motion scrolling/autoplay.
- Catalog: linked uncropped covers, neutral SVG fallback, compact author/language/format metadata and responsive mobile cards; full metadata retained on book pages.
- Performance: removed unused bxSlider implementation and layout-thrashing card/body height measurements; retain libraries with verified callers.
- Validation: client-side regression tests and isolated WordPress catalog checks.

## 1.8.11 / 2026-10-12
- 404: centered book illustration and clear recovery message; image above text on mobile, illustration and text side by side on desktop; accessible homepage link and localized copy. Removed post recommendations and sidebar widgets from the error page.

## 1.8.10 / 2026-10-12
- Structure: all live images/portfolio resources under assets/; HTML designs in reference/html/ and their CSS in assets/css/reference/, excluded from release packages. Replaced legacy zoom with native pointer/CSS zoom after browser QA.
- Search: SQL author/title matching for books, independent article search and accurate totals; removed stale HTML transients; bounded and normalized request parameters.
- Architecture: organized book metadata, search, downloads, REST policy and comment protection into internal src/ services and inc/ controllers; everything ships in one classic theme bundle.
- Security: escaped repeatable galleries, optional and typed ACF values, protected/public-post checks for downloads and statistics, safe HTTP(S) download URLs, IP cooldown for pending comments, pre-pagination language filtering with legacy comment retention.
- Statistics: atomic database counters with hashed visitor deduplication, legacy counter migration and scheduled cleanup.
- Frontend: one WordPress jQuery instance, separate gallery chunk, native responsive images, conditional reCAPTCHA, supported async ads strategy and MonsterInsights analytics deduplication.
- WordPress: lifecycle hooks, wp_body_open, the_content filters, wp_robots, SEO-plugin schema coordination, native post template registration and zoomable viewport; Gutenberg remains optional.
- Settings: configurable category roots, download page, report form, partner banner, verification and ad identifiers; portfolio assets selected by template.
- Performance: cache related IDs with post/term invalidation, fresh publication checks and no random SQL sorting or unnecessary totals.
- Tooling: Node >=22.13, Vite 8, ESLint 10, dependency audit, an allowlisted theme ZIP and release-note validation before version mutation.
- Validation: isolated WordPress 7.1.3 / PHP 8.5 runner and regression scenarios; documented staging checks for the actual site plugin stack including ACF Pro 6.8.0.1.
## 1.8.9 / 2026-07-17
- Analytics: replaced inactive Universal Analytics (analytics.js / UA-*) with opt-in GA4 (gtag.js). Configure WEBBOOKS_GA4_MEASUREMENT_ID in wp-config.php or use the webbooks_ga4_measurement_id filter.
- Refactor: extracted the portfolio page ID to WEBBOOKS_PORTFOLIO_PAGE_ID so its asset, font, and external-service exclusions are configured in one place.

## 1.8.8 / 2026-07-17
- Security: download AJAX now rejects missing, private, draft, and otherwise non-public posts before reading download metadata; external download links use noopener noreferrer.
- WordPress: hardened the download template's request handling and output escaping; related-post query input is normalized and global post data is reset.
- Admin: migrated theme-option writes to a typed Settings API registration with a sanitization callback and accessible form labels.
- Frontend: filter checkboxes now use their live DOM state instead of the initial HTML attribute.
- Tooling: declared the GPL-2.0-or-later Composer license.

## 1.8.1 / 2026-04-16
- Refactor: renamed constants to project-namespaced identifiers — `GENERAL_NONCE` → `WEBBOOKS_AJAX_NONCE` (value `myajax-nonce` → `webbooks-request-nonce`), `DOWNLOAD_BOOK_NONCE` → `WEBBOOKS_DOWNLOAD_NONCE` (value `download_book_nonce` → `webbooks-download-nonce`). Updated all usages in `inc/ajax/search.php`, `src/Book/DownloadLinks.php`, `inc/assets.php`.
- Refactor: renamed JS global objects — `php_array` → `webbooksConfig`, `php_arrayload` → `webbooksLoader`, `js_attributes` → `webbooksAjax`. Updated `inc/assets.php` (wp_localize_script), `assets/js/custom.js`, `assets/js/functions.js`, `assets/js/load.js`, `assets/js/ajax-filter.js`, `eslint.config.js`.

## 1.8.0 / 2026-04-16
- Build: `scripts/theme-release.sh` — `bump_patch` now implements carry-at-10 versioning: each component counts 0–10; when it exceeds 10 it resets to 0 and the next component increments (e.g. `X.Y.10` → `X.(Y+1).0`, `X.10.10` → `(X+1).0.0`). Version `1.7.12` → `1.8.0` is the first bump under the new scheme (patch 12+1=13 > 10 carries into minor).

## 1.7.12 / 2026-04-16
- Fix: invalid CSS selector `. table > tfoot > tr > td` (stray space after dot) in `style.css` line 2888 — caused a `css-syntax-error` warning in Vite build output.

## 1.7.11 / 2026-04-16
- Release: auto-bump via `npm run theme`; produced `webbooks-theme-release.zip` with updated i18n catalogs and production Vite build.

## 1.7.10 / 2026-04-16
- Fix: `scripts/theme-release.sh` `perl` patterns for `style.css` now use `^` anchor + `/m` flag to prevent stripping leading whitespace from `* Version:` and `* Template Version:` lines on each bump.
- Fix: `scripts/check-version-sync.sh` `sed` patterns now accept optional leading whitespace (`[[:space:]]*\*`) so the sync check works regardless of indentation style in `style.css`.
- Fix: stray leading space before `* Version:` / `* Template Version:` in `style.css` header (introduced during manual 1.7.9 bump) removed.

## 1.7.9 / 2026-04-16
- Fix: `category_query` — meta_query conditions for `complexity` and `language` are now skipped when filter values are empty; previously always filtering `= ''` returned zero results when no filter was selected.
- Fix: pagination broken — `$paged` was always 1 because `$_REQUEST['var']` is a JSON string, not an array; paged is now read from the already-decoded payload in `main_search_on_site`.
- Fix: cache key for `category_query` now includes the `selectToLink` flag to prevent wrong cached HTML being served.
- Feature: `selectToLink` flag is now passed to `template-parts/cards/book-card` via template args; when active, the "More" button is replaced with a direct download link.

## 1.6.7 / 2026-04-14
- Release: додано `scripts/theme-release.sh` для автоматичного patch bump, sync версій, i18n/update, build і ZIP-пакування.
- Release: `npm run theme` тепер є єдиною командою релізу та обовʼязково вимагає оновлення `CHANGES.md` і `README.txt` для нової версії.
- Docs: зафіксовано правило, що `webbooks-bundle` має підключатись як `type="module"`.
- Docs: уточнено, що заборонено глобальний rewrite до `type="text/javascript"` для всіх скриптів.
- Docs: додано рекомендацію додавати виняток у CDN/optimizer саме для `webbooks-bundle-js` (та суміжних handle/prefix за потреби).

## 1.6.0 / 2026-04-14
- SEO: покращено метадані та технічну індексацію шаблонів теми.
- Search UX: оновлено поведінку пошуку для більш передбачуваної взаємодії.
- Language switcher: додано/оновлено перемикач мов у публічному інтерфейсі.
- Build pipeline: стабілізовано збірку та релізний процес (включно з `dist` і Vite manifest).
- Comments security: посилено безпеку обробки коментарів і пов’язаного введення.
- Docs: оновлено `README.md` (системні вимоги, локальна збірка, ZIP release, fallback, i18n workflow).

## 1.5.2 / 2025-03-09
- Fixed: `add_filter('post_gallery', 'get_image_gallery', 10, 1)`.
- Add tag for git.

## 1.5.1 / 2025-02-24
- Sync theme from remote to repository.

## 1.5.0 / 2024-11-21
- Update CityHost Ad.

## 1.4.9 / 2024-09-12
- Remove `loading="lazy"` from gallery images.

## 1.4.8 / 2024-09-12
- Add `text-white` to all pages.
- Move `function.js` to `js` folder.
- Remove unused files.

## 1.4.7 / 2024-09-12
- Fix HTML structure on many pages.
- Add `loading="lazy"` to images.

## 1.4.6 / 2024-09-12
- Fix HTML structure on download page.

## 1.4.5 / 2024-09-12
- Fix download books.

## 1.4.4 / 2024-09-12
- Fix download books.
- Fix token validation.

## 1.4.3 / 2024-09-12
- Fix download books.

## 1.4.2 / 2024-09-12
- Fix count error in `functions.php`.

## 1.4.1 / 2024-03-05
- Disable users API endpoint.

## 1.4.0 / 2023-11-03
- Add partners banner.

## 1.3.8 / 2023-11-03
- Fix slick slider buttons.
- Fix `single.php` related output.

## 1.3.7 / 2023-11-03
- Translate portfolio into Ukrainian.
- Update links.
- Add related posts in `single.php`.

## 1.3.6 / 2023-11-03
- Fix `style.css` tags.
- Remove header block.
- Add margin and padding.

## 1.3.5 / 2023-07-02
- Fix `style.css` tags.

## 1.3.4 / 2023-07-02
- Fix HTML.
- Delete Rambler.

## 1.3.3 / 2023-07-02
- Add tag template version.
- Update version.

## 1.3.2 / 2023-07-02
- Fix portfolio.
- Fix semantic HTML.
- Fix `single-post-type.php`.
- Update version.

## 1.3.1 / 2023-06-30
- Fix theme in `style.css`.

## 1.2.1 / 2023-06-30
- Fix JS.

## 1.2.0 / 2023-06-30
- Some fixes.

## 1.1.0 / 2020-05-24
- Init git.


## Історія релізів гілки main до об’єднання
## 2.0.8 / 2026-10-11
- Archive cards: use a two-column layout on desktop for standard article cards, improving readability while retaining the single-column mobile layout.
- 404 page: align the heading and recommended-content grid with the archive layout, including the shared responsive row structure.

## 2.0.7 / 2026-10-11
- Article archives: regular news posts now use a dedicated editorial card with a 16:9 image, cleaned excerpt, title, and localized Read more action; downloadable books keep their existing metadata cards.
- Content cleanup: legacy article previews now remove caption shortcodes and imported Markdown fragments. Added a local 16:9 news-image fallback for posts without a featured image.
- Archive UI: refreshed category and tag headings and removed the obsolete “Browse by categories” control from archive and 404 templates.
- Layout: removed the unused sidebar menu top offset and extended the author-support backdrop through the full page shell.

## 2.0.6 / 2026-10-11
- Layout: restored a single viewport-height owner in the legacy shell. The wrapper now accounts for the header height with a dynamic-viewport fallback, while the content section inherits that height instead of creating a second `100vh` region.
- Performance: load the Google reCAPTCHA API only for singular content with open comments, and place its initial asynchronous loader in the document head. It no longer runs on the author-support page.

## 2.0.5 / 2026-10-11
- Author support: polished the desktop and mobile support layout to remove the nested-scroll/blank-strip issue, and added dedicated, responsive Theme Settings styling for payment controls.
- Payments: Theme Settings now support validated Monobank jar and PrivatBank envelope URLs; filled values are returned only after an explicit, nonce-protected request and appear as secure external payment links in the support dialog.
- Payments: added readable Visa, Mastercard, UnionPay, Monobank, PrivatBank, and generic payment-card SVG marks to the dialog while retaining deferred loading of payment data.

## 2.0.4 / 2026-10-11
- Author support: centered the payment-details dialog, disabled closing it by backdrop click or Escape, and added an explicit localized Close button alongside the header close control.
- Frontend: use the TypeIt package for the rotating support-message typing, cursor, hold, and erase sequence.

## 2.0.3 / 2026-10-11
- Author support: rebuilt the page as a centered two-column donation layout with an original book-and-donation illustration that moves above the content on small screens.
- Motivation: added 20 rotating, language-specific messages with a typewriter, hold, cursor, erase, and pause cycle; reduced-motion visitors receive a static message.

## 2.0.2 / 2026-10-11
- Author support: redesigned the support page as a responsive hero and action panel with clearer hierarchy, stronger primary action, and phone-specific layout.

## 2.0.1 / 2026-10-11
- Navigation: show the localized author-support link in the sidebar, portfolio navigation, and desktop header only when its current-language page is published.
- Performance: cache each language-specific support-page availability result for two hours and invalidate that language cache when the page status changes.
- UI: added a dashed-underlined sidebar support link and a compact heart icon in the desktop header.

## 2.0.0 / 2026-10-11
- Author support: the Ukrainian, Russian, and Polish support pages are now created as drafts. The next theme activation runs a one-time migration that also changes the already-created three pages to drafts, while preserving their content, slugs, templates, and Polylang relationships.

## 1.10.10 / 2026-10-11
- Clipboard: replaced the theme-owned deprecated `document.execCommand('copy')` fallback with the `clipboard` npm dependency. The author-support module now uses ClipboardJS for the copy button and card-number focus action, including the separate Vite entry required by the portfolio page.

## 1.10.9 / 2026-10-11
- Author support: added a localized support page template plus an idempotent theme migration that creates the Ukrainian, Russian, and Polish pages by stable slug, assigns the template, and saves their Polylang translation group without overwriting existing page content.
- Theme settings: replaced unused legacy fields with author name, Telegram contact URL, and validated repeatable payment cards. Card numbers are formatted and checked with the Luhn (Mod 10) checksum before storage.
- Privacy: payment-card and Telegram values are absent from rendered public markup. They are returned only from nonce-protected AJAX endpoints after a visitor action; header and portfolio contact controls now use that deferred Telegram request.
- Support UI: added a responsive payment-details modal with card-type icons, focus/copy behavior, copied feedback, reduced-motion-safe CTA animation, and Ukrainian, Russian, Polish, and English catalog updates.

## 1.10.8 / 2026-10-10
- Release tooling: Docker build fallback now repairs ownership of generated `dist/` artifacts before and after Vite runs, so repeated builds and theme ZIP packaging do not fail with `EACCES`.

## 1.10.7 / 2026-10-10
- Reading experience: imported articles now use the full reading column on desktop instead of a narrow book-description layout.
- Code samples: added GitHub-dark syntax highlighting for semantic `pre > code.language-*` blocks, with horizontally scrollable, responsive code containers.
- Frontend: added `highlight.js` to the Vite bundle for client-side code highlighting.
- Security: updated transitive frontend build dependencies to resolve the npm audit findings in `brace-expansion`, `nanoid`, `postcss`, and `source-map-js`.
- Polylang: added an authenticated REST bridge for publication integrations to assign languages and persist complete translation groups with per-post capability checks.

## 1.10.6 / 2026-09-29
- Telegram support: added the private WebBooks support-bot bridge, including a protected webhook, secure group setup, rate limiting, and two-way forwarding between visitors and the internal support group.
- Contact links: site header and portfolio now open `@webbooks_support_bot` with source-specific start parameters instead of the obsolete Telegram contact URL.
- Portfolio: added a prominent, accessible Telegram call-to-action with an icon, hover feedback, and a subtle shimmer animation that respects reduced-motion preferences.

## 1.10.3 / 2026-07-18
- Polylang: localized custom queries, search, special pages, navigation, portfolio links, structured data, and AJAX responses; added a repeatable SQL migration for translated special pages and language-specific menus.
- Localization: refreshed Ukrainian, Polish, Russian, and English theme catalogs and ensured language-aware catalog/search results.
- Reading experience: constrained long-form book descriptions to a readable editorial measure, with responsive type scale and vertical rhythm.
- CSS: converted theme-owned font sizes, margins, paddings, and spacing tokens from pixels to rem/clamp units; resolved custom-property and legacy-prefix analyzer warnings.
- Fix: portfolio asset loading now resolves the translated Polylang page ID (with a portfolio-template fallback), so Ukrainian and Polish portfolio pages load their dedicated CSS and JavaScript like the Russian source page.

## 1.10.2 / 2026-07-18
- All-post catalog: preserve the public `?page=N` parameter on the dedicated page template, render 12 newest posts per page, and progressively append later pages through the Load more control.

## 1.10.1 / 2026-07-18
- Catalog, search, download links, and featured-gallery covers now retain the source 312:440 portrait ratio without cropping.
- Header controls and preview actions use semantic buttons; widened desktop search and refined dropdown control styling.

## 1.10.0 / 2026-07-18
- Responsive UI: moved the persistent sidebar and desktop header threshold to tablet width; book cards keep covers above content until wide desktop layouts.
- Book pages: added themed icon tabs and rebuilt the comments form with Bootstrap 5 controls.
- Markup: normalized reusable catalog, search, download, sidebar, category, and modal template markup; removed legacy inline HTML transformations from the filter form.

## 1.9.10 / 2026-07-18
- Responsive UI: completed the mobile-first layout pass for catalog cards, navigation, headers, forms, and pagination across phone, tablet, and desktop breakpoints.
- Comments: rebuilt the public comments form and thread markup with Bootstrap 5 components, accessible labels, isolated template parts, and a Vite-managed emoji picker.
- Templates: replaced legacy `my*` identifiers with descriptive Webbooks-specific IDs and applied the shared pagination component to comments.

## 1.9.8 / 2026-07-18
- Mobile navigation: replaced the partial sidebar slide-out with an accessible full-screen Offcanvas, including a centered brand header, dedicated close button, Escape support, scroll locking, and a safe responsive reset.
- Catalog: preserved book-cover proportions with `object-fit: contain` and aligned card heights across tablet and desktop rows.
- Mobile header: grouped search and language selection at the right edge while retaining the menu toggle at the left.

## 1.9.7 / 2026-07-17
- Search: pass the active Polylang language with every AJAX request, scope catalog and global results to that language, and load the matching theme translation catalog for AJAX markup.
- Search UX: correctly separate downloadable books from articles by file format; repaired responsive catalog/search grids (one card on mobile, two from tablet width).
- Download UX: repaired countdown status/error visibility and placed the numerical remaining-time label above the progress bar.
- UI: fixed desktop header search layout, book-detail tabs, language-switcher disclosure indicator, and full-height sidebar/content backgrounds.
- Frontend: fixed Slick's WordPress-jQuery bridge so the Vite bundle uses the WordPress-provided jQuery instance.

## 1.9.5 / 2026-07-17
- Architecture: migrated assets, AJAX, SEO, structured data, comment security, and theme setup from `inc/` into typed PSR-4 classes under `src/`.
- Frontend: moved `functions.js` and `ajax-filter.js` into the Vite entry; the production bundle now uses WordPress jQuery and no longer executes duplicate AJAX or compatibility scripts.
- Portfolio: removed obsolete Internet Explorer and jQuery-based navigation assets; replaced them with the native portfolio runtime.
- Build: fixed the Slick jQuery peer-dependency lockfile and release ZIP validation; local/development-only archive entries are now reported explicitly.
- Templates and localization: improved comments, downloads, pagination, book cards, portfolio language routing, and translation catalogs.

## 1.9.1 / 2026-07-17
- Frontend: upgraded the build toolchain to Vite 8 and ESLint 10; builds now require Node.js 20.19+ and automatically fall back to Docker Node 20 when needed.
- Frontend: upgraded Slick to 1.8.1, added a scoped JavaScript lint command, and removed deprecated Vite CommonJS configuration usage.
- Templates: extracted root-level header, footer, search-loop, options-page, and AJAX markup into dedicated template parts; improved modal and form accessibility.
- Localization: updated UI strings, language switcher, and generated Polish, Ukrainian, Russian, and English catalogs.
- CSS: restored the original theme palette as semantic custom properties, modernized layout styles, and removed obsolete browser-specific CSS prefixes.

## 1.8.10 / 2026-07-17
- Frontend: migrated the theme from Bootstrap 3.3.2 to Bootstrap 5.3.8, bundled through Vite without a jQuery dependency for Bootstrap components.
- Templates: updated modals, tabs, dropdowns, collapse controls, carousel markup, responsive utilities, and legacy grid classes to Bootstrap 5 APIs.
- CSS: removed the embedded Bootstrap 3 stylesheet and obsolete Bootstrap 3 assets; retained a small theme-owned compatibility layer for legacy `panel`, `thumbnail`, and form markup.
- SEO: removed duplicate `Book` Microdata from catalog cards; the canonical book structured data remains the JSON-LD generated on individual book pages.
- Performance: removed a conflicting local `Questrial` declaration and redundant stylesheet preload; Google Fonts is now loaded once with `display=swap` and preconnect hints.
- AJAX: unavailable book previews and view counters now return a structured `wp_send_json_error()` response with HTTP 404; download-link errors include a machine-readable message.

## 1.8.9 / 2026-07-17
- Analytics: replaced inactive Universal Analytics (analytics.js / UA-*) with opt-in GA4 (gtag.js). Configure WEBBOOKS_GA4_MEASUREMENT_ID in wp-config.php or use the webbooks_ga4_measurement_id filter.
- Refactor: extracted the portfolio page ID to WEBBOOKS_PORTFOLIO_PAGE_ID so its asset, font, and external-service exclusions are configured in one place.

## 1.8.8 / 2026-07-17
- Security: download AJAX now rejects missing, private, draft, and otherwise non-public posts before reading download metadata; external download links use noopener noreferrer.
- WordPress: hardened the download template's request handling and output escaping; related-post query input is normalized and global post data is reset.
- Admin: migrated theme-option writes to a typed Settings API registration with a sanitization callback and accessible form labels.
- Frontend: filter checkboxes now use their live DOM state instead of the initial HTML attribute.
- Tooling: declared the GPL-2.0-or-later Composer license.

## 1.8.1 / 2026-04-16
- Refactor: renamed constants to project-namespaced identifiers — `GENERAL_NONCE` → `WEBBOOKS_AJAX_NONCE` (value `myajax-nonce` → `webbooks-request-nonce`), `DOWNLOAD_BOOK_NONCE` → `WEBBOOKS_DOWNLOAD_NONCE` (value `download_book_nonce` → `webbooks-download-nonce`). Updated all usages in `inc/ajax/search.php`, `src/Book/DownloadLinks.php`, `inc/assets.php`.
- Refactor: renamed JS global objects — `php_array` → `webbooksConfig`, `php_arrayload` → `webbooksLoader`, `js_attributes` → `webbooksAjax`. Updated `inc/assets.php` (wp_localize_script), `assets/js/custom.js`, `assets/js/functions.js`, `assets/js/load.js`, `assets/js/ajax-filter.js`, `eslint.config.js`.

## 1.8.0 / 2026-04-16
- Build: `scripts/theme-release.sh` — `bump_patch` now implements carry-at-10 versioning: each component counts 0–10; when it exceeds 10 it resets to 0 and the next component increments (e.g. `X.Y.10` → `X.(Y+1).0`, `X.10.10` → `(X+1).0.0`). Version `1.7.12` → `1.8.0` is the first bump under the new scheme (patch 12+1=13 > 10 carries into minor).

## 1.7.12 / 2026-04-16
- Fix: invalid CSS selector `. table > tfoot > tr > td` (stray space after dot) in `style.css` line 2888 — caused a `css-syntax-error` warning in Vite build output.

## 1.7.11 / 2026-04-16
- Release: auto-bump via `npm run theme`; produced `webbooks-theme-release.zip` with updated i18n catalogs and production Vite build.

## 1.7.10 / 2026-04-16
- Fix: `scripts/theme-release.sh` `perl` patterns for `style.css` now use `^` anchor + `/m` flag to prevent stripping leading whitespace from `* Version:` and `* Template Version:` lines on each bump.
- Fix: `scripts/check-version-sync.sh` `sed` patterns now accept optional leading whitespace (`[[:space:]]*\*`) so the sync check works regardless of indentation style in `style.css`.
- Fix: stray leading space before `* Version:` / `* Template Version:` in `style.css` header (introduced during manual 1.7.9 bump) removed.

## 1.7.9 / 2026-04-16
- Fix: `category_query` — meta_query conditions for `complexity` and `language` are now skipped when filter values are empty; previously always filtering `= ''` returned zero results when no filter was selected.
- Fix: pagination broken — `$paged` was always 1 because `$_REQUEST['var']` is a JSON string, not an array; paged is now read from the already-decoded payload in `main_search_on_site`.
- Fix: cache key for `category_query` now includes the `selectToLink` flag to prevent wrong cached HTML being served.
- Feature: `selectToLink` flag is now passed to `template-parts/cards/book-card` via template args; when active, the "More" button is replaced with a direct download link.

## 1.6.7 / 2026-04-14
- Release: додано `scripts/theme-release.sh` для автоматичного patch bump, sync версій, i18n/update, build і ZIP-пакування.
- Release: `npm run theme` тепер є єдиною командою релізу та обовʼязково вимагає оновлення `CHANGES.md` і `README.txt` для нової версії.
- Docs: зафіксовано правило, що `webbooks-bundle` має підключатись як `type="module"`.
- Docs: уточнено, що заборонено глобальний rewrite до `type="text/javascript"` для всіх скриптів.
- Docs: додано рекомендацію додавати виняток у CDN/optimizer саме для `webbooks-bundle-js` (та суміжних handle/prefix за потреби).

## 1.6.0 / 2026-04-14
- SEO: покращено метадані та технічну індексацію шаблонів теми.
- Search UX: оновлено поведінку пошуку для більш передбачуваної взаємодії.
- Language switcher: додано/оновлено перемикач мов у публічному інтерфейсі.
- Build pipeline: стабілізовано збірку та релізний процес (включно з `dist` і Vite manifest).
- Comments security: посилено безпеку обробки коментарів і пов’язаного введення.
- Docs: оновлено `README.md` (системні вимоги, локальна збірка, ZIP release, fallback, i18n workflow).

## 1.5.2 / 2025-03-09
- Fixed: `add_filter('post_gallery', 'get_image_gallery', 10, 1)`.
- Add tag for git.

## 1.5.1 / 2025-02-24
- Sync theme from remote to repository.

## 1.5.0 / 2024-11-21
- Update CityHost Ad.

## 1.4.9 / 2024-09-12
- Remove `loading="lazy"` from gallery images.

## 1.4.8 / 2024-09-12
- Add `text-white` to all pages.
- Move `function.js` to `js` folder.
- Remove unused files.

## 1.4.7 / 2024-09-12
- Fix HTML structure on many pages.
- Add `loading="lazy"` to images.

## 1.4.6 / 2024-09-12
- Fix HTML structure on download page.

## 1.4.5 / 2024-09-12
- Fix download books.

## 1.4.4 / 2024-09-12
- Fix download books.
- Fix token validation.

## 1.4.3 / 2024-09-12
- Fix download books.

## 1.4.2 / 2024-09-12
- Fix count error in `functions.php`.

## 1.4.1 / 2024-03-05
- Disable users API endpoint.

## 1.4.0 / 2023-11-03
- Add partners banner.

## 1.3.8 / 2023-11-03
- Fix slick slider buttons.
- Fix `single.php` related output.

## 1.3.7 / 2023-11-03
- Translate portfolio into Ukrainian.
- Update links.
- Add related posts in `single.php`.

## 1.3.6 / 2023-11-03
- Fix `style.css` tags.
- Remove header block.
- Add margin and padding.

## 1.3.5 / 2023-07-02
- Fix `style.css` tags.

## 1.3.4 / 2023-07-02
- Fix HTML.
- Delete Rambler.

## 1.3.3 / 2023-07-02
- Add tag template version.
- Update version.

## 1.3.2 / 2023-07-02
- Fix portfolio.
- Fix semantic HTML.
- Fix `single-post-type.php`.
- Update version.

## 1.3.1 / 2023-06-30
- Fix theme in `style.css`.

## 1.2.1 / 2023-06-30
- Fix JS.

## 1.2.0 / 2023-06-30
- Some fixes.

## 1.1.0 / 2020-05-24
- Init git.
