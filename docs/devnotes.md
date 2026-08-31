# Dev notes — Start CMS 3.0

Arranque actual (no Gulp, no `php -S :8000` como flujo principal):

```bash
cp .env.example .env          # MySQL del host: DATABASE_HOSTNAME=host.docker.internal
docker compose up -d          # http://localhost:8081  admin: gerber / admin123
npm install
npm run build                 # o npm run watch
```

Composer **dentro** del contenedor (`docker exec ci_php56 composer install`), no en el host con PHP 8+.

Alternativa sin Docker: `./bin/server.sh` (PHP built-in). El panel y las URLs de producción esperan Apache + `APP_BASE_URL`.
