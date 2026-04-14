#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"
DOCKER_NODE_IMAGE="${DOCKER_NODE_IMAGE:-node:20-bookworm}"

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
  NODE_MAJOR="$(node -p "process.versions.node.split('.')[0]")"
  if [[ "$NODE_MAJOR" -lt 18 ]]; then
    use_local=false
    reasons+=("Node.js version $(node -v) is below the minimum supported version 18")
  fi
fi

if [[ "$use_local" == true ]]; then
  echo "✅ Local environment is compatible. Running local build..."
  npm run build:local
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
  -v "$ROOT_DIR":/app \
  -w /app \
  "$DOCKER_NODE_IMAGE" \
  bash -lc 'npm ci && npm run build:local'
