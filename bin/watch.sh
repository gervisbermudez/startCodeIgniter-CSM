#!/usr/bin/env bash
# Watch SCSS (Vite) and copy resources/js → public/js when start.js changes.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

bash bin/copy-js.sh
node bin/watch-js.mjs &
WATCH_PID=$!
cleanup() { kill "$WATCH_PID" 2>/dev/null || true; }
trap cleanup EXIT INT TERM
npx vite build --watch
