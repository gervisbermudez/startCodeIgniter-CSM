# Prompt para ejecutar AUTH_LOGIN_JWT_HARDEN_PLAN

Pegá el bloque siguiente en un **chat nuevo** con el worktree `feat/auth-login-jwt-harden` ya abierto (cwd = ese checkout). No uses Cloud Agents ni `/best-of-n`.

---

```
Ejecutá el plan de endurecimiento de auth. Leé entero y no salgas de alcance:

docs/AUTH_LOGIN_JWT_HARDEN_PLAN.md

Contexto:
- Worktree local: este checkout. Rama feat/auth-login-jwt-harden.
- Stack: PHP 7.4 + CodeIgniter 3.1. Sin sintaxis PHP 8+.
- NO edites master ni el checkout principal. NO docker compose. NO composer install en el host. NO Cloud Agents.
- Para probar UI: .cursor/rules/worktree-preview.mdc (docker run, puerto 8082–8099, nunca 8081, misma start_cms_db, SESS_COOKIE_NAME distinto).
- Credenciales admin: gerber / admin123.
- Typos históricos se quedan: permisions, patern, categorie.
- No edites vendor/, public/vendors/, graphify-out/, public/css/admin/*.min.css.

Destino: híbrido sesión cookie (admin Vue) + JWT flaco RFC (sub/iat/exp/jti, HS256) para clientes externos. No Sanctum. No Bearer obligatorio en Vue.

Implementá en el orden de la sección 6 del plan. Criterios de hecho = sección 1. Checklist de verificación = sección 8. Completá la checklist (browser o curl) antes de declarar listo.

No commitees hasta que yo lo pida.
```

---

Si el agente no está en este worktree, primero:

```
git worktree list
# cwd debe ser:
# /home/gervis/.cursor/worktrees/startCodeIgniter-CSM/auth-login-jwt-harden
```

Abrí esa carpeta como workspace (o `/worktree` sobre `feat/auth-login-jwt-harden`) y después pegá el prompt.
