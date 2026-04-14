# AGENTS.md

## Правило синхронізації версій (обов'язково)
При **кожній зміні коду**, яка включає version bump, потрібно синхронно оновлювати:

1. `functions.php` → `WEBBOOKS_VERSION`
2. `style.css` → `Version`
3. `style.css` → `Template Version`

Умова обов'язкова:

`WEBBOOKS_VERSION === Version === Template Version`

## Правило релізних нотаток (обов'язково)
При **кожному version bump** обов'язково оновлювати:

- `CHANGES.md` — додати новий запис із датою та списком змін.
- `README.txt` — оновити секцію `Changelog` / версії.

## Короткий чекліст перед комітом
- [ ] Версії синхронізовані у 3 місцях (`functions.php`, `style.css: Version`, `style.css: Template Version`).
- [ ] `CHANGES.md` оновлено.
- [ ] `README.txt` оновлено.

## Опційно: pre-commit перевірка
Щоб не пропускати розсинхрон версій, можна увімкнути pre-commit хук:

1. `git config core.hooksPath .githooks`
2. Переконатися, що файл `.githooks/pre-commit` має права на виконання.

Хук викликає `scripts/check-version-sync.sh` і блокує commit при розбіжності версій.
