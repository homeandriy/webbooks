#!/usr/bin/env bash
set -euo pipefail
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"
ZIP_NAME="${1:-webbooks-theme-release.zip}"
THEME_DIRECTORY="webbooks"
if [[ ! "$ZIP_NAME" =~ ^webbooks[-A-Za-z0-9._]+\.zip$ ]]; then
  echo 'Release filename must be a webbooks*.zip basename inside the project.' >&2
  exit 1
fi
bash scripts/check-version-sync.sh
VERSION="$(tr -d '\r' < functions.php | sed -n "s/^const WEBBOOKS_VERSION[[:space:]]*=[[:space:]]*'\([^']\+\)';$/\1/p")"
grep -Fq "## $VERSION / " CHANGES.md || { echo 'Missing CHANGES.md release entry' >&2; exit 1; }
tr -d '\r' < README.txt | grep -Fxq "= $VERSION =" || { echo 'Missing README.txt changelog entry' >&2; exit 1; }
bash scripts/update-i18n.sh
[[ -f dist/.vite/manifest.json ]] || { echo 'Build dist before packaging.' >&2; exit 1; }
[[ -s dist/.vite/build-tag ]] || { echo 'Build dist with npm run build before packaging.' >&2; exit 1; }
STAGING_DIR="$(mktemp -d /tmp/webbooks-release.XXXXXX)"
[[ "$STAGING_DIR" == /tmp/webbooks-release.* ]] || exit 1
trap 'rm -rf -- "$STAGING_DIR"' EXIT
mkdir "$STAGING_DIR/$THEME_DIRECTORY"
# Explicit production allowlist. No IDE settings, tests, old templates or prior ZIPs.
for file in ./*.php style.css screenshot.png LICENSE.txt README.txt CHANGES.md; do
  cp "$file" "$STAGING_DIR/$THEME_DIRECTORY/"
done
for directory in assets inc src template template-parts dist; do
  cp -R "$directory" "$STAGING_DIR/$THEME_DIRECTORY/"
done
rm -rf -- "$STAGING_DIR/$THEME_DIRECTORY/assets/css/reference"
mkdir "$STAGING_DIR/$THEME_DIRECTORY/languages"
find languages -maxdepth 1 -type f \( -name '*.po' -o -name '*.mo' -o -name '*.pot' -o -name '*.l10n.php' \) -exec cp {} "$STAGING_DIR/$THEME_DIRECTORY/languages/" \;
# Vendor asset folders can contain original distribution ZIPs and editor backups.
find "$STAGING_DIR/$THEME_DIRECTORY" -type f \( -name '*.zip' -o -name '*~' -o -name '.DS_Store' \) -delete
rm -f -- "$ROOT_DIR/$ZIP_NAME"
(cd "$STAGING_DIR" && zip -rq "$ROOT_DIR/$ZIP_NAME" "$THEME_DIRECTORY")
ZIP_LISTING="$(unzip -Z1 "$ROOT_DIR/$ZIP_NAME")"
mapfile -t archive_roots < <(awk -F/ 'NF > 1 { print $1 }' <<< "$ZIP_LISTING" | sort -u)
if [[ "${#archive_roots[@]}" -ne 1 || "${archive_roots[0]:-}" != "$THEME_DIRECTORY" ]]; then
  echo "Release ZIP must contain exactly one theme directory: $THEME_DIRECTORY" >&2
  exit 1
fi
grep -Fxq "$THEME_DIRECTORY/dist/.vite/manifest.json" <<< "$ZIP_LISTING"
grep -Fxq "$THEME_DIRECTORY/dist/.vite/build-tag" <<< "$ZIP_LISTING"
grep -Eq "^$THEME_DIRECTORY/dist/assets/.*\\.js$" <<< "$ZIP_LISTING"
grep -Eq "^$THEME_DIRECTORY/dist/assets/.*\\.css$" <<< "$ZIP_LISTING"
archive_version="$(unzip -p "$ROOT_DIR/$ZIP_NAME" "$THEME_DIRECTORY/style.css" | sed -n 's/^[[:space:]]*\*[[:space:]]*Version:[[:space:]]*\([^[:space:]]*\).*$/\1/p' | head -n1)"
if [[ "$archive_version" != "$VERSION" ]]; then
  echo "Release ZIP theme version '$archive_version' does not match '$VERSION'." >&2
  exit 1
fi
if grep -Eq '(^|/)(\.idea|\.codex|node_modules|tests|reference|origianl-template|tmp)/|\.zip$' <<< "$ZIP_LISTING"; then
  echo 'Unexpected development artifact in release.' >&2
  exit 1
fi
echo "Release archive verified: $ZIP_NAME ($VERSION)"
