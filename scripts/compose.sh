#!/usr/bin/env bash
set -euo pipefail
project_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$project_root"
if [[ "${COMPOSE_ENGINE:-}" == podman ]]; then
    exec podman compose "$@"
elif [[ "${COMPOSE_ENGINE:-}" == docker ]]; then
    exec docker compose "$@"
elif command -v docker >/dev/null && docker info >/dev/null 2>&1; then
    exec docker compose "$@"
elif command -v podman >/dev/null && podman info >/dev/null 2>&1; then
    exec podman compose "$@"
else
    echo 'No accessible container engine. Start Docker or set COMPOSE_ENGINE=podman.' >&2
    exit 1
fi

