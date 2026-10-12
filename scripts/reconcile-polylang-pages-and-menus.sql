-- Webbooks / Polylang 3.8.5 content and menu reconciliation.
--
-- Scope:
--   1. Creates Ukrainian and Polish translations for Pages 481, 846 and 891.
--   2. Creates separate primary and footer menus for uk, ru and pl.
--   3. Assigns the six menus to Polylang's localized menu locations.
--
-- IMPORTANT
-- - Run only against a fresh backup of the database from 2026-07-19.
-- - Test on staging first. The source tables use MyISAM, so START TRANSACTION
--   cannot provide rollback protection.
-- - This script intentionally does not delete or overwrite the legacy menus.
-- - It assumes the WordPress table prefix is wp_.

-- The supplied database uses utf8mb4_unicode_ci. Explicitly match it so
-- phpMyAdmin sessions on MySQL 8 do not mix it with utf8mb4_0900_ai_ci.
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -------------------------------------------------------------------------
-- Legacy domain cleanup.
-- Replaces the retired cjbandit URL in public content and in classic menu
-- links. Serialized option/meta values are deliberately not updated here:
-- a plain SQL REPLACE() would invalidate their stored string lengths.
-- -------------------------------------------------------------------------

SET @webbooks_legacy_domain = 'http://cjbandit.url.ph';
SET @webbooks_canonical_domain = 'http://webbooks.com.ua/';
SET @webbooks_canonical_domain_without_slash = TRIM(TRAILING '/' FROM @webbooks_canonical_domain);

SELECT
	(SELECT COUNT(*) FROM wp_posts WHERE post_content LIKE CONCAT('%', @webbooks_legacy_domain, '%')) AS posts_with_legacy_url,
	(SELECT COUNT(*) FROM wp_postmeta WHERE meta_key = '_menu_item_url' AND meta_value LIKE CONCAT('%', @webbooks_legacy_domain, '%')) AS menu_items_with_legacy_url,
	(SELECT COUNT(*) FROM wp_comments WHERE comment_content LIKE CONCAT('%', @webbooks_legacy_domain, '%')) AS comments_with_legacy_url,
	(SELECT COUNT(*) FROM wp_term_taxonomy WHERE description LIKE CONCAT('%', @webbooks_legacy_domain, '%')) AS term_descriptions_with_legacy_url;

UPDATE wp_posts
SET post_content = REPLACE(
	REPLACE(post_content, CONCAT(@webbooks_legacy_domain, '/'), @webbooks_canonical_domain),
	@webbooks_legacy_domain,
	@webbooks_canonical_domain_without_slash
),
	post_excerpt = REPLACE(
		REPLACE(post_excerpt, CONCAT(@webbooks_legacy_domain, '/'), @webbooks_canonical_domain),
		@webbooks_legacy_domain,
		@webbooks_canonical_domain_without_slash
	)
WHERE post_content LIKE CONCAT('%', @webbooks_legacy_domain, '%')
	OR post_excerpt LIKE CONCAT('%', @webbooks_legacy_domain, '%');

UPDATE wp_postmeta
SET meta_value = REPLACE(
	REPLACE(meta_value, CONCAT(@webbooks_legacy_domain, '/'), @webbooks_canonical_domain),
	@webbooks_legacy_domain,
	@webbooks_canonical_domain_without_slash
)
WHERE meta_key = '_menu_item_url'
	AND meta_value LIKE CONCAT('%', @webbooks_legacy_domain, '%');

UPDATE wp_comments
SET comment_content = REPLACE(
	REPLACE(comment_content, CONCAT(@webbooks_legacy_domain, '/'), @webbooks_canonical_domain),
	@webbooks_legacy_domain,
	@webbooks_canonical_domain_without_slash
)
WHERE comment_content LIKE CONCAT('%', @webbooks_legacy_domain, '%');

UPDATE wp_term_taxonomy
SET description = REPLACE(
	REPLACE(description, CONCAT(@webbooks_legacy_domain, '/'), @webbooks_canonical_domain),
	@webbooks_legacy_domain,
	@webbooks_canonical_domain_without_slash
)
WHERE description LIKE CONCAT('%', @webbooks_legacy_domain, '%');

