#!/usr/bin/env bash
set -euo pipefail
script_dir="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
project_root="$(cd -- "$script_dir/.." && pwd)"
target="${1:-$project_root/database/seeds/content.json}"
mkdir -p -- "$(dirname -- "$target")"
temporary="$(mktemp "${target}.XXXXXX.tmp")"
trap 'rm -f -- "$temporary"' EXIT
"$script_dir/wp.sh" --quiet --skip-plugins --skip-themes eval-file /opt/portfolio/scripts/content-snapshot.php export > "$temporary"
python3 -c 'import json,sys; d=json.load(open(sys.argv[1])); assert d["format"] == "sabuwu-content-v1"' "$temporary"
mv -- "$temporary" "$target"
printf 'Editorial snapshot: %s\nReview post content and media before committing.\n' "$target"

