# =================================================================
# DOCUMENTACIÓN - SISTEMA DE BACKUPS AUTOMÁTICOS
# =================================================================
# Campus UPG - Sistema de Gestión de Backups
# Fecha: 2026-03-15
# Versión: 1.0
# =================================================================

## 📋 RESUMEN

Sistema automatizado para realizar backups periódicos de las bases de datos del Campus UPG, con notificaciones automáticas a administradores y registro completo de operaciones.

## 🎯 OBJETIVOS

- **Automatizar** backups periódicos de bases de datos
- **Notificar** a administradores sobre estado de backups
- **Registrar** todas las operaciones en logs
- **Gestionar** retención de backups antiguos
- **Monitorear** estado del sistema de backups

## 🏗️ ARQUITECTURA

### Componentes del Sistema:

1. **Script Bash** (`scripts/backup_database.sh`)
   - Ejecución de backup con mysqldump
   - Compresión de archivos
   - Limpieza automática
   - Logging detallado

2. **Comando Laravel** (`app/Console/Commands/BackupDatabase.php`)
   - Integración con Artisan
   - Notificaciones a admin
   - Registro en base de datos
   - Manejo de errores

3. **Cron Job** (Programación)
   - Ejecución periódica automática
   - Múltiples horarios configurables

4. **Sistema de Notificaciones**
   - Alertas en panel de admin
   - Registro de operaciones
   - Notificaciones de error

## 📁 ESTRUCTURA DE ARCHIVOS

```
/var/www/dev.upg.cat/
├── scripts/
│   └── backup_database.sh          # Script principal de backup
├── app/Console/Commands/
│   └── BackupDatabase.php          # Comando Laravel
├── storage/logs/
│   └── backup.log                 # Log de operaciones
└── /var/www/backups/             # Directorio seguro de backups
    ├── campus_dev_YYYYMMDD_HHMMSS.sql.gz
    ├── campus_prod_YYYYMMDD_HHMMSS.sql.gz
    └── backup.log
```

## ⚙️ CONFIGURACIÓN

### Variables de Entorno:

```bash
# Base de datos
DB_DATABASE=campus_dev            # o campus_upg para producción
DB_USERNAME=artacho
DB_PASSWORD=contraseña

# Directorios
BACKUP_DIR=/var/www/backups
LOG_FILE=/var/www/backups/backup.log
```

### Permisos Recomendados:

```bash
# Directorio de backups
chmod 750 /var/www/backups
chown artacho:artacho /var/www/backups

# Scripts
chmod +x /var/www/dev.upg.cat/scripts/backup_database.sh
```

## 🔄 CRONOGRAMA DE EJECUCIÓN

### Backups Diarios:
```bash
# Todos los días a las 02:00 AM
0 2 * * * /usr/bin/php /var/www/dev.upg.cat/artisan backup:database --env=dev

# Todos los días a las 03:00 AM (producción)
0 3 * * * /usr/bin/php /var/www/campus.upg.cat/artisan backup:database --env=prod
```

### Backups Semanales:
```bash
# Domingos a las 01:00 AM (backup completo)
0 1 * * 0 /usr/bin/php /var/www/dev.upg.cat/artisan backup:database --env=dev --full
```

## 📊 MONITOREO Y REGISTROS

### Tabla de Registros:
```sql
CREATE TABLE backup_records (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    environment VARCHAR(10) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_size VARCHAR(20),
    status ENUM('success', 'error') DEFAULT 'success',
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Log de Operaciones:
```
[2026-03-15 02:00:00] === INICIO BACKUP AUTOMÁTICO ===
[2026-03-15 02:00:01] Base de datos: campus_dev
[2026-03-15 02:00:01] Directorio: /var/www/backups
[2026-03-15 02:00:05] Backup completado: /var/www/backups/campus_dev_20260315_020005.sql
[2026-03-15 02:00:06] Backup comprimido: /var/www/backups/campus_dev_20260315_020005.sql.gz
[2026-03-15 02:00:06] Tamaño del backup: 145KB
[2026-03-15 02:00:06] Backups mantenidos: 7 archivos
[2026-03-15 02:00:06] === BACKUP AUTOMÁTICO COMPLETADO ===
```

## 🔔 NOTIFICACIONES

### Tipos de Notificaciones:

1. **✅ Backup Exitoso**
   - Título: "🔄 Backup Automático Completado"
   - Mensaje: "Backup de base de datos completado exitosamente en entorno {env}."
   - Tipo: info

2. **❌ Error en Backup**
   - Título: "❌ Error en Backup Automático"
   - Mensaje: "Error en backup de base de datos en entorno {env}: {error}"
   - Tipo: error

### Destinatarios:
- Super Admin
- Admin
- Coordinación (opcional)

## 🛠️ COMANDOS ÚTILES

### Ejecución Manual:
```bash
# Backup de desarrollo
php artisan backup:database --env=dev

