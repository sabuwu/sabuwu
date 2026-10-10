#!/usr/bin/env bash
set -euo pipefail
script_dir="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
project_root="$(cd -- "$script_dir/.." && pwd)"
source_file="${1:-$project_root/database/seeds/content.json}"
[[ -f "$source_file" ]] || { echo "Snapshot not found: $source_file" >&2; exit 1; }
if [[ "${2:-}" != --yes ]]; then
    printf 'This replaces posts, pages, menus and editorial settings. Type IMPORT: '
    read -r answer
    [[ "$answer" == IMPORT ]] || exit 1
fi
# stdin avoids requiring arbitrary host paths inside a container.
"$script_dir/compose.sh" exec -T wordpress sh -c 'umask 077; cat > /tmp/portfolio-content-import.json' < "$source_file"
"$script_dir/compose.sh" exec -T wordpress wp eval-file /opt/portfolio/scripts/content-snapshot.php import /tmp/portfolio-content-import.json
"$script_dir/compose.sh" exec -T wordpress rm -f /tmp/portfolio-content-import.json

