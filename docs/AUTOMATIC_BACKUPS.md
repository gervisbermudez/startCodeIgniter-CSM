# Sistema Híbrido de Backups Automáticos

## 🎯 Problema Resuelto

**Pregunta:** ¿Qué pasa si no puedo configurar un cron job en mi servidor?

**Respuesta:** ¡No hay problema! El sistema funciona de **DOS FORMAS** diferentes.

---

## 🔄 Dos Modos de Operación

### **Modo 1: Cron Job Real** (Recomendado)
✅ **Cuándo usar:** Tienes acceso a crontab en tu servidor  
✅ **Ventajas:**
- Ejecución precisa y programada
- No depende de visitas al sitio
- Más eficiente
- Mejor para sitios con poco tráfico

**Configuración:**
```bash
crontab -e
# Agregar:
0 3 * * * cd /ruta/proyecto && ./auto_backup.sh >> /var/log/backup.log 2>&1
```

---

### **Modo 2: Pseudo-Cron** (Automático - Fallback)
✅ **Cuándo usar:** NO tienes acceso a crontab (hosting compartido, etc.)  
✅ **Ventajas:**
- Funciona automáticamente sin configuración
- No requiere acceso al servidor
- Se activa solo con visitas al admin
- Cero configuración adicional

**Cómo funciona:**
1. Un administrador accede al panel de administración
2. El sistema verifica (10% de probabilidad) si es necesario un backup
3. Si ha pasado el tiempo configurado → Crea el backup automáticamente
4. Todo sucede en segundo plano, sin afectar la experiencia del usuario

---

## 📊 Comparación Detallada

| Característica | Cron Job Real | Pseudo-Cron |
|---------------|---------------|-------------|
| **Requiere configuración** | ✅ Sí (crontab) | ❌ No |
| **Acceso al servidor** | ✅ Necesario | ❌ No necesario |
| **Ejecución precisa** | ✅ Exacta (ej: 3:00 AM) | ⚠️ Aproximada |
| **Funciona sin visitas** | ✅ Sí | ❌ Requiere visitas admin |
| **Impacto en rendimiento** | ✅ Ninguno | ⚠️ Mínimo (10% checks) |
| **Ideal para** | Producción | Desarrollo/Hosting compartido |

---

## 🚀 Cómo Funciona el Pseudo-Cron

### Flujo de Ejecución:

```
Usuario Admin accede al Dashboard
          ↓
Hook se ejecuta (post_controller_constructor)
          ↓
¿Es área de admin? → No → Termina
          ↓ Sí
¿Toca verificar? (10% random) → No → Termina
          ↓ Sí
¿Backups habilitados? → No → Termina
          ↓ Sí
¿Ha pasado el tiempo? → No → Termina
          ↓ Sí
Crea Backup en Segundo Plano
          ↓
Actualiza LAST_AUTO_BACKUP
          ↓
Limpia Backups Antiguos
          ↓
Registra en Logs
```

### Verificación Inteligente:

**Pregunta:** ¿Por qué solo verifica el 10% de las veces?

**Respuesta:** Para no sobrecargar el servidor. Si verificara en cada carga de página:
- ❌ Consultaría la BD en cada request
- ❌ Impacto en rendimiento
- ❌ Innecesario (el backup es diario, no cada minuto)

Con 10% de probabilidad:
- ✅ Reduce overhead en 90%
- ✅ Aún así verifica frecuentemente
- ✅ En un día con 100 visitas admin → ~10 verificaciones
- ✅ Suficiente para detectar si es necesario

---

## ⚙️ Configuración del Sistema

### Paso 1: Habilitar Backups Automáticos

**Admin → Configuración → Sistema**

```
AUTO_BACKUP_ENABLED: Si
AUTO_BACKUP_FREQUENCY: daily (o hourly/weekly/monthly)
AUTO_BACKUP_RETENTION: 7 (número de backups a mantener)
AUTO_BACKUP_TIME: 03:00 (solo informativo para cron real)
```

### Paso 2: Elegir el Modo

#### Opción A: Cron Job (Si tienes acceso)

```bash
# Editar crontab
crontab -e

# Para Docker:
0 3 * * * docker exec ci_php56 php /var/www/html/index.php cron auto_backup >> /var/log/backup.log 2>&1

# Para servidor normal:
0 3 * * * cd /var/www/html && php index.php cron auto_backup >> /var/log/backup.log 2>&1
```

#### Opción B: Pseudo-Cron (Automático)

**¡No hacer nada!** Ya está configurado y funcionando.

Solo asegúrate de:
- ✅ Hooks habilitados (`config['enable_hooks'] = true`)
- ✅ Directorio `backups/database/` con permisos 777
- ✅ Visitar el admin regularmente

---

## 🧪 Probar el Sistema

### Verificar que el Hook está activo:

1. Accede al admin: `http://tudominio.com/admin`
2. Revisa los logs: **Admin → Logs → Filtrar por "config"**
3. Busca: "Backup automático creado (pseudo-cron)"

