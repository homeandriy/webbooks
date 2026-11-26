# Ілюстрації статей у Cloudflare R2

Маршрут `POST /wp-json/webbooks-ai/v1/images` приймає один бінарний файл `file` у `multipart/form-data`. Авторизація — штатний WordPress Application Password через HTTPS, користувач має право `upload_files`. Чинний механізм Cloudflare додатково перевіряє `X-Webbooks-Edge-Key`.

PNG, JPEG і WebP: від 1 до 8192 px на кожній осі, до 20 000 000 пікселів, до **8 388 608 байтів включно**. Ім’я та MIME клієнта не визначають формат. Перевіряються сигнатура, MIME, розміри, цілісність контейнера та повне декодування GD. Оригінал передається потоком без масштабування чи перекодування. SVG, GIF, HTML, JSON, зовнішні URL, масиви файлів і додаткові поля призначення відхиляються.

Обкладинки залишаються на штатному `POST /wp-json/wp/v2/media`, який повертає ID для `featured_media`. Новий маршрут не створює вкладень, дописів або груп Polylang.

## Що додати в production wp-config.php

Розмістіть визначення **перед** підключенням `wp-settings.php`, заповнивши значення на сервері:

```php
define('WEBBOOKS_R2_ENDPOINT', 'https://<ACCOUNT_ID>.r2.cloudflarestorage.com');
define('WEBBOOKS_R2_BUCKET', '<BUCKET_NAME>');
define('WEBBOOKS_R2_ACCESS_KEY_ID', '<R2_ACCESS_KEY_ID>');
define('WEBBOOKS_R2_SECRET_ACCESS_KEY', '<R2_SECRET_ACCESS_KEY>');
define('WEBBOOKS_R2_PUBLIC_BASE_URL', 'https://data.webbooks.com.ua');
```

Використайте S3-сумісну пару **Access Key ID / Secret Access Key** окремого R2-токена з дозволом **Object Read & Write** та областю доступу лише до потрібного бакета. Для роботи потрібні запис об’єкта та читання його метаданих (PutObject і HeadObject). Бакет має бути тим, до якого вже прив’язаний `data.webbooks.com.ua`. Код не змінює прив’язку домену.

Альтернатива — однойменні змінні середовища PHP-FPM; визначена константа має пріоритет. Значення читаються лише із серверної конфігурації й не зберігаються у WordPress options. Публічна база фіксована: інший домен конфігурація відхилить; якщо параметр бази відсутній, використовується `https://data.webbooks.com.ua`.

WordPress Application Password створіть у профілі виділеного користувача з `upload_files`. Він зберігається у хмарному клієнті. Edge key також залишається у чинному механізмі Cloudflare та конфігурації клієнта. Ці два значення не потрібні у `wp-config.php` для нового маршруту. Не додавайте секрети до Git або журналів; обмежте читання `wp-config.php` обліковим записом адміністратора і PHP.

## Сервер і залежності

- PHP 8.2 або новіший, GD із підтримкою PNG/JPEG/WebP, Fileinfo, DOM і SimpleXML.
- Composer встановлює зафіксований `async-aws/s3` та його залежності. GitHub Release ZIP містить production vendor без dev-пакетів. Для checkout із Git перед використанням API виконайте `composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader` у відповідному PHP-контейнері.
- Клієнт працює з HTTPS endpoint R2, перевіркою TLS, без перенаправлень і автоматичного повторення невизначеного PUT. Обмеження з’єднання — 5 с простою, 25 с на операцію.
- Рекомендовано `memory_limit=512M`; перед декодуванням маршрут перевіряє доступний запас пам’яті.
- Перевірте всі шари завантаження. Наприклад, PHP: `upload_max_filesize=8M`, `post_max_size=12M`; nginx: `client_max_body_size 12m;`. Ліміт запиту має враховувати multipart-обгортку. Перевірте також тайм-аути PHP-FPM/nginx та правила обмеження тіла в Cloudflare.
- Збережіть передавання Authorization до PHP. Пароль застосунку розбирає WordPress, а не код маршруту.

Якщо немає потрібної конфігурації, декодера або сумісного SDK, маршрут повертає контрольовану 503. Інші сторінки теми не потребують з’єднання з R2.

## Точна зміна Cloudflare

Власник або уповноважений адміністратор додає ще одну дозволену гілку **всередині чинної перевірки валідного edge key**:

```text
(http.host eq "webbooks.com.ua"
 and http.request.method eq "POST"
 and http.request.uri.path eq "/wp-json/webbooks-ai/v1/images")
```

Тобто логічна структура чинного механізму стає:

```text
ЧИННА_ПЕРЕВІРКА_ВАЛІДНОГО_EDGE_KEY
and (ЧИННІ_ДОЗВОЛЕНІ_ЗАПИТИ or НОВА_ГІЛКА_ВИЩЕ)
```

Другий приклад — схема розташування умов, не готовий вираз із такими ідентифікаторами. Підставте наявні умови без зміни їхньої перевірки секрету й без розширення дії правила. Наявність заголовка сама по собі не є авторизацією. Збережіть інші обмеження й захист. Новий шлях потребує тієї самої обробки, яка вже дозволяє авторизованому хмарному процесу звертатися до чинних API-маршрутів. Окреме правило, яке безумовно пропускає цей шлях, не потрібне.

