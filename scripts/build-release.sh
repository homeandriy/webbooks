#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

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

rm -f "$ZIP_NAME"

zip -rq "$ZIP_NAME" . \
  -x ".git/*" \
     "node_modules/*" \
     "$ZIP_NAME"

if ! unzip -Z1 "$ZIP_NAME" | grep -qx "$MANIFEST_PATH"; then
  echo "Error: '$MANIFEST_PATH' was not included in '$ZIP_NAME'." >&2
  exit 1
fi

if ! unzip -Z1 "$ZIP_NAME" | grep -q '^dist/'; then
  echo "Error: 'dist/' directory was not included in '$ZIP_NAME'." >&2
  exit 1
fi

echo "Release archive created: $ZIP_NAME"
echo "Verified: '$DIST_DIR/' and '$MANIFEST_PATH' are present in the archive."
