=== Webbooks classic theme ===
Contributors: homeandriy
Tags: classic-theme, books, custom-fields, translation-ready
Requires at least: 6.5
Requires PHP: 8.2
Tested up to: 7.1.3
Stable tag: 2.1.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Classic WordPress theme for a book catalog and articles. Gutenberg and block themes are not required. Book data is stored in existing post metadata; ACF is optional for frontend rendering.
All functionality ships in one theme bundle: services in src/, controllers and settings in inc/, presentation in template-parts/. Runtime resources live in assets/; archived HTML drafts live in reference/html/ and are excluded from releases.

== Installation ==
1. Build the production assets and install the webbooks theme ZIP.
2. All site services are included in the theme ZIP; no separate Webbooks plugin is required.
3. In Settings > Webbooks configure book/article category IDs, the published download page using download.php, the Ninja Forms report ID and any optional ads/partner/verification values. No site-specific defaults are assumed.
4. Keep Classic Editor if the site uses TinyMCE. Article template is natively available for posts; existing Custom Post Templates assignments are retained.
5. For guest comments configure GC_V2_PUBLIC and GC_V2_SECRET in wp-config.php. Pending submissions consume the IP cooldown. Comments without a legacy language tag remain visible.
6. Analytics is disabled unless WEBBOOKS_GA4_MEASUREMENT_ID is configured. When MonsterInsights is active the theme does not add a second tracker. CookieYes consent configuration must be tested on staging.

== Build and verification ==
Use Node >=22.13: npm ci && npm run lint && npm run test:ui && npm run build.
Docker fallback: bash scripts/build-with-fallback.sh. Both local and Docker builds write dist/ directly into the project directory, verify the manifest plus CSS/JS outputs, and generate a fresh dist/.vite/build-tag used as the Vite CSS/JS cache version. Without local Node/npm, run bash scripts/theme-release.sh directly; npm run theme itself still requires the npm command to exist.
PHP/WordPress integration: WITH_PLUGINS=1 docker compose -p codex-webbooks-tests -f tests/compose.yml up --build --abort-on-container-exit --exit-code-from tests.
Package the current version: bash scripts/build-release.sh webbooks-theme-release.zip (requires gettext, zip and unzip).
Every version bump must update WEBBOOKS_VERSION, both CSS version headers, CHANGES.md and this changelog. The npm theme command checks release notes before modifying versions.
The ZIP root is always webbooks/, matching the only supported production directory wp-content/themes/webbooks/. WordPress can therefore replace the installed webbooks theme during an upload instead of adding a second theme.
See REGRESSION_CHECKLIST.md for test coverage, plugin limitations, deployment and rollback.

== Compatibility ==
Minimum supported PHP remains 8.2. The included runner targets PHP 8.5 and current WordPress. Tested third-party versions are recorded in REGRESSION_CHECKLIST.md.
ACF Pro 6.8.0.1 and the full production plugin configuration require staging verification; free ACF tests do not certify Pro-only gallery fields. S3_Service is an optional unfinished adapter, not a required integration.
Counters migrate _views_count lazily into dedicated tables. New counts are read through Webbooks\Statistics\Views::count(); custom integrations reading the old meta key must migrate. One IP/post/hour counts as one visit; no raw visitor IP is stored by this counter.

== Changelog ==

= 2.1.4 =
* Homepage: removes redundant English promotional copy and uses concise Ukrainian section labels and catalogue links.
* Articles: restores the 75/25 desktop and tablet reading layout with the contextual right sidebar.

= 2.1.3 =
* Search: desktop suggestions are visible with Bootstrap 5 and begin from three entered characters.
* Navigation: the “Read books” panel uses the same localized category tree as the sidebar.
* Homepage: shows six newest books and six newest articles in separate responsive sections.
* Empty states: categories and home feeds explain empty results and offer the next action.
* Support page: a theme release only verifies existing support pages and never resets an editor-published page to draft.

= 2.1.2 =
* Typography: ship Inter locally with Ukrainian Cyrillic coverage, remove the incorrect M.Video/Questrial mapping and eliminate external Google Fonts requests.
* Articles: remove the duplicate gallery, improve the reading layout and keep related articles in the main content flow.
* Mobile UX: focus the search input on dialog open, improve search surfaces and allow the offcanvas navigation to scroll independently.
* Navigation: align the signed-in account label and restore the multilingual Andrii Beznosko portfolio link.

= 2.1.1 =
* Compatibility: sidebar navigation retains the legacy Books/Articles roots and CityHost banner defaults when site settings are not configured.
* Header: the logo keeps its historical dark panel and light text if CSS custom properties are unavailable.