### Forzar un backup manual:

```bash
# Docker:
docker exec ci_php56 php /var/www/html/index.php cron auto_backup

# Servidor normal:
php index.php cron auto_backup
```

### Ver backups creados:

**Admin → Configuración → Base de Datos**

Verás dos tipos:
- `auto_YYYYMMDDHHMMSS.gz` - Backups automáticos
- `YYYYMMDDHHMMSS.gz` - Backups manuales

---

## 🔍 Detección de Tiempo Transcurrido

**Pregunta:** ¿Cómo sabe si ha pasado suficiente tiempo?

**Respuesta:** El sistema compara `LAST_AUTO_BACKUP` con la hora actual:

```php
// Ejemplo para frecuencia "daily"
$last_backup = "2023-12-20 16:00:00";
$now = "2023-12-21 17:00:00";
$diff = 25 horas;

if ($diff >= 24 horas) {
    // ✅ Crear backup
}
```

**Frecuencias soportadas:**
- `hourly` → Cada 1 hora
- `daily` → Cada 24 horas
- `weekly` → Cada 7 días
- `monthly` → Cada 30 días

---

## 📝 Escenarios de Uso

### Escenario 1: Hosting Compartido (Sin Cron)

```
✅ Habilitar: AUTO_BACKUP_ENABLED = Si
✅ Frecuencia: daily
✅ Retención: 7
❌ NO configurar cron job
✅ El sistema usará pseudo-cron automáticamente
```

**Resultado:**
- Cada vez que accedas al admin, hay 10% de probabilidad de verificar
- Si han pasado 24h desde el último backup → Se crea automáticamente
- Mantiene los últimos 7 backups

---

### Escenario 2: VPS/Servidor Dedicado (Con Cron)

```
✅ Habilitar: AUTO_BACKUP_ENABLED = Si
✅ Frecuencia: daily
✅ Retención: 30
✅ Configurar cron job para las 3:00 AM
✅ El pseudo-cron también está activo (doble seguridad)
```

**Resultado:**
- A las 3:00 AM → Cron job crea backup
- Si el cron falla → Pseudo-cron lo detecta y crea backup
- Sistema redundante y confiable

---

### Escenario 3: Sitio con Poco Tráfico

```
✅ Habilitar: AUTO_BACKUP_ENABLED = Si
✅ Frecuencia: weekly
✅ Configurar cron job semanal
```

**Por qué:** Con pocas visitas, el pseudo-cron podría no ejecutarse. El cron job garantiza backups regulares.

---

## 🛡️ Seguridad y Rendimiento

### Impacto en Rendimiento:

**Pseudo-Cron:**
- Verifica solo en 10% de requests admin
- Ejecución en segundo plano (no bloquea)
- Overhead: < 50ms en verificaciones
- Backup real: Se ejecuta async cuando es posible

**Cron Job:**
- Cero impacto (se ejecuta fuera de requests)

### Seguridad:

- ✅ Solo se ejecuta para usuarios admin
- ✅ Backups en directorio protegido
- ✅ Archivos `.gz` excluidos de Git
- ✅ Logs de todas las operaciones

---

## 🔧 Troubleshooting

### "No se crean backups automáticos"

**Verificar:**
1. ¿Está habilitado? → `AUTO_BACKUP_ENABLED = Si`
2. ¿Hooks activos? → `config['enable_hooks'] = true`
3. ¿Permisos? → `chmod 777 backups/database/`
4. ¿Visitas admin? → Necesitas acceder al panel
5. ¿Tiempo suficiente? → Espera según frecuencia configurada

### "Backups se crean pero no se limpian"

**Verificar:**
- `AUTO_BACKUP_RETENTION` está configurado
- Solo limpia backups con prefijo `auto_`
- Backups manuales NO se eliminan

### "Quiero backups más frecuentes"

**Opciones:**
1. Cambiar frecuencia a `hourly`
2. Configurar cron job cada hora
3. Crear backups manuales cuando necesites

---

## 📚 Resumen

| Pregunta | Respuesta |
|----------|-----------|
| ¿Necesito cron job? | No, pero es recomendado |
| ¿Funciona sin cron? | ✅ Sí, con pseudo-cron |
| ¿Cómo detecta el tiempo? | Compara `LAST_AUTO_BACKUP` con ahora |
| ¿Afecta rendimiento? | Mínimo (10% checks, async) |
| ¿Qué pasa sin visitas? | Cron job sigue funcionando |
| ¿Puedo usar ambos? | ✅ Sí, recomendado |

---

## 🎯 Recomendación Final

**Mejor configuración:**
1. ✅ Habilitar backups automáticos
2. ✅ Configurar cron job (si es posible)
3. ✅ Dejar pseudo-cron activo (fallback)
4. ✅ Frecuencia: `daily`
5. ✅ Retención: `7-30` según espacio disponible

**Resultado:** Sistema robusto y redundante que funciona en cualquier entorno.
