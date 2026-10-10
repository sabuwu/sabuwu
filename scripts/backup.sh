#!/usr/bin/env bash
set -euo pipefail
script_dir="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
project_root="$(cd -- "$script_dir/.." && pwd)"
backup_dir="$project_root/backups/$(date +%Y%m%d-%H%M%S)"
umask 077
mkdir -p "$backup_dir"
"$script_dir/compose.sh" exec -T database sh -c 'MYSQL_PWD="$(cat /run/portfolio-secrets/DB_PASSWORD)" exec mariadb-dump --user="$MARIADB_USER" --single-transaction --skip-lock-tables "$MARIADB_DATABASE"' > "$backup_dir/database.sql"
tar --exclude='wp-content/cache' --exclude='wp-content/updraft' --exclude='wp-content/aiowps_backups' \
    -C "$project_root/app/wordpress" -czf "$backup_dir/wp-content.tar.gz" wp-content
printf 'Private operational backup: %s\nSQL contains user hashes and private plugin settings. Keep outside Git.\n' "$backup_dir"

