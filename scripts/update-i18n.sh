#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

POT_FILE="languages/webbooks.pot"
PO_FILES=(
  "languages/webbooks-en_US.po"
  "languages/webbooks-uk_UA.po"
  "languages/webbooks-ru_RU.po"
  "languages/webbooks-pl_PL.po"
)

mapfile -t PHP_FILES < <(find . -type f -name '*.php' -not -path './vendor/*' -not -path './.git/*' | sort)

if [[ ${#PHP_FILES[@]} -eq 0 ]]; then
  echo "Error: no PHP files found for xgettext." >&2
  exit 1
fi

echo "Generating POT template..."
xgettext --from-code=UTF-8 --language=PHP \
  --keyword=__ --keyword=_e --keyword=_x:1,2c \
  --keyword=esc_html__ --keyword=esc_html_e --keyword=esc_html_x:1,2c \
  --keyword=esc_attr__ --keyword=esc_attr_e --keyword=esc_attr_x:1,2c \
  --keyword=_n:1,2 \
  --add-comments=translators \
  --package-name='webbooks' \
  --output="$POT_FILE" \
  "${PHP_FILES[@]}"

echo "Deduplicating POT entries with msguniq..."
msguniq --use-first -o "$POT_FILE" "$POT_FILE"

echo "Checking POT duplicates..."
if msguniq -d "$POT_FILE" | grep -q 'msgid'; then
  echo "Error: duplicate msgid entries found in $POT_FILE after deduplication." >&2
  exit 1
fi

echo "Merging POT updates into PO catalogs..."
for po_file in "${PO_FILES[@]}"; do
  msguniq --use-first -o "$po_file" "$po_file"
  msgmerge --update "$po_file" "$POT_FILE"
done

echo "Compiling MO binaries from PO catalogs..."
if ! command -v msgfmt >/dev/null 2>&1; then
  echo "Error: msgfmt is required to build .mo files but is not installed." >&2
  exit 1
fi

for po_file in "${PO_FILES[@]}"; do
  mo_file="${po_file%.po}.mo"
  msgfmt --check --output-file="$mo_file" "$po_file"
done

echo "i18n catalogs and MO binaries updated successfully."