-- -------------------------------------------------------------------------
-- Preconditions and language identifiers.
-- -------------------------------------------------------------------------

SELECT option_value INTO @webbooks_home_url
FROM wp_options
WHERE option_name = 'home'
LIMIT 1;

SET @webbooks_home_url = TRIM(TRAILING '/' FROM @webbooks_home_url);

SELECT tt.term_taxonomy_id INTO @webbooks_language_uk
FROM wp_terms AS t
INNER JOIN wp_term_taxonomy AS tt ON tt.term_id = t.term_id
WHERE tt.taxonomy = 'language' AND t.slug = 'uk'
LIMIT 1;

SELECT tt.term_taxonomy_id INTO @webbooks_language_ru
FROM wp_terms AS t
INNER JOIN wp_term_taxonomy AS tt ON tt.term_id = t.term_id
WHERE tt.taxonomy = 'language' AND t.slug = 'ru'
LIMIT 1;

SELECT tt.term_taxonomy_id INTO @webbooks_language_pl
FROM wp_terms AS t
INNER JOIN wp_term_taxonomy AS tt ON tt.term_id = t.term_id
WHERE tt.taxonomy = 'language' AND t.slug = 'pl'
LIMIT 1;

-- Stop here if any of the following values is NULL.
SELECT
	@webbooks_language_uk AS uk_language_taxonomy_id,
	@webbooks_language_ru AS ru_language_taxonomy_id,
	@webbooks_language_pl AS pl_language_taxonomy_id;

-- -------------------------------------------------------------------------
-- 1. Page translations.
-- Existing Russian sources: 481 allpost, 846 portfolio, 891 download.
-- The templates are copied, while Yoast and editing metadata are deliberately
-- not copied: SEO titles/descriptions must be localized in WordPress admin.
-- -------------------------------------------------------------------------

