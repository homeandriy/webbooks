=== Webbooks classic theme ===
Contributors: homeandriy
Tags: classic-theme, books, custom-fields, translation-ready
Requires at least: 6.5
Requires PHP: 8.2
Tested up to: 7.1.3
Stable tag: 1.9.1
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
Docker fallback: bash scripts/build-with-fallback.sh.
PHP/WordPress integration: WITH_PLUGINS=1 docker compose -p codex-webbooks-tests -f tests/compose.yml up --build --abort-on-container-exit --exit-code-from tests.
Package the current version: bash scripts/build-release.sh webbooks-theme-release.zip (requires gettext, zip and unzip).
Every version bump must update WEBBOOKS_VERSION, both CSS version headers, CHANGES.md and this changelog. The npm theme command checks release notes before modifying versions.
See REGRESSION_CHECKLIST.md for test coverage, plugin limitations, deployment and rollback.

== Compatibility ==
Minimum supported PHP remains 8.2. The included runner targets PHP 8.5 and current WordPress. Tested third-party versions are recorded in REGRESSION_CHECKLIST.md.
ACF Pro 6.8.0.1 and the full production plugin configuration require staging verification; free ACF tests do not certify Pro-only gallery fields. S3_Service is an optional unfinished adapter, not a required integration.
Counters migrate _views_count lazily into dedicated tables. New counts are read through Webbooks\Statistics\Views::count(); custom integrations reading the old meta key must migrate. One IP/post/hour counts as one visit; no raw visitor IP is stored by this counter.

== Changelog ==

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