= 2.1.0 =
* Release: the ZIP always uses the webbooks/ root directory, matching wp-content/themes/webbooks/ for an in-place WordPress theme replacement.
* Validation: packaging checks the sole root directory, Vite manifest/CSS/JS and the version inside style.css before completing.

= 2.0.10 =
* Build: Docker fallback writes verified Vite output directly to the theme dist/ directory and generates a new cache build tag for every successful build, including content-identical CSS/JS builds.
* Release: theme packaging requires the verified build tag alongside the Vite manifest.
* Verification: the release archive includes the fresh Vite CSS/JS and manifest, without Slick Carousel or Fancybox references.

= 2.0.9 =
* Reconcile main/master using 1.9.6 as the foundation; preserve support payments, Telegram, Polylang REST sync, editorial news cards, progressive loading and code highlighting.
* Retain Bootstrap 5, Swiper Zoom, two-column catalogs, static 404 and security improvements; remove Slick/Fancybox.
* Package one complete webbooks-theme-release.zip.



= 1.9.6 =
* Performance: cache related-post IDs for ten minutes through WordPress transients, including installations without persistent object caching; invalidate on post updates/deletion and taxonomy changes, then hydrate only currently public posts.
* Packaging: use webbooks-theme-release.zip as the default archive name and remove obsolete local release ZIPs.


= 1.9.5 =
* Release the complete Bootstrap 5/Swiper theme bundle with cleaned assets, default covers and responsive two-column lists.
* Automatically synchronize Stable tag during npm run theme.
* Validated with eight UI tests and 34 WordPress/PHP 8.5 integration checks.

= 1.9.4 =
* Hide empty author rows in book cards to avoid redundant dividers.
Full Bootstrap 5.3.8 CSS/markup migration; preserve classic spacing and responsive thresholds, use native offcanvas, tabs, modals and pagination. Remove the Bootstrap 3 adapter, embedded framework CSS and Glyphicons. Local Bootstrap 5 backups support missing-manifest fallback.
Catalog, news, category, archive and search lists keep two columns from 768px upward, including HD and Full HD; narrow phones use one column.

= 1.9.3 =
* Aligned sidebar navigation and widgets, removed duplicate search, compacted catalog buttons and fixed disappearing expanded panels.
* Generated default book cover for books without uploaded artwork across catalog, search, galleries and downloads.
* Swiper carousels and book gallery with thumbnails, Zoom and expanded dialog.
* Bootstrap 5 JS components via Vite with existing theme layout styling preserved.
* Removed Roller, Fancybox, Slick and standalone Bootstrap 3 assets; load shared chunk CSS.

= 1.9.2 =
* Removed 13 unreferenced JavaScript files and dead legacy adapter callers.
* Audited active libraries and upstream versions; see JS_DEPENDENCIES.txt in the source repository.
* Verified JavaScript lint, seven UI regressions and the production build.

= 1.9.1 =
* Release package with catalog UX, search, accessibility and 404 improvements.
* Fixed stale filter responses, shared URL restoration and the mobile card grid.
* Updated search translations; verified seven JS regressions and 28 WordPress checks.

= 1.9.0 =
* Centered responsive 404 with localized copy, book illustration and a clear homepage link; removed post recommendations.
* Reliable repeatable catalog filtering, visible errors/retry and expired-session recovery.
* Optional filters, reset, shareable search/filter URLs and browser history restoration.
* Accessible search labels, live result counts, short explicit searches and keyboard focus.
* Compact linked book covers, neutral SVG placeholders and mobile layouts.
* Reduced-motion scrolling and carousel autoplay; removed unused bxSlider and JS height measurement.

= 1.8.11 =
* Redesigned 404 with a centered responsive book illustration, clear recovery message and homepage link; removed post recommendations.

= 1.8.10 =
* Organized runtime assets and archived HTML references; fixed browser-discovered scrollbar/module zoom errors.
* Complete author search and modular services within one theme bundle.
* Safe ACF/galleries/downloads, atomic deduplicated views, pending-comment cooldown and language-aware pagination.
* Single jQuery, conditional gallery/reCAPTCHA, native images, cache invalidation and modern classic-theme hooks.
* Configurable site identifiers, native post templates, SEO/analytics deduplication and allowlisted releases.
* Updated build dependencies, PHP 8.5 integration runner and staging/rollback documentation.
= 1.8.9 =
* Analytics: replaced Universal Analytics with opt-in GA4. Define WEBBOOKS_GA4_MEASUREMENT_ID in wp-config.php to enable it.
* Refactor: portfolio page ID is now the WEBBOOKS_PORTFOLIO_PAGE_ID constant.

