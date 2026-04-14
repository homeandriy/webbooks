#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "Running i18n update workflow..."
bash scripts/update-i18n.sh

DIST_DIR="dist"
MANIFEST_PATH="$DIST_DIR/.vite/manifest.json"
ZIP_NAME="${1:-webbooks-theme-release-$(date -u +%Y%m%d-%H%M%S).zip}"

if [[ ! -d "$DIST_DIR" ]]; then
  echo "Error: '$DIST_DIR/' is missing. Run 'npm run build' locally or in CI before creating release ZIP." >&2
  exit 1
fi

if [[ ! -f "$MANIFEST_PATH" ]]; then
  echo "Error: '$MANIFEST_PATH' is missing. Build artifacts are incomplete." >&2
  exit 1
fi

if find "$DIST_DIR" -mindepth 1 -maxdepth 1 -type f | grep -q .; then
  echo "Error: build artifacts must stay inside '$DIST_DIR/' subdirectories (no flattening into root)." >&2
  exit 1
fi

rm -f "$ZIP_NAME"

zip -rq "$ZIP_NAME" . \
  -x ".git/*" \
     "node_modules/*" \
     "$ZIP_NAME"

ZIP_LISTING="$(unzip -Z1 "$ZIP_NAME")"

if ! grep -qx "$MANIFEST_PATH" <<< "$ZIP_LISTING"; then
  echo "Error: '$MANIFEST_PATH' was not included in '$ZIP_NAME'." >&2
  exit 1
fi

if ! grep -Eq '^(\./)?dist/' <<< "$ZIP_LISTING"; then
  echo "Error: 'dist/' directory was not included in '$ZIP_NAME'." >&2
  exit 1
fi

if ! grep -Eq '^(\./)?dist/assets/.*\.js$' <<< "$ZIP_LISTING"; then
  echo "Error: no JavaScript bundles found under 'dist/assets/' in '$ZIP_NAME'." >&2
  exit 1
fi

if ! grep -Eq '^(\./)?dist/assets/.*\.css$' <<< "$ZIP_LISTING"; then
  echo "Error: no CSS bundles found under 'dist/assets/' in '$ZIP_NAME'." >&2
  exit 1
fi

if ! grep -Eq '^(\./)?dist/assets/.*\.(woff2?|ttf|otf|eot|svg|png|jpe?g|gif|webp|avif)$' <<< "$ZIP_LISTING"; then
  echo "Error: no static assets (fonts/images) found under 'dist/assets/' in '$ZIP_NAME'." >&2
  exit 1
fi

echo "Release archive created: $ZIP_NAME"
echo "Verified: full '$DIST_DIR/' structure is present in the archive, including '$MANIFEST_PATH' and dist/assets/* bundles."
