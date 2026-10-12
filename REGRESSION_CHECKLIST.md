# Перевірка Webbooks 2.0.9

## Об’єднаний реліз / 2026-10-12

Основа 1.9.6 зі збереженням можливостей 2.0.8. ESLint, Vite, 10 UI-тестів, 49 інтеграційних перевірок та PHP lint 100 файлів пройшли на WordPress 7.1.3 / PHP 8.5.11. Перевірено пошук книги за автором серед понад 80 статей, розмежування книг/статей за форматом, AJAX-картки після Load more, валідацію карток і платіжних URL. ACF Pro та production-конфігурація не входили до цього прогону.

Браузерне прев’ю підтвердило дві колонки на широкому екрані та одну на вузькому; хрестик offcanvas і Escape додатково перевірені автоматичним UI-тестом. Slick/Fancybox відсутні серед підключених ресурсів. Збірка містить Bootstrap 5.3.8, Swiper 14.3.0 та Zoom; один архів — webbooks-theme-release.zip.

## Попередня перевірка 1.8.10

## Виконаний ремонт

Пошук SQL за автором і назвою, окремі статті та точні totals; одна копія jQuery; optional ACF із нормалізацією; екранована повторна галерея без підміни post_gallery; the_content; відсутність HTML-transients; pending comment cooldown; атомарні views з дедуплікацією; дозволений authenticated REST; PSR-4; controller/service/template; сервіси всередині одного пакета теми; параметри сайту; lifecycle hooks, native images, умовні JS, robots/schema/zoom; cached related IDs; allowlist ZIP; оновлені build tools і документація.

Рішення залишається classic theme. Classic Editor та Advanced Editor Tools можна залишити; block theme не запроваджується.

## Автоматичні перевірки

```sh
WITH_PLUGINS=1 docker compose -p codex-webbooks-tests -f tests/compose.yml up --build --abort-on-container-exit --exit-code-from tests
npm run lint
npm run build
npm audit
bash scripts/check-version-sync.sh
bash scripts/build-release.sh webbooks-theme-release.zip
```

Runner ізольований назвою Compose та базою webbooks_tests; production базу не використовує. Fixtures видаляються. PHP 8.5.11, WordPress 7.1.3; baseline без плагінів і з ACF 6.8.10, Polylang 3.8.10, Ninja Forms 3.15.5. Assertions під E_ALL; PHP lint усього власного коду виконується окремо. WP-CLI має власні deprecated-повідомлення PHP 8.5; це не результати перевірки теми.

Сценарії: >80 збігів, author-only, wildcard/injection, приватні записи, точні totals, cleanup SQL filter, malformed ACF, PSR-4, картки без global post, escaped/multiple/empty galleries, pending cooldown і email rotation, міграція та дедуп views, download schemes/passwords, invalidation related IDs, language pagination і legacy comments, anonymous/authenticated REST.

Фінальний прогін: 26 assertions без плагінів + 26 з ACF/Polylang/Ninja Forms, усі пройшли. ESLint, Vite build, PHP lint, синхронізація версій і git diff --check пройшли. PhpStorm build успішний, але IDE повідомляє про обмежені build diagnostics; перевірка PHP виконувалася окремо в Docker.

Локальний браузер: пошук Preview author повертає дві книги, вкладка завантажень працює, Fancybox відкриває зображення, після countdown з’являється дозволене посилання. У перевірених сценаріях console errors немає. ZIP webbooks-theme-1.8.10.zip містить одну тему з усіма сервісами; reference/, IDE, тести та вкладені ZIP виключені.

Це не сертифікація всієї конфігурації production. ACF Pro 6.8.0.1 (реліз 2026-03-31) недоступний локально; Pro gallery і site field groups потрібно перевірити на staging. S3 за уточненням власника незавершений, тому реальний S3-прогін не входить у перевірку.

## Плагіни встановленого сайту