= 1.8.8 =
* Security: download AJAX rejects unavailable posts, and download links now isolate the opened tab.
* WordPress: sanitized download-template request data and escaped rendered post metadata.
* Admin: settings are now sanitized on write through the WordPress Settings API.
* Frontend: filter checkboxes read the current checked state.

= 1.8.1 =
* Refactor: `GENERAL_NONCE` → `WEBBOOKS_AJAX_NONCE` (value `myajax-nonce` → `webbooks-request-nonce`), `DOWNLOAD_BOOK_NONCE` → `WEBBOOKS_DOWNLOAD_NONCE` (value `download_book_nonce` → `webbooks-download-nonce`).
* Refactor: JS globals `php_array` → `webbooksConfig`, `php_arrayload` → `webbooksLoader`, `js_attributes` → `webbooksAjax`.

= 1.8.0 =
* Build: `bump_patch` in `theme-release.sh` now uses carry-at-10 versioning — each component counts 0–10; exceeding 10 resets it to 0 and increments the next component (0.0.10 → 0.1.0, 0.10.10 → 1.0.0). First bump under new scheme: 1.7.12 → 1.8.0.

= 1.7.12 =
* Fix: invalid CSS selector `. table > tfoot > tr > td` (stray space after dot) in `style.css` — caused a css-syntax-error warning in Vite build.

= 1.7.11 =
* Release: auto-bump via `npm run theme`; produced release ZIP with updated i18n and production build.

= 1.7.10 =
* Fix: `theme-release.sh` perl patterns for `style.css` now use `^` + `/m` to preserve line prefix on each bump.
* Fix: `check-version-sync.sh` sed patterns accept optional leading whitespace in `style.css` header.
* Fix: stray leading space removed from `* Version:` / `* Template Version:` lines in `style.css` header.

= 1.7.9 =
* Fix: `category_query` meta_query no longer adds empty conditions — filters for complexity/language are skipped when values are not provided.
* Fix: Pagination now works correctly; `$paged` was always 1 due to reading `$_REQUEST['var']` as an array when it is a JSON string.
* Fix: Cache key includes `selectToLink` flag to prevent stale cached HTML.
* Feature: `selectToLink` flag is forwarded to the book-card template; when active, the "More" button is replaced by a direct download link.

= Unreleased =
* Release rule: `npm run theme` зобовʼязаний виконувати patch bump версії одночасно в `functions.php` (`WEBBOOKS_VERSION`), `style.css` (`Version`, `Template Version`).
* Release rule: перед успішним завершенням `npm run theme` обовʼязково мають бути оновлені `CHANGES.md` та `README.txt` (changelog для нової версії), інакше скрипт завершується з помилкою.

= 1.1 =
* git push https://github.com/homeandriy/s3-books-convert master:master -f
* Create interface to convert pdf books into images and upload to S3 storage

= 1.0 =
* A change since the previous version.
* Another change.

= 0.5 =
* List versions from most recent at top to oldest at bottom.

== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`


== Previous main releases ==
= 2.0.8 =
* Standard article cards now use two columns on desktop while retaining a single-column mobile layout.
* Updated the 404 page heading and recommended-content grid to match the archive layout.

= 2.0.7 =
* Standard articles now use editorial archive cards with a 16:9 image, cleaned excerpt, title, and localized Read more action; downloadable books retain their metadata cards.
* Added a local 16:9 fallback image for article posts without a featured image and remove legacy caption/Markdown artifacts from article previews.
* Refreshed category and tag headings, removed the obsolete Browse by categories action, removed the unnecessary sidebar-menu top offset, and extended the author-support backdrop through the page shell.

= 2.0.6 =
* Restored one viewport-height owner for the legacy layout shell: the wrapper accounts for the header while the content section inherits that height, preventing redundant page scrolling.
* Google reCAPTCHA now loads only for singular content with open comments, with its initial asynchronous loader in the document head; it is not loaded on the author-support page.

= 2.0.5 =
* Refined the author-support layout to remove the nested-scroll/blank-strip issue and added responsive Theme Settings styling for payment controls.
* Theme Settings now accept validated Monobank jar and PrivatBank envelope URLs. Filled links and payment-card data remain unavailable in public HTML and load only after the visitor explicitly opens the support dialog.
* Added branded Visa, Mastercard, UnionPay, Monobank, PrivatBank, and generic payment-card SVG marks in the support dialog.

= 2.0.4 =
* Centered the author-support payment dialog and made it static: it now closes only through its close icon or explicit Close button.
* The rotating support message uses TypeIt for the type, cursor, hold, and erase cycle.

= 2.0.3 =
* Rebuilt the author-support page as a responsive image-and-content donation layout and added 20 language-specific rotating motivation messages with a reduced-motion fallback.

= 2.0.2 =
* Redesigned the author-support page with a responsive hero, clearer call-to-action hierarchy, and a compact phone layout.

= 2.0.1 =
* Added localized author-support links to the sidebar, portfolio navigation, and desktop header. They render only while the corresponding language page is published; availability is cached for two hours and cleared on status changes.

= 2.0.0 =
* Ukrainian, Russian, and Polish author-support pages are now created as drafts. The next theme activation also converts the already-created three pages to drafts once, without changing their content or Polylang links.

= 1.10.10 =
* Replaced the deprecated theme-owned copy fallback with the `clipboard` npm dependency; author-support copying now uses ClipboardJS on both the main site and portfolio bundle.

= 1.10.9 =
* Added an author-support page template and an idempotent Polylang migration for Ukrainian, Russian, and Polish support pages.
* Theme Settings now manage author name, Telegram contact URL, and repeatable payment cards with Luhn (Mod 10) validation.
* Payment cards and Telegram URLs are loaded only after a visitor action through protected AJAX endpoints; they are not rendered in public page HTML.

= 1.10.8 =
* Docker fallback builds now repair ownership of generated `dist/` artifacts, preventing `EACCES` failures when rebuilding or preparing the theme ZIP.

= 1.10.7 =
* Imported articles use the full desktop reading column, and language-tagged code samples now have responsive GitHub-dark syntax highlighting.
* Added `highlight.js` to the Vite frontend bundle.
* Updated transitive frontend build dependencies; `npm audit` reports no known vulnerabilities.
* Added an authenticated Polylang REST bridge for saving translation groups from publication integrations.

= 1.10.6 =
* Telegram support: added a protected support-bot webhook with secure group setup, rate limiting, and two-way visitor/support-group message forwarding.
* Contact links in the site header and portfolio now open `@webbooks_support_bot`; the portfolio has a prominent animated Telegram call-to-action that respects reduced-motion preferences.

= 1.10.3 =
* Polylang support now covers custom queries, search, special pages, navigation, portfolio links, structured data, and AJAX responses; includes a repeatable SQL migration for translations and localized menus.
* Theme catalogs were refreshed for Ukrainian, Polish, Russian, and English.
* Long-form book descriptions use a responsive editorial reading measure and typography; theme-owned spacing and type values now use rem/clamp units.
* Fixed Ukrainian and Polish portfolio translations so they load the dedicated portfolio stylesheet and JavaScript.

= 1.10.2 =
* All-post catalog preserves `?page=N`, renders 12 newest posts per page, and progressively appends later pages through Load more.

= 1.10.1 =
* Catalog, search, download, and gallery covers retain their portrait proportions without cropping.
* Header controls and book-preview actions use semantic buttons; desktop search width and dropdown styling were refined.

= 1.10.0 =
* Responsive UI: tablet layouts use the persistent sidebar and cards keep covers above content until wide desktop widths.
* Book pages: themed tab icons and Bootstrap 5 comments controls.
* Markup: normalized reusable catalog, search, download, sidebar, category, and modal template markup.

= 1.9.10 =
* Responsive UI: completed the mobile-first layout pass for catalog cards, navigation, headers, forms, and pagination.
* Comments: rebuilt the public comments form and thread markup with Bootstrap 5 components, accessible labels, isolated template parts, and a Vite-managed emoji picker.
* Templates: replaced legacy my-prefixed identifiers with descriptive Webbooks-specific IDs and applied the shared pagination component to comments.

= 1.9.8 =
* Mobile navigation: full-screen Offcanvas with a centered brand, close button, Escape support, and scroll locking.
* Catalog: book covers retain their proportions and tablet/desktop card rows align to a consistent height.
* Mobile header: search and language controls are grouped predictably at the right edge.

= 1.9.7 =
* Search: AJAX catalog and global search now respect the active Polylang language and render matching translations.
* Search UX: downloadable books and articles are separated correctly; catalog cards use a one-column mobile and two-column tablet/desktop grid.
* UI: improved download countdown contrast, book tabs, header search width, language-switcher indicator, and full-height sidebar backgrounds.
* Frontend: Slick now consumes the WordPress jQuery instance through the Vite bridge.

= 1.9.5 =
* Architecture: moved theme integrations from inc/ into typed PSR-4 classes under src/.
* Frontend: Vite owns the theme JavaScript entry; WordPress jQuery is used once and duplicate legacy scripts are no longer enqueued.
* Build: fixed the Slick peer-dependency lockfile and release ZIP validation.

= 1.9.1 =
* Frontend: upgraded the build toolchain to Vite 8 and ESLint 10; Node.js 20.19+ is required, with Docker Node 20 fallback for older local environments.
* Frontend: upgraded Slick to 1.8.1 and added scoped JavaScript linting.
* Templates, accessibility, localization, AJAX handling, and CSS palette/legacy-prefix cleanup were updated across the theme.

= 1.8.10 =
* Frontend: migrated the bundled framework from Bootstrap 3.3.2 to Bootstrap 5.3.8.
* Templates: modals, tabs, dropdowns, collapse controls, carousel markup, and responsive/grid helpers now use Bootstrap 5 APIs.
* CSS: removed Bootstrap 3 runtime/styles and retained only a small theme compatibility layer for legacy panel and thumbnail markup.
* SEO: catalog cards no longer duplicate `Book` Microdata; book pages retain JSON-LD structured data.
* Performance: Questrial loads once from Google Fonts with `display=swap` and connection hints.
* AJAX: unavailable book previews and view counters return structured 404 errors; download-link errors include a message for the frontend.

= 1.8.9 =
* Analytics: replaced Universal Analytics with opt-in GA4. Define WEBBOOKS_GA4_MEASUREMENT_ID in wp-config.php to enable it.
* Refactor: portfolio page ID is now the WEBBOOKS_PORTFOLIO_PAGE_ID constant.

= 1.8.8 =
* Security: download AJAX rejects unavailable posts, and download links now isolate the opened tab.
* WordPress: sanitized download-template request data and escaped rendered post metadata.
* Admin: settings are now sanitized on write through the WordPress Settings API.
* Frontend: filter checkboxes read the current checked state.

= 1.8.1 =
* Refactor: `GENERAL_NONCE` → `WEBBOOKS_AJAX_NONCE` (value `myajax-nonce` → `webbooks-request-nonce`), `DOWNLOAD_BOOK_NONCE` → `WEBBOOKS_DOWNLOAD_NONCE` (value `download_book_nonce` → `webbooks-download-nonce`).
* Refactor: JS globals `php_array` → `webbooksConfig`, `php_arrayload` → `webbooksLoader`, `js_attributes` → `webbooksAjax`.

= 1.8.0 =
* Build: `bump_patch` in `theme-release.sh` now uses carry-at-10 versioning — each component counts 0–10; exceeding 10 resets it to 0 and increments the next component (0.0.10 → 0.1.0, 0.10.10 → 1.0.0). First bump under new scheme: 1.7.12 → 1.8.0.

= 1.7.12 =
* Fix: invalid CSS selector `. table > tfoot > tr > td` (stray space after dot) in `style.css` — caused a css-syntax-error warning in Vite build.

= 1.7.11 =
* Release: auto-bump via `npm run theme`; produced release ZIP with updated i18n and production build.

= 1.7.10 =
* Fix: `theme-release.sh` perl patterns for `style.css` now use `^` + `/m` to preserve line prefix on each bump.
* Fix: `check-version-sync.sh` sed patterns accept optional leading whitespace in `style.css` header.
* Fix: stray leading space removed from `* Version:` / `* Template Version:` lines in `style.css` header.

= 1.7.9 =
* Fix: `category_query` meta_query no longer adds empty conditions — filters for complexity/language are skipped when values are not provided.
* Fix: Pagination now works correctly; `$paged` was always 1 due to reading `$_REQUEST['var']` as an array when it is a JSON string.
* Fix: Cache key includes `selectToLink` flag to prevent stale cached HTML.
* Feature: `selectToLink` flag is forwarded to the book-card template; when active, the "More" button is replaced by a direct download link.

= Unreleased =
* Release rule: `npm run theme` зобовʼязаний виконувати patch bump версії одночасно в `functions.php` (`WEBBOOKS_VERSION`), `style.css` (`Version`, `Template Version`).
* Release rule: перед успішним завершенням `npm run theme` обовʼязково мають бути оновлені `CHANGES.md` та `README.txt` (changelog для нової версії), інакше скрипт завершується з помилкою.

= 1.1 =
* git push https://github.com/homeandriy/s3-books-convert master:master -f
* Create interface to convert pdf books into images and upload to S3 storage

= 1.0 =
* A change since the previous version.
* Another change.

= 0.5 =
* List versions from most recent at top to oldest at bottom.
