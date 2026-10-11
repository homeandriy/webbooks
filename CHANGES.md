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
