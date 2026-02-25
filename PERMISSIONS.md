# 📋 Guía de Permisos para Laravel

Esta guía documenta el sistema de gestión de permisos implementado para el proyecto Laravel, asegurando que la aplicación funcione correctamente en cualquier entorno.

## 🎯 Objetivo

Garantizar que los directorios y archivos críticos de Laravel tengan los permisos adecuados para:
- Escritura de logs
- Cache de aplicación
- Sesiones de usuario
- Vistas compiladas
- Archivos de configuración

## 📁 Estructura de Permisos Requerida

### Directorios Principales
```
storage/          - 775 (recursivo)
├── app/
├── framework/
│   ├── cache/
│   ├── sessions/
│   └── views/
└── logs/

bootstrap/cache/  - 775 (recursivo)
public/           - 755 (recursivo)
```

### Archivos Críticos
```
.env              - 644
artisan           - 755
```

## 🛠️ Herramientas Disponibles

### 1. Script de Despliegue (`deploy.sh`)

Script completo que ejecuta todo el proceso de despliegue con configuración automática de permisos.

**Uso:**
```bash
# Ejecutar despliegue completo
./deploy.sh

# O usando npm
npm run deploy
```

**Funciones:**
- ✅ Actualización de dependencias Composer
- ✅ Configuración automática de permisos
- ✅ Limpieza de caché
- ✅ Optimización de Laravel
- ✅ Ejecución de migraciones
- ✅ Verificación final de permisos

### 2. Comando Artisan (`setup:permissions`)

Comando personalizado para verificar y corregir permisos.

**Verificar permisos:**
```bash
php artisan setup:permissions
```

**Corregir permisos automáticamente:**
```bash
php artisan setup:permissions --fix
```

**Características:**
- 🔍 Verificación detallada de permisos
- 🔧 Corrección automática con opción `--fix`
- 📊 Reporte de estado con colores
- 🎯 Verificación de directorios críticos
- 📝 Logs detallados del proceso

### 3. Scripts NPM

Scripts rápidos para tareas comunes:

```bash
# Despliegue completo
npm run deploy

# Corregir permisos rápidamente
npm run setup-perms
```

### 4. Composer Hooks

Se ejecutan automáticamente durante operaciones de Composer:

```bash
# Después de actualizar dependencias
composer update  # Los permisos se corrigen automáticamente

# Después de instalar
composer install
```

## 🚀 Uso Rápido

### Para Desarrollo Local

```bash
# Verificar permisos actuales
php artisan setup:permissions

# Corregir si hay problemas
php artisan setup:permissions --fix

# O usar el script npm
npm run setup-perms
```

### Para Producción

```bash
# Despliegue completo con todo configurado
./deploy.sh

# O usando npm
npm run deploy
```

### Después de Actualizar Dependencias

```bash
# Composer se encarga automáticamente
composer update

# O verificar manualmente
php artisan setup:permissions --fix
```

## 🔧 Solución de Problemas Comunes

### Problema: "Permission denied" en storage/

**Causa:** Permisos incorrectos en directorios de storage

**Solución:**
```bash
php artisan setup:permissions --fix
```

### Problema: "Unable to write log file"

**Causa:** Directorio `storage/logs` no es escribible

**Solución:**
```bash
chmod -R 775 storage/logs/
php artisan setup:permissions --fix
```

### Problema: Cache no funciona

**Causa:** Permisos incorrectos en `bootstrap/cache` o `storage/framework/cache`

**Solución:**
```bash
php artisan setup:permissions --fix
php artisan cache:clear
```

### Problema: Sesiones no guardan

**Causa:** Directorio `storage/framework/sessions` no es escribible

**Solución:**
```bash
php artisan setup:permissions --fix
```

## 📊 Verificación Manual

Para verificar permisos manualmente:

```bash
# Verificar permisos de directorios principales
ls -la storage/
ls -la bootstrap/cache/
ls -la public/

# Verificar archivos críticos
ls -la .env
ls -la artisan

# Verificar directorios de framework
ls -la storage/framework/
```

## 🌍 Entornos Específicos

### Windows (Laragon/WAMP)

Los scripts están diseñados para funcionar en entornos Unix. Para Windows:

```bash
# Usar Git Bash o WSL
./deploy.sh

# O ejecutar comandos directamente
php artisan setup:permissions --fix
```

### Docker

Asegúrate que el contenedor tenga los permisos adecuados:

```bash
# Dentro del contenedor
php artisan setup:permissions --fix
```

### Servidores Compartidos

Si no puedes ejecutar `chmod`, contacta a tu proveedor de hosting para configurar:

- `storage/` - 775 recursivo
- `bootstrap/cache/` - 775 recursivo
- `public/` - 755 recursivo

## 🔐 Consideraciones de Seguridad

- **Nunca** uses 777 en producción
- `.env` debe permanecer en 644
- Directorios `storage/` y `bootstrap/cache/` deben ser 775
- Verifica que el servidor web no pueda escribir fuera de los directorios permitidos

## 📝 Checklist de Despliegue

- [ ] Ejecutar `php artisan setup:permissions` para verificar
- [ ] Corregir con `--fix` si es necesario
- [ ] Probar escritura de logs
- [ ] Verificar que el cache funciona
- [ ] Confirmar que las sesiones guardan
- [ ] Ejecutar `./deploy.sh` para despliegue completo

## 🆘 Soporte

Si encuentras problemas:

1. Ejecuta `php artisan setup:permissions` para diagnóstico
2. Usa `--fix` para corrección automática
3. Revisa los logs de Laravel en `storage/logs/`
4. Verifica los permisos del servidor web

---

**Nota:** Este sistema está diseñado para funcionar automáticamente en la mayoría de los casos. Si necesitas ajustes específicos para tu entorno, modifica los archivos según tus necesidades.
