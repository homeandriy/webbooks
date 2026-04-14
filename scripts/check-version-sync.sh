#!/usr/bin/env bash
set -euo pipefail

root_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

functions_version="$(sed -n "s/^const WEBBOOKS_VERSION = '\([^']\+\)';$/\1/p" "$root_dir/functions.php" | head -n1)"
style_version="$(sed -n 's/^\* Version: \(.*\)$/\1/p' "$root_dir/style.css" | head -n1 | xargs)"
template_version="$(sed -n 's/^\* Template Version: \(.*\)$/\1/p' "$root_dir/style.css" | head -n1 | xargs)"

if [[ -z "$functions_version" || -z "$style_version" || -z "$template_version" ]]; then
  echo "❌ Не вдалося зчитати одну або більше версій з functions.php/style.css"
  exit 1
fi

if [[ "$functions_version" != "$style_version" || "$functions_version" != "$template_version" ]]; then
  echo "❌ Версії не синхронізовані:"
  echo "   WEBBOOKS_VERSION:  $functions_version"
  echo "   style.css Version: $style_version"
  echo "   Template Version:  $template_version"
  echo "   Вимога: WEBBOOKS_VERSION === Version === Template Version"
  exit 1
fi

echo "✅ Версії синхронізовані: $functions_version"
