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