| Плагін / надана версія | Дія та перевірка |
|---|---|
| ACF Pro 6.8.0.1 | Оновити на staging, порівняти metadata і Pro gallery; frontend має fallback без ACF. |
| ACF Content Analysis for Yoast 3.2 | Перевірити редактор і аналіз власних полів після оновлення ACF/Yoast. |
| Yoast SEO 28.6 | Основний WebSite/WebPage/OpenGraph graph належить Yoast; тема додає лише Book. Перевірити canonical, breadcrumbs, sitemap, noindex downloads. |
| MonsterInsights 11.3.0 | Тема автоматично пропускає власний GA4; перевірити один page_view і відсутність дубля gtag. |
| Cookie consent | У приватному вікні перевірити прийняття й відмову. До згоди та після відмови аналітика і реклама не мають бути у HTML; після прийняття — з'явитися після перезавантаження. |
| Classic Editor 1.7.0 / Advanced Editor Tools 5.10.1 | Залишити для TinyMCE; створення/редагування/shortcodes/ACF metaboxes. |
| Custom Post Templates 1.5 | Article зареєстрований через Template Post Type: post. Старий custom_post_template підтримано; після перевірки вимкнути старий плагін і призначати штатний шаблон. |
| WP REST API 2.0-beta15 | Зайвий для сучасного core (REST endpoints у core з 4.7). Вимкнути спершу на staging; перевірити AI Translate, CPT UI та authenticated REST. |
| RusToLat 0.3 | Замінити/вимкнути після перевірки нових slug. Є опублікована CSRF-знахідка для <=0.3; існуючі permalink не перейменовувати масово. |
| WP No Category Base 1.1.1 | Перевірити маршрути категорій, Polylang, collisions зі сторінками й 301; зміна плагіна потребує збереження URL. |
| AIOS 5.4.10 / Really Simple Security 9.8.3 | Узгодити, хто керує firewall/login/REST/captcha; не вмикати дубльовані правила. Заміна теми не потребує двох наборів однакових обмежень. |
| Akismet 5.7.2 | Перевірити статус spam/pending та форму разом із власним cooldown/reCAPTCHA. |
| Ninja Forms 3.15.5 | Інтеграційний boot перевірено; report_form ID налаштовується. На staging перевірити реальне надсилання і доставку через SMTP. |
| Polylang 3.8.10 | Інтеграційний boot перевірено; на staging перевірити uk/ru/en category roots, search, pagination, comments, downloads. |
| Webbooks AI Translate 1.0.1 | Власний код не наданий; перевірити authenticated REST, чергу, cron і метадані після перекладу. |
| Custom Post Type UI 1.19.3 | Перевірити registered CPT/taxonomies/REST; каталожні сервіси теми наразі працюють із post. |
| Enlighter 4.7.0 | the_content тепер викликається; перевірити highlight, shortcode та JS на статтях. |
| Easy WP SMTP 2.15.1 | Перевірити тестовий лист і доставку Ninja Forms без production адресатів. |
| TinyPNG 3.8.0 | Перевірити attachment derivatives/srcset після компресії, нове завантаження зображень. |
| UpdraftPlus 1.26.8 | Зберегти файли та DB перед міграцією; перевірити включення нових webbooks_views/webbooks_view_visitors tables у backup. |
| Advanced Cron Manager 2.7.0 | Перевірити webbooks_cleanup_views і чергу перекладу. |
| WP OPcache 4.3.0 | Після deploy скинути OPcache доступним методом хостингу. |
| Query Monitor 4.0.7 | Використати для staging SQL/errors; оцінити витрати інструмента на production. |
| WP Total Hacks 4.7.2 | Перевірити активні tweaks, які можуть змінювати hooks/editor/comments; compatibility залежить від налаштувань. |

Публічні джерела: [REST у WordPress 4.7](https://wordpress.org/documentation/wordpress-version/version-4-7/), [ACF changelog](https://www.advancedcustomfields.com/changelog/), [RusToLat CSRF advisory](https://www.wordfence.com/threat-intel/vulnerabilities/wordpress-plugins/rustolat/rus-to-lat-03-cross-site-request-forgery-to-plugins-options-changes), [Custom Post Templates source](https://plugins.svn.wordpress.org/custom-post-template/trunk/custom-post-templates.php).

## Послідовність staging → production

1. Backup DB/uploads/плагінів/поточної теми; відновити ізольовану копію без production mail/cron/webhooks.
2. Встановити тему 1.8.10 одним ZIP з усіма вбудованими сервісами. Нові таблиці створюються через dbDelta, _views_count мігрує при першому перегляді; метадані книг не переписуються.
3. Налаштування → Webbooks: перенести books category (раніше 18), articles category (раніше 19), download page, Ninja report (раніше 3), рекламу/verification/partner. Portfolio page повинна мати portfolio-good.php. Не покладатися на старі page ID чи upload-шляхи.
4. Перевірити сайт на поточній PHP і потім PHP 8.5 з WP_DEBUG_LOG/E_ALL: homepage, archives, category, search/filter/pagination, book/article/gallery, download, portfolio, pending/approved comments, редактор/shortcodes, uk/ru/en, mobile/keyboard/zoom.
5. Оновити ACF Pro; прибрати WP REST API і Custom Post Templates на staging; замінити RusToLat зі збереженням старих URL. Тести повторити з усіма чинними плагінами і їхніми реальними параметрами.
6. Перевірити Query Monitor/лог, consent, один tracker, доставку форми, cron і резервну копію нових tables. Лише після цього переносити перевірену конфігурацію на production.

Rollback: повернути попередню тему/PHP/плагіни з backup. Нові таблиці автоматично не видаляються. Старий _views_count залишається стартовим значенням; після відкату старий код не бачить нові views — для точного відновлення лічильників потрібен контрольований експорт, не автоматичний overwrite.
