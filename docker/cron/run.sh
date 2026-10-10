#!/usr/bin/env bash
set -euo pipefail
interval="${CRON_INTERVAL:-60}"
[[ "$interval" =~ ^[0-9]+$ ]] && (( interval >= 10 && interval <= 3600 )) || {
    echo 'CRON_INTERVAL must be between 10 and 3600 seconds.' >&2
    exit 1
}
trap 'exit 0' TERM INT
while true; do
    if wp cron event run --due-now; then
        date +%s > /tmp/portfolio-cron-heartbeat
    else
        echo 'WP-Cron failed; heartbeat remains stale.' >&2
    fi
    sleep "$interval" &
    wait $! || true
done

