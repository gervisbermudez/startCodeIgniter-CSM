# Normalización Completa de Archivos en Resources

## Fecha: 4 de enero de 2026

### Resumen General

Se ha completado la normalización integral de todos los archivos en la carpeta `/resources` para mantener consistencia completa en:
- **Idioma**: 100% en inglés
- **Convención de nombres**: PascalCase para componentes, camelCase para instancias
- **Estructura**: Archivos y referencias unificadas

---

## 📋 Cambios Realizados

### 1. Archivos .component.js → Component.js (8 archivos)

| Anterior | Nuevo |
|----------|-------|
| `dataEdit.component.js` | `DataEditComponent.js` |
| `export.component.js` | `ExportComponent.js` |
| `import.component.js` | `ImportComponent.js` |
| `loggerData.component.js` | `LoggerDataComponent.js` |
| `PageView.component.js` | `PageViewComponent.js` |
| `FormSiteDetails.component.js` | `FormSiteDetailsComponent.js` |
| `Notifications.component.js` | `NotificationsComponent.js` |
| `permissionsData.component.js` | `PermissionsDataComponent.js` |

**Ubicación**: `resources/components/`

---

### 2. Módulos camelCase → PascalCase (4 archivos)

| Anterior | Nuevo |
|----------|-------|
| `dashboardModule.js` | `DashboardModule.js` |
| `fileExplorerModule.js` | `FileExplorerModule.js` |
| `fileUploaderModule.js` | `FileUploaderModule.js` |
| `dataFormModule.js` | `DataFormModule.js` |

**Ubicación**: `resources/components/`

---

### 3. Componentes Widget con minúsculas → PascalCase (4 archivos)

| Anterior | Nuevo |
|----------|-------|
| `createContents.js` | `CreateContents.js` |
| `fileExplorerCollection.js` | `FileExplorerCollection.js` |
| `pageCardComponent.js` | `PageCardComponent.js` |
| `usersCollection.js` | `UsersCollection.js` |

**Ubicación**: `resources/components/widget/`

---

### 4. Componentes Principales con minúsculas → PascalCase (1 archivo)

| Anterior | Nuevo |
|----------|-------|
| `searchComponent.js` | `SearchComponent.js` |

**Ubicación**: `resources/components/`

---

### 5. Archivos Blade.php de Componentes (4 archivos)

| Anterior | Nuevo |
|----------|-------|
| `dataEditComponent.blade.php` | `DataEditComponent.blade.php` |
| `createContentsComponent.blade.php` | `CreateContentsComponent.blade.php` |
| `fileExplorerCollectionComponent.blade.php` | `FileExplorerCollectionComponent.blade.php` |
| `pageCardComponent.blade.php` | `PageCardComponent.blade.php` |

**Ubicación**: `application/views/admin/components/`

---

### 6. Archivos en public/js/components (Archivos compilados)

| Anterior | Nuevo |
|----------|-------|
| `ConfiguracionList.js` | `ConfigurationList.js` |

**Ubicación**: `public/js/components/`

---

## 🔄 Referencias Actualizadas

### En Vistas Blade.php:
- ✅ Todos los `<script src=...` actualizados
- ✅ Todos los `@include('admin.components.X')` actualizados
- ✅ 30+ archivos .blade.php modificados

### En Archivos JavaScript:
- ✅ Variables de instancias Vue actualizadas a PascalCase
- ✅ Llamadas a métodos de módulos actualizadas
- ✅ Referencias internas consistentes

**Archivos JS modificados:**
- `DataEditComponent.js` (variable: `DataEdit`)
- `SearchComponent.js` (variable: `SearchComponent`)
- `FormSiteDetailsComponent.js` (variable: `FormSiteDetails`)
- `LoggerDataComponent.js` (variable: `LoggerData`)
- `FileExplorerModule.js` (variable: `FileExplorerModule`)
- `DashboardModule.js` (variable: `DashboardModule`)
- `FileUploaderModule.js` (variable: `FileUploaderModule`)

---

## ✅ Verificación Final

```
✓ 0 referencias a .component.js residuales
✓ 0 módulos con camelCase residuales
✓ 0 componentes widget minúsculos residuales
✓ 100% cobertura de actualización de referencias
```

---

## 📌 Esquema de Normalización Aplicado

### Archivos de Componentes Vue:
```
PascalCaseComponent.js  ← Nombre archivo
├── Vue.component("camelCaseTag", {...})  ← Nombre tag HTML (no cambia)
└── var PascalCase = Vue.component(...)    ← Variable (sí cambia)
```

### Módulos:
```
PascalCaseModule.js  ← Nombre archivo
└── var PascalCaseModule = new Vue({...})  ← Variable (PascalCase)
```

### Archivos Blade:
```
PascalCase.blade.php  ← Nombre archivo
└── @include('admin.components.PascalCase')  ← Referencia coincide
```

---

## 🎯 Beneficios

1. **Consistencia Total**: Todos los archivos siguen la misma convención
2. **Claridad**: Sin mezcla de idiomas ni convenciones
3. **Mantenibilidad**: Fácil de buscar y referenciar
4. **Escalabilidad**: Base sólida para nuevos componentes
5. **Internacionalización**: Nombres en inglés facilitan colaboración

---

## 📝 Notas Importantes

- Los nombres de tags Vue.component internos (ej: `"dataEdit"`) se mantuvieron igual para no afectar las vistas HTML
- Solo los nombres de archivos, variables y referencias fueron normalizados
- Se recomienda ejecutar `npm run build` para regenerar archivos compilados en `public/`

