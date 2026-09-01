#!/bin/bash
# Copy legacy globals from resources/js → public/js, then concat admin bundles.
# Page Vue islands stay in resources/components/ except the concatenations below.
set -euo pipefail

ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd )"
cd "$ROOT_DIR"

mkdir -p public/js

cp -f resources/js/*.js public/js/

concat_js() {
  local out="$1"
  shift
  : > "$out"
  local f
  for f in "$@"; do
    if [[ -f "$f" ]]; then
      cat "$f" >> "$out"
      printf '\n' >> "$out"
    else
      echo "Omitido (no existe): $f" >&2
    fi
  done
}

concat_js public/js/admin-runtime.js \
  public/js/jquery.js \
  public/js/materialize.min.js \
  public/js/start.js

concat_js public/js/admin-chrome.js \
  resources/components/NotificationsComponent.js \
  resources/components/SearchPalette.js

concat_js public/js/form-fields.js \
  resources/components/formComponents/formFieldTitle.js \
  resources/components/formComponents/formFieldBoolean.js \
  resources/components/formComponents/formFieldNumber.js \
  resources/components/formComponents/formFieldDate.js \
  resources/components/formComponents/formFieldTime.js \
  resources/components/formComponents/formFieldSelect.js \
  resources/components/formComponents/formFieldTextArea.js \
  resources/components/formComponents/formTextFormat.js \
  resources/components/formComponents/formImageSelector.js

concat_js public/js/dashboard-widgets.js \
  resources/components/widget/AlbumsWidgetComponent.js \
  resources/components/widget/CreateContents.js \
  resources/components/widget/FileExplorerCollection.js \
  resources/components/widget/PageCardComponent.js \
  resources/components/widget/UsersCollection.js

echo "Copied resources/js/*.js → public/js/ (+ admin-runtime, admin-chrome, form-fields, dashboard-widgets)"
