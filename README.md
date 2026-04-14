## Webbooks Theme ##

Contributors: (this should be a list of wordpress.org userid's)
Donate link: https://webbooks.com.ua/portfolio
Tags: comments, spam
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

[![Build Status](https://camo.githubusercontent.com/f51101fd3a1a9f49bd78fbb8f2fe957bc4d68358/687474703a2f2f7068703772656164792e74696d6573706c696e7465722e63682f7068702d6170692d636c69656e74732f7472617669732f62616467652e737667)](https://travis-ci.org/joemccann/dillinger)

#### Description ####

This is the long description.  No limit, and you can use Markdown (as well as in the following sections).

For backwards compatibility, if this section is missing, the full length of the short description will be used, and
Markdown parsed.

A few notes about the sections above:

*   "Contributors" is a comma separated list of wp.org/wp-plugins.org usernames
*   "Tags" is a comma separated list of tags that apply to the plugin
*   "Requires at least" is the lowest version that the plugin will work on
*   "Tested up to" is the highest version that you've *successfully used to test the plugin*. Note that it might work on
higher versions... this is just the highest one you've verified.
*   Stable tag should indicate the Subversion "tag" of the latest stable version, or "trunk," if you use `/trunk/` for
stable.

    Note that the `readme.txt` of the stable tag is the one that is considered the defining one for the plugin, so
if the `/trunk/readme.txt` file says that the stable tag is `4.3`, then it is `/tags/4.3/readme.txt` that'll be used
for displaying information about the plugin.  In this situation, the only thing considered from the trunk `readme.txt`
is the stable tag pointer.  Thus, if you develop in trunk, you can update the trunk `readme.txt` to reflect changes in
your in-development version, without having that information incorrectly disclosed about the current stable version
that lacks those changes -- as long as the trunk's `readme.txt` points to the correct stable tag.

    If no stable tag is provided, it is assumed that trunk is stable, but you should specify "trunk" if that's where
you put the stable version, in order to eliminate any doubt.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `books-s3-prepare.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==
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

## Системні вимоги

- **Node.js:** `>= 18`
- **npm:** встановлений у системі (рекомендовано актуальний LTS-разом із Node.js)

## Локальна збірка

```bash
npm install
npm run build
```

Після успішної збірки фронтенд-артефакти зʼявляються в каталозі `dist/`.

### Fallback-збірка через Docker (рекомендовано)

Якщо локальне оточення не підходить (немає Node.js/npm або версія Node.js застаріла), використовуйте:

```bash
./scripts/build-with-fallback.sh
```

Рекомендований Docker-образ для fallback-збірки: `node:20-bookworm`.

За потреби тег можна перевизначити змінною оточення:

```bash
DOCKER_NODE_IMAGE=node:20 ./scripts/build-with-fallback.sh
```

## Політика щодо `dist/`

- `dist/` **не комітимо** в репозиторій;
- продакшн-артефакти збираються в CI (або локально перед `release:prepare`);
- для ZIP-релізу `dist/` має бути присутній у зібраному архіві.

## Release через ZIP

Для релізного архіву обовʼязково включайте:

- каталог `dist/`;
- маніфест `dist/.vite/manifest.json`.
- JS-бандли `dist/assets/*.js`;
- CSS-бандли `dist/assets/*.css`;
- статичні файли `dist/assets/*.{woff,woff2,ttf,otf,eot,svg,png,jpg,jpeg,gif,webp,avif,...}`.

Важливо:

- не переміщуйте зібрані файли з `dist/` в корінь теми після build;
- релізний ZIP має зберігати повну структуру `dist/` без «плаского» копіювання.

Рекомендована перевірка перед архівацією:

```bash
test -d dist && test -f dist/.vite/manifest.json
```

Release workflow (однією командою):

```bash
npm run theme
```

Що робить `npm run theme`:

1. Робить patch bump версії (`X.Y.Z -> X.Y.(Z+1)`) із `WEBBOOKS_VERSION` у `functions.php`.
2. Синхронно оновлює `functions.php`, `style.css: Version`, `style.css: Template Version`.
3. Валідовує рівність трьох версій через `bash scripts/check-version-sync.sh`.
4. Запускає `npm run i18n:update`, rebuild `dist/` і пакування `webbooks-theme-release.zip`.
5. Перевіряє, що для нової версії оновлені `CHANGES.md` і `README.txt` (changelog), інакше зупиняється з помилкою.

## Шляхи підключення ассетів у темі

- маніфест читається з `dist/.vite/manifest.json`;
- URL бандлів формуються з префіксом `/dist/` (через `get_template_directory_uri() . '/dist/' ...` у `inc/assets.php`).

## Підключення `webbooks-bundle` та сумісність з оптимізаторами

- `webbooks-bundle` має завантажуватись як `<script type="module">` (не як `text/javascript`).
- Не застосовуйте глобальний rewrite `type="text/javascript"` для всіх скриптів, бо це ламає module-бандл теми.
- Якщо CDN/optimizer вимагає винятків, додавайте виняток саме для хендла `webbooks-bundle-js` (а також суміжних `webbooks-bundle`/`webbooks-bundle-*`, якщо плагін працює за префіксом).

## Після деплою

Обовʼязково очистіть кеші оптимізаційних плагінів і CDN (Cloudflare, LiteSpeed Cache, WP Rocket тощо), щоб не віддавався старий або переписаний шлях до ассетів.

## Fallback-поведінка без `dist`

- якщо `dist/` відсутній, тема використовує fallback-поведінку без Vite-артефактів;
- у такому режимі працює базовий функціонал, але без оптимізованого продакшн-бандла;
- для релізу fallback-режим **не допускається** — перед пакуванням треба виконати `npm run build`.

## i18n workflow (коротко: POT / PO / MO)

1. Оновіть POT-шаблон:
   ```bash
   xgettext --from-code=UTF-8 --language=PHP \
     --keyword=__ --keyword=_e --keyword=_x:1,2c \
     --keyword=esc_html__ --keyword=esc_html_e --keyword=esc_html_x:1,2c \
     --keyword=esc_attr__ --keyword=esc_attr_e --keyword=esc_attr_x:1,2c \
     --keyword=_n:1,2 \
     --add-comments=translators \
     --package-name='webbooks' \
     --output=languages/webbooks.pot \
     $(find . -type f -name '*.php' -not -path './vendor/*' -not -path './.git/*' | sort)
   ```
2. Обовʼязково приберіть дублікати в POT:
   ```bash
   msguniq --use-first -o languages/webbooks.pot languages/webbooks.pot
   ```
3. Перевірте POT на дублікати перед merge:
   ```bash
   msguniq -d languages/webbooks.pot
   ```
4. Оновіть PO-файли через `msgmerge`:
   ```bash
   msgmerge --update languages/webbooks-en_US.po languages/webbooks.pot
   msgmerge --update languages/webbooks-uk_UA.po languages/webbooks.pot
   msgmerge --update languages/webbooks-ru_RU.po languages/webbooks.pot
   msgmerge --update languages/webbooks-pl_PL.po languages/webbooks.pot
   ```
5. Додайте переклади для нових `msgid` у кожній мові.
6. Згенеруйте MO-файли через `msgfmt`:
   ```bash
   msgfmt languages/webbooks-en_US.po -o languages/webbooks-en_US.mo
   msgfmt languages/webbooks-uk_UA.po -o languages/webbooks-uk_UA.mo
   msgfmt languages/webbooks-ru_RU.po -o languages/webbooks-ru_RU.mo
   msgfmt languages/webbooks-pl_PL.po -o languages/webbooks-pl_PL.mo
   ```

Для автоматичного запуску цього workflow у релізі використовуйте:

```bash
npm run i18n:update
```
