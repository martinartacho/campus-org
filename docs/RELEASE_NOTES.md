# Release Notes - Campus UPG

## 📋 Control de Versiones y Cambios

---

## 🚀 Release v1.1.0 - Sistema de IBAN y Seguridad Mejorados
**Fecha:** 2026-03-16  
**Branch:** main  
**Estado:** ✅ Desplegado y estable

### 🎯 Objetivos Principales
- Corregir error 500 en manejo de IBAN encriptado
- Estandarizar validación y formato de IBAN en toda la aplicación
- Implementar seguridad de emails para entorno de desarrollo
- Resolver problemas de permisos y foreign keys

---

## 📦 Componentes Actualizados

### 🔐 **Manejo de IBAN**
- **Modelo `CampusTeacher`:** Simplificado sin cast `encrypted`
- **Accesores:** `formatted_iban`, `masked_iban` para visualización segura
- **Validación:** Regex unificada para formato español
- **Formularios:** Actualizados en todas las vistas

### 📧 **Controllers Actualizados**
- **`TeacherController`:** Validación consistente de IBAN
- **`TeacherAccessController`:** Corrección de validación y formato
- **`SendTeacherAccessController`:** Fix de foreign key constraint

### 🎨 **Vistas Actualizadas**
- **`campus/teachers/edit.blade.php`:** IBAN enmascarado + edición segura
- **`campus/teachers/show.blade.php`:** Visualización enmascarada
- **`treasury/consents/teacher-payment.blade.php`:** IBAN seguro
- **`teacher-access/form-payments-acordeo.blade.php`:** Formatos consistentes

### 🛡️ **Seguridad de Emails**
- **Configuración por entorno:** `.env` condicional (local vs producción)
- **Desarrollo:** `MAIL_MAILER=log` + `MAIL_ALWAYS_TO=preview@mailpit`
- **Producción:** Configuración SMTP real sin redirección
- **AppServiceProvider:** Simplificado sin lógica compleja

---

## ✅ Problemas Resueltos

### 🚨 **Error 500 - "The payload is invalid"**
- **Causa:** Conflicto entre cast `encrypted` y mutadores personalizados
- **Solución:** Eliminar cast y manejar encriptación manualmente
- **Resultado:** Formulario estable sin reinicios de servidor

### 🔒 **Visualización de IBAN Encriptado**
- **Causa:** Valor encriptado mostrado directamente en UI
- **Solución:** Implementar accesores con desencriptación y formateo
- **Resultado:** IBAN enmascarado: `ES00 ********************* 0000`

### 🌐 **Formato Inconsistente**
- **Causa:** Diferentes patrones de validación en toda la app
- **Solución:** Regex unificada: `/^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{4}\s?\d{4}$/`
- **Resultado:** Formato consistente: `ES00 0000 0000 0000 0000 0000`

### 🔐 **Permisos 403**
- **Causa:** Usuario `tresoreria@upg.cat` sin permiso `campus.consents.request`
- **Solución:** Añadir permiso faltante al usuario
- **Resultado:** Acceso completo a funciones de tesorería

### 🗄️ **Foreign Key Constraint**
- **Causa:** Confusión entre `user_id` y `teacher_id` en `consent_histories`
- **Solución:** Usar `$user->id` en lugar de `$teacher->id` para FK
- **Resultado:** Guardado correcto de consentimientos finales

### 📧 **Logs Duplicados**
- **Causa:** `AppServiceProvider@boot()` ejecutando en cada request
- **Solución:** Configuración condicional en `.env` por entorno
- **Resultado:** Configuración limpia y mantenible

---

## 📊 Estadísticas de Cambios
- **Commits:** 6 commits principales
- **Archivos modificados:** 12 archivos
- **Vistas actualizadas:** 5 archivos
- **Controllers actualizados:** 3 archivos
- **Problemas resueltos:** 5 problemas críticos
- **Tiempo de desarrollo:** ~2 horas

---

## 🚀 Release v1.0.0 - Sistema de Backups Automatizados
**Fecha:** 2026-03-15  
**Branch:** dev  
**Estado:** ✅ Listo para pruebas

### 🎯 Objetivo Principal
Implementar sistema de backups automatizados con dashboard informativo y enfoque de seguridad.

---

## 📦 Componentes Implementados

### 🗄️ **Sistema de Backups**
- **Script bash:** `/scripts/backup_database.sh` con variables de entorno
- **Comando Laravel:** `app/Console/Commands/BackupDatabase.php`
- **Base de datos:** Tabla `backup_records` para logging
- **Notificaciones:** Automáticas a administradores

