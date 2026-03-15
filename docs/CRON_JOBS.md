# =================================================================
# CRON JOBS - SISTEMA DE BACKUPS AUTOMÁTICOS
# =================================================================
# Campus UPG - Configuración de Tareas Programadas
# Fecha: 2026-03-15
# =================================================================

# =================================================================
# BACKUPS DIARIOS - ENTORNO DE DESARROLLO
# =================================================================

# Backup diario de desarrollo - 02:00 AM
0 2 * * * /usr/bin/php /var/www/dev.upg.cat/artisan backup:database --env=dev >> /var/www/backups/cron.log 2>&1

# Backup diario de desarrollo - 14:00 PM (backup del mediodía)
0 14 * * * /usr/bin/php /var/www/dev.upg.cat/artisan backup:database --env=dev >> /var/www/backups/cron.log 2>&1

# =================================================================
# BACKUPS DIARIOS - ENTORNO DE PRODUCCIÓN
# =================================================================

# Backup diario de producción - 03:00 AM
0 3 * * * /usr/bin/php /var/www/campus.upg.cat/artisan backup:database --env=prod >> /var/www/backups/cron.log 2>&1

# Backup diario de producción - 15:00 PM (backup del mediodía)
0 15 * * * /usr/bin/php /var/www/campus.upg.cat/artisan backup:database --env=prod >> /var/www/backups/cron.log 2>&1

# =================================================================
# BACKUPS SEMANALES - COMPLETOS
# =================================================================

# Backup semanal completo de desarrollo - Domingo 01:00 AM
0 1 * * 0 /usr/bin/php /var/www/dev.upg.cat/artisan backup:database --env=dev >> /var/www/backups/cron.log 2>&1

# Backup semanal completo de producción - Domingo 02:00 AM
0 2 * * 0 /usr/bin/php /var/www/campus.upg.cat/artisan backup:database --env=prod >> /var/www/backups/cron.log 2>&1

# =================================================================
# MANTENIMIENTO Y LIMPIEZA
# =================================================================

# Limpiar logs antiguos de cron - Diario 04:00 AM
0 4 * * * find /var/www/backups -name "cron.log" -mtime +30 -exec rm {} \; >> /var/www/backups/cleanup.log 2>&1

# Limpiar backups antiguos (más de 30 días) - Lunes 01:00 AM
0 1 * * 1 find /var/www/backups -name "*.sql.gz" -mtime +30 -delete >> /var/www/backups/cleanup.log 2>&1

# Verificar espacio en disco - Cada 6 horas
0 */6 * * * df -h /var/www/backups >> /var/www/backups/disk_usage.log 2>&1

# =================================================================
# MONITOREO Y ALERTAS
# =================================================================

# Verificar si hay backups recientes - Cada hora
0 * * * * /usr/bin/find /var/www/backups -name "campus_*.sql.gz" -mtime -1 | wc -l > /tmp/backup_check.txt && if [ \$(cat /tmp/backup_check.txt) -lt 2 ]; then echo "ALERTA: No hay backups recientes" | mail -s "Backup Alert" artacho@upg.cat; fi >> /var/www/backups/monitor.log 2>&1

# =================================================================
# INSTRUCCIONES DE INSTALACIÓN
# =================================================================

# 1. Editar el crontab del usuario:
# crontab -e

# 2. Agregar este contenido al crontab

# 3. Verificar que los scripts tengan permisos de ejecución:
# chmod +x /var/www/dev.upg.cat/scripts/backup_database.sh
# chmod +x /var/www/campus.upg.cat/scripts/backup_database.sh

# 4. Verificar que PHP esté en la ruta correcta:
# which php
# which artisan

# 5. Probar ejecución manual:
# php artisan backup:database --env=dev

# 6. Verificar logs:
# tail -f /var/www/backups/cron.log

# =================================================================
# NOTAS IMPORTANTES
# =================================================================

# - Los horarios están configurados para evitar picos de uso
# - Los backups de producción se ejecutan 1 hora después que los de desarrollo
# - Los logs se guardan en /var/www/backups/
# - La limpieza automática mantiene solo 30 días de backups
# - El monitoreo envía alertas si no hay backups recientes

# =================================================================
# FORMATO DE CRONTAB
# =================================================================

# Minuto Hora DíaMes Mes DíaSemana Comando
# *     *    *       *     *       comando
# 0-59  0-23 1-31    1-12   0-6    comando a ejecutar

# Ejemplos:
# 0 2 * * *     -> Todos los días a las 2:00 AM
# 0 */6 * * *    -> Cada 6 horas
# 0 1 * * 0     -> Todos los domingos a la 1:00 AM
# 0 1 * * 1     -> Todos los lunes a la 1:00 AM
