#!/bin/bash
# Copy legacy globals from resources/js → public/js (footer loads public/js/start.js).
# Do not copy resources/components/ — the admin loads those files from resources/.

ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd )"
cd "$ROOT_DIR"

mkdir -p public/js
cp -f resources/js/*.js public/js/

echo "Copied resources/js/*.js → public/js/"
