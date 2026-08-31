# Docker — Start CMS 3.0

Compose levanta **solo** Apache + PHP 7.4. MySQL corre en el host. El contenedor se llama `ci_php56` y publica **8081**.

## Qué incluye

| Pieza | Dónde |
|---|---|
| PHP 7.4 + Apache + Composer | Imagen del `Dockerfile`, contenedor `ci_php56` |
| Código | Volume `.:/var/www/html` |
| MySQL 5.7+ | **Host** (no hay servicio `db` en `docker-compose.yml`) |
| Red al host | `extra_hosts: host.docker.internal:host-gateway` |

No hay contenedor `ci_mysql57`. `docker compose down -v` **no** borra la base: los datos viven fuera de Compose.

## Requisitos

- Docker 20.10+ con el plugin Compose (`docker compose`)
- MySQL 5.7+ accesible desde el contenedor, base `start_cms_db`
- ~512 MB RAM para el contenedor web

## Arranque

```bash
git clone https://github.com/gervisbermudez/startCodeIgniter-CSM.git
cd startCodeIgniter-CSM
cp .env.example .env
# Creá start_cms_db en tu MySQL si no existe; importá application/database/start.sql
# y las migraciones en application/database/migrations/ que aún no hayas aplicado.
docker compose up -d
```

O `./bin/install.sh` (comprueba Docker y levanta `ci_php56`; no crea MySQL).

| | |
|---|---|
| Sitio | http://localhost:8081 |
| Admin | http://localhost:8081/admin/login |
| Usuario / clave | `gerber` / `admin123` |

## `.env`

```env
APP_ENV=development
APP_BASE_URL=http://localhost:8081/

DATABASE_HOSTNAME=host.docker.internal
DATABASE_DATABASE=start_cms_db
DATABASE_USER=ci_user
DATABASE_PASSWORD=ci_pass
```

`host.docker.internal` es el host desde el contenedor (Docker Desktop, o `host-gateway` en Linux/WSL). No uses `db` salvo que vos agregues un servicio MySQL.

En un **preview de worktree** cambiá `APP_BASE_URL` y `SESS_COOKIE_NAME` (puerto 8082–8099). No toques `DATABASE_*`. Receta: `.cursor/rules/worktree-preview.mdc`.

## Compose actual

```yaml
services:
  web:
    build: .
    container_name: ci_php56
    ports:
      - "8081:80"
    volumes:
      - ./:/var/www/html
    extra_hosts:
      - "host.docker.internal:host-gateway"
```

`container_name: ci_php56` es fijo. Un segundo `docker compose up` en otro checkout **pisa** este contenedor. En worktrees usá `docker run`, nunca Compose.

## Tareas habituales

```bash
docker exec -it ci_php56 bash
docker logs -f ci_php56
docker exec ci_php56 composer install
docker exec ci_php56 php -r "echo password_hash('mypassword', PASSWORD_BCRYPT);"
```

MySQL (en el **host**):

```bash
mysql -h 127.0.0.1 -u ci_user -pci_pass start_cms_db
mysqldump -h 127.0.0.1 -u ci_user -pci_pass start_cms_db > backup.sql
mysql -h 127.0.0.1 -u ci_user -pci_pass start_cms_db < backup.sql
```

Caché de la app:

```bash
docker exec ci_php56 rm -rf /var/www/html/application/cache/*
docker exec ci_php56 rm -rf /var/www/html/themes/*/cache/*
```

No borres `application/cache/sessions` a ciegas: las sesiones CI viven ahí (fuera de `/tmp`).

## Problemas

**El contenedor no arranca** — `docker ps -a`, `docker logs ci_php56`. Puerto 8081 ocupado: otro proceso o un compose viejo. `docker compose down` (sin `-v` da igual para la DB) y volvé a `up -d`.

**Error de conexión a MySQL** — desde el contenedor el host no es `localhost` ni `db`:

```bash
docker exec ci_php56 php -r "
\$m = new mysqli('host.docker.internal', 'ci_user', 'ci_pass', 'start_cms_db');
echo \$m->connect_error ? \$m->connect_error : 'ok';
"
```

**Permisos** — el entrypoint ajusta `uploads/`, `application/cache`, `application/logs`. Si falla, `docker compose restart` (en el checkout **principal**).

**Cambiar la clave de `gerber`**

```bash
HASH=$(docker exec ci_php56 php -r "echo password_hash('newpassword', PASSWORD_BCRYPT);")
mysql -h 127.0.0.1 -u ci_user -pci_pass start_cms_db -e "UPDATE user SET password='$HASH' WHERE username='gerber';"
```

## Producción

1. `.env` con secretos reales (`JWT_SECRET_KEY`, clave de admin, DB).
2. `APP_ENV=production`.
3. Reverse proxy TLS delante de Apache.
4. Backups: [AUTOMATIC_BACKUPS.md](AUTOMATIC_BACKUPS.md).
5. No publiques `8081` en crudo a internet.

## Worktrees

El volume de `ci_php56` es el checkout principal. El código de un worktree **no** se ve en `:8081`. Preview: Apache propio, puerto `8082–8099`, misma `start_cms_db`. Nunca `docker compose up|down|restart` desde un worktree.