### 🎨 **Dashboard Administrativo**
- **Ruta:** `/admin/backups`
- **Vista:** `resources/views/admin/backups/index.blade.php`
- **Estadísticas:** Últimas 24h, 7 días, tasa de éxito
- **Uso de disco:** Información de almacenamiento

### 🌐 **Traducciones**
- **Català:** `lang/ca/site.php` - 15 nuevas claves
- **Interfaz:** 100% en català
- **Mensajes:** Localizados correctamente

---

## ✅ Mejoras Implementadas

### 🔒 **Seguridad**
- **Backups fuera de webroot:** `/var/www/backups/`
- **Ejecución solo terminal:** Sin botones de acción web
- **Permisos restringidos:** 750, owner: artacho
- **Variables de entorno:** Sin credenciales hardcodeadas

### 📊 **Dashboard**
- **Estadísticas en tiempo real:** Últimos backups y éxito
- **Uso de disco:** Total, usado, libre, backups
- **Registros detallados:** Tabla con historial completo
- **Traducciones català:** Interface localizada

### 🛠️ **Técnico**
- **Comando Laravel:** `php artisan backup:database --environment=dev|prod`
- **Script bash:** Automatizado con compresión gzip
- **Limpieza automática:** Mantenimiento de archivos antiguos
- **Notificaciones:** Sistema integrado con usuarios admin

---

## 🐛 Errores Corregidos

### 🔧 **Errores Técnicos**
- **Error 500 web:** Corregido permisos de usuario web
- **Variables de entorno:** Script bash actualizado
- **Credenciales hardcodeadas:** Migradas a config Laravel
- **Route naming:** Corregidos nombres de rutas admin

### 🎨 **Errores UI/UX**
- **Layout $slot undefined:** Adaptado a `campus.shared.layout`
- **Traducciones faltantes:** Añadidas claves en català
- **404 en /admin/backups:** Corregidos prefijos de rutas
- **Duplicación de contenido:** Eliminada pestaña redundante

---

## 📋 Cambios en Archivos

### 📝 **Archivos Modificados**
```
app/Console/Commands/BackupDatabase.php      [NUEVO]
scripts/backup_database.sh                   [NUEVO]
resources/views/admin/backups/index.blade.php [NUEVO]
resources/views/components/dashboard-admin-cards.blade.php
routes/web.php
lang/ca/site.php
```

### 📊 **Estadísticas**
- **Archivos nuevos:** 3
- **Archivos modificados:** 3
- **Líneas añadidas:** ~120
- **Líneas eliminadas:** ~154

---

## 🔄 Flujo de Trabajo

### 🛠️ **Implementación**
1. **Diseño:** Sistema de backups seguro
2. **Desarrollo:** Scripts y comandos Laravel
3. **UI:** Dashboard informativo
4. **Testing:** Validación CLI y web
5. **Security:** Enfoque terminal-only

### 🚀 **Deploy**
1. **Branch:** main → dev
2. **Merge:** Completado exitosamente
3. **Push:** Subido a origin/dev
4. **Status:** ✅ Listo para pruebas

---

## 🎯 Próximos Pasos

### 📋 **Pendientes**
- [ ] **Cron jobs:** Programar backups automáticos
- [ ] **Testing:** Validar en entorno dev
- [ ] **Documentación:** Actualizar guías de uso
- [ ] **Monitorización:** Configurar alertas

### 🚀 **Futuro**
- **Backup completo:** Opción --full
- **Restauración:** Sistema de restore
- **Cloud storage:** Integración S3/Azure
- **Dashboard avanzado:** Gráficos y métricas

---

## 📞 Contacto y Soporte

### 👥 **Equipo**
- **Desarrollo:** Sistema automatizado
- **Testing:** Validación en dev
- **Documentación:** Guías completas

### 📧 **Soporte**
- **Issues:** GitHub repository
- **Documentación:** `/docs/` directory
- **Logs:** `/var/www/backups/backup.log`

---

## 📊 Resumen Técnico

### 🔧 **Configuración**
- **PHP:** Laravel Artisan commands
- **Bash:** Script de backup con mysqldump
- **MySQL:** Volcado y compresión gzip
- **Filesystem:** /var/www/backups/

### 🌐 **Web Interface**
- **Framework:** Laravel Blade
- **Bootstrap:** UI components
- **Translations:** Català locale
- **Security:** Admin middleware

---

**📅 Última actualización:** 2026-03-15  
**🔄 Versión:** v1.0.0  
**📍 Branch:** dev  
**✅ Estado:** Listo para pruebas en entorno de desarrollo
