#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

DOCKER_NODE_IMAGE="${DOCKER_NODE_IMAGE:-node:22-bookworm}"
minimum_node_major=22
minimum_node_minor=13

has_compatible_node() {
  command -v node >/dev/null 2>&1 && command -v npm >/dev/null 2>&1 || return 1

  local node_major node_minor
  node_major="$(node -p "process.versions.node.split('.')[0]")"
  node_minor="$(node -p "process.versions.node.split('.')[1]")"

  [[ "$node_major" -gt "$minimum_node_major" || ( "$node_major" -eq "$minimum_node_major" && "$node_minor" -ge "$minimum_node_minor" ) ]]
}

if has_compatible_node; then
	echo "✅ Local Node.js $(node -v) is compatible. Running ESLint locally..."
  npm run lint:local
  exit 0
fi

if ! command -v docker >/dev/null 2>&1; then
  echo "❌ npm run lint requires Node.js >= ${minimum_node_major}.${minimum_node_minor} or Docker." >&2
  exit 1
fi

local_node_version="$(node -v 2>/dev/null || echo 'not installed')"
echo "⚠️ Local Node.js ${local_node_version} is incompatible; running ESLint in Docker (${DOCKER_NODE_IMAGE})."

docker run --rm \
  -u "$(id -u):$(id -g)" \
  --mount "type=bind,source=$ROOT_DIR,target=/workspace" \
  -w /workspace \
  -e HOME=/tmp \
  -e npm_config_cache=/tmp/npm-cache \
  "$DOCKER_NODE_IMAGE" \
  bash -lc 'npm ci && npm run lint:local'
