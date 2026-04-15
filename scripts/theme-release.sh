#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

get_current_version() {
  sed -n "s/^const WEBBOOKS_VERSION[[:space:]]*=[[:space:]]*'\([^']\+\)';$/\1/p" functions.php | head -n1
}

bump_patch() {
  local version="$1"
  IFS='.' read -r major minor patch <<< "$version"

  if [[ -z "${major:-}" || -z "${minor:-}" || -z "${patch:-}" || ! "$major" =~ ^[0-9]+$ || ! "$minor" =~ ^[0-9]+$ || ! "$patch" =~ ^[0-9]+$ ]]; then
    echo "❌ Некоректний формат версії: '$version'. Очікується X.Y.Z" >&2
    exit 1
  fi

  # Carry-at-10: each component rolls over from 10 → 0 and increments the next.
  patch=$((patch + 1))
  if [[ $patch -gt 10 ]]; then
    patch=0
    minor=$((minor + 1))
    if [[ $minor -gt 10 ]]; then
      minor=0
      major=$((major + 1))
    fi
  fi

  echo "${major}.${minor}.${patch}"
}

CURRENT_VERSION="$(get_current_version)"
if [[ -z "$CURRENT_VERSION" ]]; then
  echo "❌ Не вдалося зчитати WEBBOOKS_VERSION з functions.php" >&2
  exit 1
fi

NEW_VERSION="$(bump_patch "$CURRENT_VERSION")"

echo "🔁 Bump version: ${CURRENT_VERSION} -> ${NEW_VERSION}"

perl -0777 -i -pe "s/(const WEBBOOKS_VERSION\s*=\s*')${CURRENT_VERSION}(';)/\${1}${NEW_VERSION}\${2}/g" functions.php
perl -0777 -i -pe "s/^([[:space:]]*\* Version:[[:space:]]*)${CURRENT_VERSION}/\${1}${NEW_VERSION}/gm" style.css
perl -0777 -i -pe "s/^([[:space:]]*\* Template Version:[[:space:]]*)${CURRENT_VERSION}/\${1}${NEW_VERSION}/gm" style.css

echo "🔎 Перевірка синхронізації версій"
bash scripts/check-version-sync.sh

#echo "🔎 Перевірка релізних нотаток"
#if ! grep -Eq "^## ${NEW_VERSION} / [0-9]{4}-[0-9]{2}-[0-9]{2}$" CHANGES.md; then
#  echo "❌ CHANGES.md має містити секцію для версії ${NEW_VERSION} у форматі: ## ${NEW_VERSION} / YYYY-MM-DD" >&2
#  exit 1
#fi

#if ! grep -Eq "^= ${NEW_VERSION} =$" README.txt; then
#  echo "❌ README.txt має містити запис у секції Changelog для версії ${NEW_VERSION} у форматі: = ${NEW_VERSION} =" >&2
#  exit 1
#fi

echo "🌐 Оновлення перекладів"
npm run i18n:update

echo "🧹 Видалення dist"
rm -rf dist

echo "🏗️ Build"
npm run build

echo "🧹 Видалення попереднього ZIP"
rm -f webbooks-theme-release.zip

echo "📦 Створення релізного ZIP"
bash scripts/build-release.sh webbooks-theme-release.zip

echo "✅ Релізний пакет готовий: webbooks-theme-release.zip"