Перевірте через публічний хост: чинний edge key і чинний Application Password проходять; без edge key запит зупиняє Cloudflare; із неправильним Application Password доступ зупиняє WordPress. WordPress не дублює секрет edge key. Production-правила автоматично не змінюються.

## Повтори й атомарність

Ключ визначає SHA-256 оригінальних байтів:

```text
articles/images/sha256/{перші-2-символи}/{64-символи-sha256}.{png|jpg|webp}
```

Новий об’єкт створюється через PutObject з `If-None-Match: *`. SDK перевіряється на підтримку умовного запису; перед відправленням додатково перевіряється сформований заголовок. Попереднього HEAD перед PUT немає. Конкурентний програш або невизначений результат PUT перевіряється через HEAD лише точного ключа. Збіг SHA-256 у метаданих, розміру та MIME означає наявний об’єкт. ETag не замінює SHA-256.

Новий об’єкт отримує правильний Content-Type, `Cache-Control: public, max-age=31536000, immutable` і метадані `sha256`. Суперечливий наявний об’єкт повертає 409; код його не перезаписує і не видаляє. Якщо стан неможливо підтвердити, повертається 503. Повторіть ті самі байти пізніше.

Джерела: [атомарні умовні операції R2](https://developers.cloudflare.com/r2/api/s3/extensions/#conditional-operations-in-putobject), [узгодженість R2](https://developers.cloudflare.com/r2/reference/consistency/#conditional-writes), [потокові завантаження AsyncAws S3](https://async-aws.com/clients/s3.html#upload-files).

## Виклик із хмарного клієнта

Змінні задайте у сховищі секретів клієнта. Не вмикайте shell tracing, curl verbose або дамп заголовків запиту.

```bash
curl --silent --show-error --max-time 90 \
  --request POST \
  --user "${WP_USERNAME}:${WP_APPLICATION_PASSWORD}" \
  --header "X-Webbooks-Edge-Key: ${WEBBOOKS_EDGE_KEY}" \
  --form "file=@${IMAGE_PATH}" \
  --dump-header "${RESPONSE_HEADERS_PATH}" \
  --output "${RESPONSE_BODY_PATH}" \
  --write-out '%{http_code}\n' \
  'https://webbooks.com.ua/wp-json/webbooks-ai/v1/images'
```

201 означає `created: true`; повтор дає 200 і `created: false`. Решта полів однакові: `url`, `sha256`, `mime_type`, `bytes`, `width`, `height`. Відповіді маршруту, включно з помилками WordPress, мають `Cache-Control: no-store`.

Клієнт вставляє URL у HTML з відповідним мовним alt, атрибутами width/height із відповіді й адаптивним оформленням. Значення URL та alt екрануються під час створення HTML.

## Помилки й діагностика

| HTTP | Код |
|---|---|
| 400 | webbooks_image_invalid_request |
| 401 / 403 | Штатна автентифікація або права WordPress |
| 413 | webbooks_image_too_large |
| 415 | webbooks_image_unsupported_type |
| 422 | webbooks_image_invalid_data |
| 409 | webbooks_image_object_conflict |
| 503 | webbooks_image_storage_unavailable |

Формат — WP_Error: code, message, data.status. Відповідь Cloudflare/nginx до входу в WordPress може мати інший формат. У діагностиці зберігайте лише HTTP-статус, Content-Type, CF-Ray та короткий код WordPress. Не зберігайте Authorization, edge key, секрети R2, вміст зображення або повний запит.

## Перевірки

Із Linux-каталогу проєкту, після встановлення Composer-залежностей у контейнері:

```bash
docker compose -p codex-webbooks-tests -f tests/compose.yml build tests
docker compose -p codex-webbooks-tests -f tests/compose.yml run --rm --interactive=false --entrypoint php tests /theme/tests/r2-upload.php
```

Прогін використовує лише ізольовану БД `webbooks_tests`, синтетичні зображення й імітацію S3-транспорту. Реальні R2-ключі не потрібні. Перевіряються оригінальні байти трьох форматів, повтори, межа 8 MiB та перевищення, пошкодження, розміри, конфлікти, невизначений PUT, недоступність, конкурентні процеси, штатні Application Passwords, права, HTTPS, no-store і незмінність медіатеки.

Окремого staging у власника немає. Реальну перевірку R2 та публічного Cloudflare виконуйте лише після дозволу на production-тест і з окремо створеними тестовими зображеннями. Стан живої перевірки та невиконані кроки фіксуються у підсумковому звіті; локальна імітація не вважається перевіркою справжнього R2.


### Production: 27 листопада 2026

Після налаштування прав R2-токена перевірено версію 2.3.0 через HTTPS origin: PNG/JPEG/WebP, два одночасні завантаження тих самих байтів (201 і 200), повтор із новим ім’ям, точні 8 MiB (201), перевищення (413), HTML замість зображення (415) та пошкоджений PNG (422). Публічні URL на data.webbooks.com.ua повернули 200 із правильними MIME та незміненим SHA-256. Медіатека й групи перекладів не змінилися; тимчасовий Application Password відкликано.

Публічний запит без WordPress-авторизації повернув 401. Повний сценарій хмарного клієнта з чинним edge key окремо не перевірявся; production-правила Cloudflare не змінювалися.
