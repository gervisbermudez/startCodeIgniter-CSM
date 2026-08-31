#!/usr/bin/env bash
# Prepara un Git worktree local para este CMS.
# Cursor lo ejecuta al crear el worktree (Agents Window, /worktree, CLI).
# No arranca Docker ni Cloud Agents: el stack sigue en el checkout principal.
set -euo pipefail

ROOT="${ROOT_WORKTREE_PATH:-}"
if [[ -z "$ROOT" ]]; then
  echo "ROOT_WORKTREE_PATH no está definido; no hay checkout principal que copiar." >&2
  exit 1
fi

copy_tree() {
  local src="$1"
  local dest="$2"
  if [[ ! -e "$src" ]]; then
    echo "Omitido (no existe en el checkout principal): $src"
    return 0
  fi
  if [[ -d "$dest" ]] && [[ -n "$(ls -A "$dest" 2>/dev/null || true)" ]]; then
    echo "Ya existe, no se pisa: $dest"
    return 0
  fi
  mkdir -p "$dest"
  cp -a "$src/." "$dest/"
  echo "Copiado $src → $dest"
}

copy_file() {
  local src="$1"
  local dest="$2"
  if [[ ! -f "$src" ]]; then
    echo "Omitido (no existe): $src"
    return 0
  fi
  if [[ -f "$dest" ]]; then
    echo "Ya existe, no se pisa: $dest"
    return 0
  fi
  cp -a "$src" "$dest"
  echo "Copiado $src → $dest"
}

if [[ -f "$ROOT/.env" ]]; then
  copy_file "$ROOT/.env" ".env"
elif [[ -f ".env.example" ]]; then
  copy_file ".env.example" ".env"
else
  echo "Aviso: no hay .env ni .env.example; CodeIgniter no tendrá DB hasta que copies uno." >&2
fi

# vendor/ sale de PHP 7.4 en Docker; no correr composer en el host (suele ser PHP 8+).
copy_tree "$ROOT/vendor" "vendor"
if [[ ! -f vendor/autoload.php ]]; then
  echo "Aviso: vendor/ incompleto. En el checkout principal: docker exec ci_php56 composer install" >&2
fi

copy_tree "$ROOT/node_modules" "node_modules"
copy_tree "$ROOT/themes" "themes"

mkdir -p application/cache uploads trash backups/database
touch application/cache/.gitkeep uploads/.gitkeep trash/.gitkeep 2>/dev/null || true

echo "Worktree listo (local). No ejecutes docker compose aquí: ci_php56 y :8081 viven en el checkout principal."
echo "Para probar este árbol: docker run aislado (puerto 8082+, misma start_cms_db). Ver .cursor/rules/worktree-preview.mdc."
