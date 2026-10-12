# Webbooks

Класична WordPress-тема для каталогу книг і статей. Gutenberg і block theme не потрібні.

- Мінімум: WordPress 6.5, PHP 8.2, Node 22.13 для збірки.
- PHP 8.5 перевіряється ізольованим Docker-прогоном; точний обсяг перевірки — у [REGRESSION_CHECKLIST.md](REGRESSION_CHECKLIST.md).
- Один пакет теми: сервіси у `src/`, контролери й налаштування у `inc/`, розмітка у `template-parts/`.
- Реліз 2.0.9 об’єднує основу 1.9.6 з підтримкою автора, Telegram, Polylang, картками статей, Load more та підсвіткою коду з 2.0.8; фронтенд працює на Bootstrap 5 і Swiper.
- Інсталяційні ID, реклама й партнерські посилання: **Налаштування → Webbooks**.
- CSS/JS/шрифти/зображення: `assets/`; ресурси портфоліо: `assets/portfolio/`, фото: `assets/images/portfolio/`. HTML-референси: `reference/html/`, CSS референсів: `assets/css/reference/`; вони не входять у реліз.
- Деталі встановлення, збірки й історія версій: [README.txt](README.txt) та [CHANGES.md](CHANGES.md).

```sh
npm ci
npm run lint
npm run build
WITH_PLUGINS=1 docker compose -p codex-webbooks-tests -f tests/compose.yml up --build --abort-on-container-exit --exit-code-from tests
bash scripts/build-release.sh webbooks-theme-release.zip
```

Пакувальник створює один інсталяційний ZIP теми з усіма сервісами. Перед публікацією перенесіть чинні налаштування сайту і перевірте всі активні плагіни на staging. Релізний bump потребує синхронізації трьох версій та обох changelog-файлів згідно з AGENTS.md.