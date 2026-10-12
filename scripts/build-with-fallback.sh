#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"
DOCKER_NODE_IMAGE="${DOCKER_NODE_IMAGE:-node:22-bookworm}"
BUILD_TAG_FILE="$ROOT_DIR/dist/.vite/build-tag"

previous_build_tag=""
if [[ -f "$BUILD_TAG_FILE" ]]; then
  previous_build_tag="$(tr -d '\r\n' < "$BUILD_TAG_FILE")"
fi

verify_build_output() {
  local current_build_tag

  [[ -s "$ROOT_DIR/dist/.vite/manifest.json" ]] || {
    echo "❌ Build did not create dist/.vite/manifest.json in the project directory." >&2
    exit 1
  }

  find "$ROOT_DIR/dist/assets" -type f -name '*.js' -print -quit | grep -q . || {
    echo "❌ Build did not create a JavaScript bundle in dist/assets." >&2
    exit 1
  }

  find "$ROOT_DIR/dist/assets" -type f -name '*.css' -print -quit | grep -q . || {
    echo "❌ Build did not create a CSS bundle in dist/assets." >&2
    exit 1
  }

  [[ -s "$BUILD_TAG_FILE" ]] || {
    echo "❌ Build did not create $BUILD_TAG_FILE." >&2
    exit 1
  }

  current_build_tag="$(tr -d '\r\n' < "$BUILD_TAG_FILE")"
  if [[ "$current_build_tag" == "$previous_build_tag" ]]; then
    echo "❌ Build tag was not refreshed: $current_build_tag" >&2
    exit 1
  fi

  echo "✅ Build output is available in $ROOT_DIR/dist (tag: $current_build_tag)"
}

run_build() {
  npm run build:local
  verify_build_output
}

has_node=true
has_npm=true
use_local=true
reasons=()

if ! command -v node >/dev/null 2>&1; then
  has_node=false
  use_local=false
  reasons+=("node is not installed")
fi

if ! command -v npm >/dev/null 2>&1; then
  has_npm=false
  use_local=false
  reasons+=("npm is not installed")
fi

if [[ "$has_node" == true ]]; then
  NODE_MINOR="$(node -p "process.versions.node.split('.')[1]")"
  NODE_MAJOR="$(node -p "process.versions.node.split('.')[0]")"
  if [[ "$NODE_MAJOR" -lt 22 || ( "$NODE_MAJOR" -eq 22 && "$NODE_MINOR" -lt 13 ) ]]; then
    use_local=false
    reasons+=("Node.js version $(node -v) is below the minimum supported version 22.13")
  fi
fi

if [[ "$use_local" == true ]]; then
  echo "✅ Local environment is compatible. Running local build..."
  run_build
  exit 0
fi

if ! command -v docker >/dev/null 2>&1; then
  echo "❌ Local environment is incompatible: ${reasons[*]}." >&2
  echo "❌ Docker is not installed. Install Docker to run a containerized build (${DOCKER_NODE_IMAGE}, Debian 12 bookworm base)." >&2
  exit 1
fi

echo "⚠️ Local environment is incompatible: ${reasons[*]}"
echo "➡️ Falling back to Docker build (${DOCKER_NODE_IMAGE}, Debian 12 bookworm base)..."

docker run --rm \
  -u "$(id -u):$(id -g)" \
  --mount "type=bind,source=$ROOT_DIR,target=/workspace" \
  -w /workspace \
  -e HOME=/tmp \
  -e npm_config_cache=/tmp/npm-cache \
  "$DOCKER_NODE_IMAGE" \
  bash -lc 'npm ci && npm run build:local'

verify_build_output
