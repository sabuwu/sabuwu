#!/usr/bin/env bash
set -euo pipefail
script_dir="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
backup_dir="${1:?Usage: restore.sh backups/<timestamp> [--yes]}"
[[ -f "$backup_dir/database.sql" && -f "$backup_dir/wp-content.tar.gz" ]] || { echo 'Incomplete backup.' >&2; exit 1; }
if [[ "${2:-}" != --yes ]]; then
    printf 'This overwrites the local database and wp-content. Type RESTORE: '
    read -r answer
    [[ "$answer" == RESTORE ]] || exit 1
fi
"$script_dir/compose.sh" stop cron wordpress
"$script_dir/compose.sh" exec -T database sh -c 'MYSQL_PWD="$(cat /run/portfolio-secrets/DB_PASSWORD)" exec mariadb --user="$MARIADB_USER" "$MARIADB_DATABASE"' < "$backup_dir/database.sql"
tar -xzf "$backup_dir/wp-content.tar.gz" -C "$script_dir/../app/wordpress"
"$script_dir/compose.sh" up -d wordpress cron