# Backup de producción
php artisan backup:database --env=prod
```

### Verificación:
```bash
# Ver últimos backups
ls -la /var/www/backups/campus_*.sql.gz | tail -10

# Ver log de operaciones
tail -20 /var/www/backups/backup.log

# Ver registros en base de datos
php artisan tinker --execute="DB::table('backup_records')->latest()->limit(5)->get();"
```

### Mantenimiento:
```bash
 # Limpiar backups antiguos (más de 30 días)
find /var/www/backups -name "*.sql.gz" -mtime +30 -delete

# Ver espacio utilizado
du -sh /var/www/backups
```

## 🚨 PROCEDIMIENTOS DE EMERGENCIA

### Si falla un backup:
1. **Verificar log**: `/var/www/backups/backup.log`
2. **Verificar espacio**: `df -h /var/www/backups`
3. **Verificar permisos**: `ls -la /var/www/backups`
4. **Ejecución manual**: `php artisan backup:database --env=dev`
5. **Notificar al administrador**: Sistema automático

### Restauración:
```bash
# Descomprimir backup
gunzip /var/www/backups/campus_dev_YYYYMMDD_HHMMSS.sql.gz

# Restaurar base de datos
mysql -u artacho -p campus_dev < backup_file.sql
```

## 📈 MÉTRICAS Y REPORTES

### Indicadores Clave:
- **Frecuencia de backups**: Diaria
- **Tasa de éxito**: >95%
- **Tiempo de ejecución**: <5 minutos
- **Espacio utilizado**: Variable según datos
- **Retención**: 7 días (configurable)

### Reporte Semanal:
```sql
SELECT 
    environment,
    DATE(created_at) as fecha,
    COUNT(*) as total_backups,
    SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as exitosos,
    SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) fallidos
FROM backup_records 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY environment, DATE(created_at)
ORDER BY fecha DESC;
```

## 🔐 SEGURIDAD

### Medidas Implementadas:
- **Backups fuera de webroot**: `/var/www/backups/`
- **Permisos restringidos**: 750
- **Compresión de archivos**: Reducción de tamaño
- **Encriptación de contraseñas**: Variables de entorno
- **Registro de auditoría**: Logs completos

### Recomendaciones:
- **Rotación de credenciales**: Cada 3 meses
- **Backup externo**: Considerar nube o servidor remoto
- **Monitoreo de acceso**: Revisar logs periódicamente
- **Pruebas de restauración**: Mensuales

## 📞 SOPORTE

### Contacto en Caso de Problemas:
- **Administrador del Sistema**: artacho@upg.cat
- **Documentación**: Este archivo
- **Logs**: `/var/www/backups/backup.log`

### Checklist de Troubleshooting:
- [ ] Verificar conexión a base de datos
- [ ] Verificar espacio en disco
- [ ] Verificar permisos de directorios
- [ ] Revisar logs de errores
- [ ] Probar ejecución manual
- [ ] Verificar configuración de cron

---

## 📝 HISTORIAL DE CAMBIOS

| Fecha | Versión | Cambio | Autor |
|-------|---------|---------|-------|
| 2026-03-15 | 1.0 | Versión inicial del sistema | Sistema Automático |

---

**Este documento debe mantenerse actualizado con cualquier cambio en el sistema de backups.**