DROP TEMPORARY TABLE IF EXISTS wb_page_translation_seed;
CREATE TEMPORARY TABLE wb_page_translation_seed (
	source_page_id BIGINT UNSIGNED NOT NULL,
	language_slug CHAR(2) NOT NULL,
	page_title VARCHAR(200) NOT NULL,
	page_slug VARCHAR(200) NOT NULL,
	page_template VARCHAR(200) NOT NULL,
	translated_page_id BIGINT UNSIGNED NULL,
	PRIMARY KEY (source_page_id, language_slug),
	UNIQUE KEY wb_page_slug (page_slug)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO wb_page_translation_seed (
	source_page_id,
	language_slug,
	page_title,
	page_slug,
	page_template
) VALUES
	(481, 'uk', 'Усі записи', 'usi-zapysy', 'all-post.php'),
	(481, 'pl', 'Wszystkie wpisy', 'wszystkie-wpisy', 'all-post.php'),
	(846, 'uk', 'Портфоліо', 'portfolio-uk', 'portfolio-good.php'),
	(846, 'pl', 'Portfolio', 'portfolio-pl', 'portfolio-good.php'),
	(891, 'uk', 'Завантажити', 'zavantazhyty', 'download.php'),
	(891, 'pl', 'Pobierz', 'pobierz', 'download.php');

INSERT INTO wp_posts (
	post_author,
	post_date,
	post_date_gmt,
	post_content,
	post_title,
	post_excerpt,
	post_status,
	comment_status,
	ping_status,
	post_password,
	post_name,
	to_ping,
	pinged,
	post_modified,
	post_modified_gmt,
	post_content_filtered,
	post_parent,
	guid,
	menu_order,
	post_type,
	post_mime_type,
	comment_count
)
SELECT
	source.post_author,
	UTC_TIMESTAMP(),
	UTC_TIMESTAMP(),
	source.post_content,
	seed.page_title,
	source.post_excerpt,
	'publish',
	source.comment_status,
	source.ping_status,
	'',
	seed.page_slug,
	'',
	'',
	UTC_TIMESTAMP(),
	UTC_TIMESTAMP(),
	source.post_content_filtered,
	0,
	'',
	0,
	'page',
	'',
	0
FROM wb_page_translation_seed AS seed
INNER JOIN wp_posts AS source ON source.ID = seed.source_page_id
WHERE source.post_type = 'page'
	AND source.post_status = 'publish'
	AND NOT EXISTS (
		SELECT 1
		FROM wp_posts AS existing_page
		WHERE existing_page.post_type = 'page'
			AND existing_page.post_name = seed.page_slug
	);

UPDATE wb_page_translation_seed AS seed
INNER JOIN wp_posts AS translated_page
	ON translated_page.post_type = 'page'
	AND translated_page.post_name = seed.page_slug
SET seed.translated_page_id = translated_page.ID;

UPDATE wp_posts AS translated_page
INNER JOIN wb_page_translation_seed AS seed ON seed.translated_page_id = translated_page.ID
SET translated_page.guid = CONCAT(@webbooks_home_url, '/?page_id=', translated_page.ID)
WHERE translated_page.guid = '';

INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
SELECT seed.translated_page_id, '_wp_page_template', seed.page_template
FROM wb_page_translation_seed AS seed
WHERE seed.translated_page_id IS NOT NULL
	AND NOT EXISTS (
		SELECT 1
		FROM wp_postmeta AS existing_meta
		WHERE existing_meta.post_id = seed.translated_page_id
			AND existing_meta.meta_key = '_wp_page_template'
	);

INSERT IGNORE INTO wp_term_relationships (object_id, term_taxonomy_id, term_order)
SELECT seed.translated_page_id,
	CASE seed.language_slug
		WHEN 'uk' THEN @webbooks_language_uk
		WHEN 'pl' THEN @webbooks_language_pl
	END,
	0
FROM wb_page_translation_seed AS seed
WHERE seed.translated_page_id IS NOT NULL;

-- The Russian source Pages are already assigned to ru in the supplied dump.
INSERT IGNORE INTO wp_term_relationships (object_id, term_taxonomy_id, term_order)
VALUES
	(481, @webbooks_language_ru, 0),
	(846, @webbooks_language_ru, 0),
	(891, @webbooks_language_ru, 0);

DROP TEMPORARY TABLE IF EXISTS wb_page_translation_groups;
CREATE TEMPORARY TABLE wb_page_translation_groups (
	source_page_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
	group_slug VARCHAR(200) NOT NULL UNIQUE
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO wb_page_translation_groups (source_page_id, group_slug) VALUES
	(481, 'pll_webbooks_page_allpost'),
	(846, 'pll_webbooks_page_portfolio'),
	(891, 'pll_webbooks_page_download');

INSERT INTO wp_terms (name, slug, term_group)
SELECT translation_groups.group_slug, translation_groups.group_slug, 0
FROM wb_page_translation_groups AS translation_groups
WHERE NOT EXISTS (
	SELECT 1
	FROM wp_terms AS existing_term
	WHERE existing_term.slug = translation_groups.group_slug
);

INSERT INTO wp_term_taxonomy (term_id, taxonomy, description, parent, count)
SELECT terms.term_id, 'post_translations', '', 0, 3
FROM wb_page_translation_groups AS translation_groups
INNER JOIN wp_terms AS terms ON terms.slug = translation_groups.group_slug
WHERE NOT EXISTS (
	SELECT 1
	FROM wp_term_taxonomy AS existing_taxonomy
	WHERE existing_taxonomy.term_id = terms.term_id
		AND existing_taxonomy.taxonomy = 'post_translations'
);

UPDATE wp_term_taxonomy AS group_taxonomy
INNER JOIN wp_terms AS group_term ON group_term.term_id = group_taxonomy.term_id
INNER JOIN wb_page_translation_groups AS translation_groups ON translation_groups.group_slug = group_term.slug
INNER JOIN (
	SELECT
		source_page_id,
		MAX(CASE WHEN language_slug = 'uk' THEN translated_page_id END) AS uk_page_id,
		MAX(CASE WHEN language_slug = 'pl' THEN translated_page_id END) AS pl_page_id
	FROM wb_page_translation_seed
	GROUP BY source_page_id
) AS translated_pages ON translated_pages.source_page_id = translation_groups.source_page_id
SET group_taxonomy.description = CONCAT(
	'a:3:{s:2:"uk";i:', translated_pages.uk_page_id,
	';s:2:"ru";i:', translation_groups.source_page_id,
	';s:2:"pl";i:', translated_pages.pl_page_id,
	';}'
),
	group_taxonomy.count = 3
WHERE group_taxonomy.taxonomy = 'post_translations';

INSERT IGNORE INTO wp_term_relationships (object_id, term_taxonomy_id, term_order)
SELECT translation_groups.source_page_id, group_taxonomy.term_taxonomy_id, 0
FROM wb_page_translation_groups AS translation_groups
INNER JOIN wp_terms AS group_term ON group_term.slug = translation_groups.group_slug
INNER JOIN wp_term_taxonomy AS group_taxonomy
	ON group_taxonomy.term_id = group_term.term_id
	AND group_taxonomy.taxonomy = 'post_translations';

-- MySQL cannot reopen the same temporary table in two UNION branches.
INSERT IGNORE INTO wp_term_relationships (object_id, term_taxonomy_id, term_order)
SELECT seed.translated_page_id, group_taxonomy.term_taxonomy_id, 0
FROM wb_page_translation_seed AS seed
INNER JOIN wb_page_translation_groups AS translation_groups ON translation_groups.source_page_id = seed.source_page_id
INNER JOIN wp_terms AS group_term ON group_term.slug = translation_groups.group_slug
INNER JOIN wp_term_taxonomy AS group_taxonomy
	ON group_taxonomy.term_id = group_term.term_id
	AND group_taxonomy.taxonomy = 'post_translations'
WHERE seed.translated_page_id IS NOT NULL;

SELECT translated_page_id INTO @webbooks_allpost_uk
FROM wb_page_translation_seed
WHERE source_page_id = 481 AND language_slug = 'uk';
SELECT translated_page_id INTO @webbooks_allpost_pl
FROM wb_page_translation_seed
WHERE source_page_id = 481 AND language_slug = 'pl';
SELECT translated_page_id INTO @webbooks_portfolio_uk
FROM wb_page_translation_seed
WHERE source_page_id = 846 AND language_slug = 'uk';
SELECT translated_page_id INTO @webbooks_portfolio_pl
FROM wb_page_translation_seed
WHERE source_page_id = 846 AND language_slug = 'pl';

-- -------------------------------------------------------------------------
-- 2. One primary and one footer menu for each language.
-- Polylang uses localized locations: top___uk, top___ru, top___pl, etc.
-- No language taxonomy is attached to nav_menu_item posts: the menu location
-- itself is language-specific, as documented by Polylang.
-- -------------------------------------------------------------------------

DROP TEMPORARY TABLE IF EXISTS wb_menu_seed;
CREATE TEMPORARY TABLE wb_menu_seed (
	menu_slug VARCHAR(200) NOT NULL PRIMARY KEY,
	language_slug CHAR(2) NOT NULL,
	location_slug VARCHAR(20) NOT NULL,
	menu_name VARCHAR(200) NOT NULL,
	menu_term_id BIGINT UNSIGNED NULL,
	menu_taxonomy_id BIGINT UNSIGNED NULL
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO wb_menu_seed (menu_slug, language_slug, location_slug, menu_name) VALUES
	('webbooks-primary-uk', 'uk', 'top', 'Основна навігація'),
	('webbooks-primary-ru', 'ru', 'top', 'Основная навигация'),
	('webbooks-primary-pl', 'pl', 'top', 'Główna nawigacja'),
	('webbooks-footer-uk', 'uk', 'bottom', 'Навігація у підвалі'),
	('webbooks-footer-ru', 'ru', 'bottom', 'Навигация в подвале'),
	('webbooks-footer-pl', 'pl', 'bottom', 'Nawigacja w stopce');

INSERT INTO wp_terms (name, slug, term_group)
SELECT seed.menu_name, seed.menu_slug, 0
FROM wb_menu_seed AS seed
WHERE NOT EXISTS (
	SELECT 1
	FROM wp_terms AS existing_term
	WHERE existing_term.slug = seed.menu_slug
);

INSERT INTO wp_term_taxonomy (term_id, taxonomy, description, parent, count)
SELECT terms.term_id, 'nav_menu', '', 0, 0
FROM wb_menu_seed AS seed
INNER JOIN wp_terms AS terms ON terms.slug = seed.menu_slug
WHERE NOT EXISTS (
	SELECT 1
	FROM wp_term_taxonomy AS existing_taxonomy
	WHERE existing_taxonomy.term_id = terms.term_id
		AND existing_taxonomy.taxonomy = 'nav_menu'
);

UPDATE wb_menu_seed AS seed
INNER JOIN wp_terms AS terms ON terms.slug = seed.menu_slug
INNER JOIN wp_term_taxonomy AS taxonomy
	ON taxonomy.term_id = terms.term_id
	AND taxonomy.taxonomy = 'nav_menu'
SET seed.menu_term_id = terms.term_id,
	seed.menu_taxonomy_id = taxonomy.term_taxonomy_id;

-- Root category IDs are read from translated category slugs already present in
-- the supplied dump, instead of using legacy Russian numeric IDs in menu items.
SELECT term_id INTO @webbooks_books_uk FROM wp_terms WHERE slug = 'knigi-uk' LIMIT 1;
SELECT term_id INTO @webbooks_books_ru FROM wp_terms WHERE slug = 'books-main' LIMIT 1;
SELECT term_id INTO @webbooks_books_pl FROM wp_terms WHERE slug = 'knigi-pl' LIMIT 1;
SELECT term_id INTO @webbooks_articles_uk FROM wp_terms WHERE slug = 'stati-uk' LIMIT 1;
SELECT term_id INTO @webbooks_articles_ru FROM wp_terms WHERE slug = 'post-main' LIMIT 1;
SELECT term_id INTO @webbooks_articles_pl FROM wp_terms WHERE slug = 'stati-pl' LIMIT 1;

DROP TEMPORARY TABLE IF EXISTS wb_menu_item_seed;
CREATE TEMPORARY TABLE wb_menu_item_seed (
	item_slug VARCHAR(200) NOT NULL PRIMARY KEY,
	menu_slug VARCHAR(200) NOT NULL,
	menu_order INT NOT NULL,
	item_type VARCHAR(20) NOT NULL,
	object_type VARCHAR(20) NOT NULL,
	object_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
	menu_label VARCHAR(200) NOT NULL,
	menu_url VARCHAR(255) NOT NULL DEFAULT '',
	menu_item_id BIGINT UNSIGNED NULL
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO wb_menu_item_seed (
	item_slug, menu_slug, menu_order, item_type, object_type, object_id, menu_label, menu_url
) VALUES
	('wb-top-uk-home', 'webbooks-primary-uk', 1, 'custom', 'custom', 0, 'Головна', CONCAT(@webbooks_home_url, '/')),
	('wb-top-uk-books', 'webbooks-primary-uk', 2, 'taxonomy', 'category', @webbooks_books_uk, 'Книги', ''),
	('wb-top-uk-articles', 'webbooks-primary-uk', 3, 'taxonomy', 'category', @webbooks_articles_uk, 'Статті', ''),
	('wb-top-uk-allpost', 'webbooks-primary-uk', 4, 'post_type', 'page', @webbooks_allpost_uk, 'Усі записи', ''),
	('wb-top-uk-portfolio', 'webbooks-primary-uk', 5, 'post_type', 'page', @webbooks_portfolio_uk, 'Портфоліо', ''),
	('wb-top-ru-home', 'webbooks-primary-ru', 1, 'custom', 'custom', 0, 'Главная', CONCAT(@webbooks_home_url, '/ru/')),
	('wb-top-ru-books', 'webbooks-primary-ru', 2, 'taxonomy', 'category', @webbooks_books_ru, 'Книги', ''),
	('wb-top-ru-articles', 'webbooks-primary-ru', 3, 'taxonomy', 'category', @webbooks_articles_ru, 'Статьи', ''),
	('wb-top-ru-allpost', 'webbooks-primary-ru', 4, 'post_type', 'page', 481, 'Все записи', ''),
	('wb-top-ru-portfolio', 'webbooks-primary-ru', 5, 'post_type', 'page', 846, 'Портфолио', ''),
	('wb-top-pl-home', 'webbooks-primary-pl', 1, 'custom', 'custom', 0, 'Strona główna', CONCAT(@webbooks_home_url, '/pl/')),
	('wb-top-pl-books', 'webbooks-primary-pl', 2, 'taxonomy', 'category', @webbooks_books_pl, 'Książki', ''),
	('wb-top-pl-articles', 'webbooks-primary-pl', 3, 'taxonomy', 'category', @webbooks_articles_pl, 'Artykuły', ''),
	('wb-top-pl-allpost', 'webbooks-primary-pl', 4, 'post_type', 'page', @webbooks_allpost_pl, 'Wszystkie wpisy', ''),
	('wb-top-pl-portfolio', 'webbooks-primary-pl', 5, 'post_type', 'page', @webbooks_portfolio_pl, 'Portfolio', ''),
	('wb-footer-uk-books', 'webbooks-footer-uk', 1, 'taxonomy', 'category', @webbooks_books_uk, 'Книги', ''),
	('wb-footer-uk-articles', 'webbooks-footer-uk', 2, 'taxonomy', 'category', @webbooks_articles_uk, 'Статті', ''),
	('wb-footer-uk-allpost', 'webbooks-footer-uk', 3, 'post_type', 'page', @webbooks_allpost_uk, 'Усі записи', ''),
	('wb-footer-uk-portfolio', 'webbooks-footer-uk', 4, 'post_type', 'page', @webbooks_portfolio_uk, 'Портфоліо', ''),
	('wb-footer-ru-books', 'webbooks-footer-ru', 1, 'taxonomy', 'category', @webbooks_books_ru, 'Книги', ''),
	('wb-footer-ru-articles', 'webbooks-footer-ru', 2, 'taxonomy', 'category', @webbooks_articles_ru, 'Статьи', ''),
	('wb-footer-ru-allpost', 'webbooks-footer-ru', 3, 'post_type', 'page', 481, 'Все записи', ''),
	('wb-footer-ru-portfolio', 'webbooks-footer-ru', 4, 'post_type', 'page', 846, 'Портфолио', ''),
	('wb-footer-pl-books', 'webbooks-footer-pl', 1, 'taxonomy', 'category', @webbooks_books_pl, 'Książki', ''),
	('wb-footer-pl-articles', 'webbooks-footer-pl', 2, 'taxonomy', 'category', @webbooks_articles_pl, 'Artykuły', ''),
	('wb-footer-pl-allpost', 'webbooks-footer-pl', 3, 'post_type', 'page', @webbooks_allpost_pl, 'Wszystkie wpisy', ''),
	('wb-footer-pl-portfolio', 'webbooks-footer-pl', 4, 'post_type', 'page', @webbooks_portfolio_pl, 'Portfolio', '');

INSERT INTO wp_posts (
	post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,
	post_status, comment_status, ping_status, post_password, post_name, to_ping,
	pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent,
	guid, menu_order, post_type, post_mime_type, comment_count
)
SELECT
	1, UTC_TIMESTAMP(), UTC_TIMESTAMP(), '', seed.menu_label, '',
	'publish', 'closed', 'closed', '', seed.item_slug, '',
	'', UTC_TIMESTAMP(), UTC_TIMESTAMP(), '', 0,
	'', seed.menu_order, 'nav_menu_item', '', 0
FROM wb_menu_item_seed AS seed
WHERE NOT EXISTS (
	SELECT 1
	FROM wp_posts AS existing_item
	WHERE existing_item.post_type = 'nav_menu_item'
		AND existing_item.post_name = seed.item_slug
);

UPDATE wb_menu_item_seed AS seed
INNER JOIN wp_posts AS menu_item
	ON menu_item.post_type = 'nav_menu_item'
	AND menu_item.post_name = seed.item_slug
SET seed.menu_item_id = menu_item.ID;

INSERT IGNORE INTO wp_term_relationships (object_id, term_taxonomy_id, term_order)
SELECT seed.menu_item_id, menus.menu_taxonomy_id, 0
FROM wb_menu_item_seed AS seed
INNER JOIN wb_menu_seed AS menus ON menus.menu_slug = seed.menu_slug
WHERE seed.menu_item_id IS NOT NULL;

INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
SELECT seed.menu_item_id, '_menu_item_type', seed.item_type
FROM wb_menu_item_seed AS seed
WHERE NOT EXISTS (SELECT 1 FROM wp_postmeta AS meta WHERE meta.post_id = seed.menu_item_id AND meta.meta_key = '_menu_item_type');

INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
SELECT seed.menu_item_id, '_menu_item_menu_item_parent', '0'
FROM wb_menu_item_seed AS seed
WHERE NOT EXISTS (SELECT 1 FROM wp_postmeta AS meta WHERE meta.post_id = seed.menu_item_id AND meta.meta_key = '_menu_item_menu_item_parent');

INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
SELECT seed.menu_item_id, '_menu_item_object_id', seed.object_id
FROM wb_menu_item_seed AS seed
WHERE NOT EXISTS (SELECT 1 FROM wp_postmeta AS meta WHERE meta.post_id = seed.menu_item_id AND meta.meta_key = '_menu_item_object_id');

INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
SELECT seed.menu_item_id, '_menu_item_object', seed.object_type
FROM wb_menu_item_seed AS seed
WHERE NOT EXISTS (SELECT 1 FROM wp_postmeta AS meta WHERE meta.post_id = seed.menu_item_id AND meta.meta_key = '_menu_item_object');

INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
SELECT seed.menu_item_id, '_menu_item_target', ''
FROM wb_menu_item_seed AS seed
WHERE NOT EXISTS (SELECT 1 FROM wp_postmeta AS meta WHERE meta.post_id = seed.menu_item_id AND meta.meta_key = '_menu_item_target');

INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
SELECT seed.menu_item_id, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'
FROM wb_menu_item_seed AS seed
WHERE NOT EXISTS (SELECT 1 FROM wp_postmeta AS meta WHERE meta.post_id = seed.menu_item_id AND meta.meta_key = '_menu_item_classes');

INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
SELECT seed.menu_item_id, '_menu_item_xfn', ''
FROM wb_menu_item_seed AS seed
WHERE NOT EXISTS (SELECT 1 FROM wp_postmeta AS meta WHERE meta.post_id = seed.menu_item_id AND meta.meta_key = '_menu_item_xfn');

INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
SELECT seed.menu_item_id, '_menu_item_url', seed.menu_url
FROM wb_menu_item_seed AS seed
WHERE NOT EXISTS (SELECT 1 FROM wp_postmeta AS meta WHERE meta.post_id = seed.menu_item_id AND meta.meta_key = '_menu_item_url');

UPDATE wp_term_taxonomy AS menu_taxonomy
INNER JOIN (
	SELECT term_taxonomy_id, COUNT(*) AS menu_item_count
	FROM wp_term_relationships
	GROUP BY term_taxonomy_id
) AS menu_counts ON menu_counts.term_taxonomy_id = menu_taxonomy.term_taxonomy_id
INNER JOIN wb_menu_seed AS seed ON seed.menu_taxonomy_id = menu_taxonomy.term_taxonomy_id
SET menu_taxonomy.count = menu_counts.menu_item_count;

SELECT menu_term_id INTO @webbooks_top_uk FROM wb_menu_seed WHERE menu_slug = 'webbooks-primary-uk';
SELECT menu_term_id INTO @webbooks_top_ru FROM wb_menu_seed WHERE menu_slug = 'webbooks-primary-ru';
SELECT menu_term_id INTO @webbooks_top_pl FROM wb_menu_seed WHERE menu_slug = 'webbooks-primary-pl';
SELECT menu_term_id INTO @webbooks_bottom_uk FROM wb_menu_seed WHERE menu_slug = 'webbooks-footer-uk';
SELECT menu_term_id INTO @webbooks_bottom_ru FROM wb_menu_seed WHERE menu_slug = 'webbooks-footer-ru';
SELECT menu_term_id INTO @webbooks_bottom_pl FROM wb_menu_seed WHERE menu_slug = 'webbooks-footer-pl';

-- Polylang stores per-language menu locations in its option and WordPress
-- stores the matching localized location keys in the theme_mods option.
UPDATE wp_options
SET option_value = REPLACE(
	option_value,
	's:9:"nav_menus";a:0:{}',
	CONCAT(
		's:9:"nav_menus";a:1:{s:22:"webbooks-theme-release";a:2:{',
		's:3:"top";a:3:{s:2:"uk";i:', @webbooks_top_uk, ';s:2:"ru";i:', @webbooks_top_ru, ';s:2:"pl";i:', @webbooks_top_pl, ';}',
		's:6:"bottom";a:3:{s:2:"uk";i:', @webbooks_bottom_uk, ';s:2:"ru";i:', @webbooks_bottom_ru, ';s:2:"pl";i:', @webbooks_bottom_pl, ';}',
		'}}'
	)
)
WHERE option_name = 'polylang'
	AND option_value LIKE '%s:9:"nav_menus";a:0:{}%';

UPDATE wp_options
SET option_value = REPLACE(
	option_value,
	's:18:"nav_menu_locations";a:2:{s:3:"top";i:0;s:6:"bottom";i:0;}',
	CONCAT(
		's:18:"nav_menu_locations";a:6:{',
		's:8:"top___uk";i:', @webbooks_top_uk, ';s:8:"top___ru";i:', @webbooks_top_ru, ';s:8:"top___pl";i:', @webbooks_top_pl, ';',
		's:11:"bottom___uk";i:', @webbooks_bottom_uk, ';s:11:"bottom___ru";i:', @webbooks_bottom_ru, ';s:11:"bottom___pl";i:', @webbooks_bottom_pl, ';}'
	)
)
WHERE option_name = 'theme_mods_webbooks-theme-release'
	AND option_value LIKE '%s:18:"nav_menu_locations";a:2:{s:3:"top";i:0;s:6:"bottom";i:0;}%';

-- -------------------------------------------------------------------------
-- Verification queries. All rows must be present before the script is used
-- outside staging.
-- -------------------------------------------------------------------------

SELECT
	seed.source_page_id,
	seed.language_slug,
	seed.translated_page_id,
	translated_page.post_title,
	translated_page.post_name
FROM wb_page_translation_seed AS seed
LEFT JOIN wp_posts AS translated_page ON translated_page.ID = seed.translated_page_id
ORDER BY seed.source_page_id, seed.language_slug;

SELECT
	seed.location_slug,
	seed.language_slug,
	seed.menu_name,
	seed.menu_term_id,
	menu_taxonomy.count AS menu_item_count
FROM wb_menu_seed AS seed
LEFT JOIN wp_term_taxonomy AS menu_taxonomy ON menu_taxonomy.term_taxonomy_id = seed.menu_taxonomy_id
ORDER BY seed.location_slug, seed.language_slug;

SELECT option_name, option_value
FROM wp_options
WHERE option_name IN ('polylang', 'theme_mods_webbooks-theme-release');

-- After import: clear object/page cache and save Settings > Permalinks once.
-- Then verify uk, ru and pl variants of /allpost/, /portfolio/ and the menu.
