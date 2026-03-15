#!/bin/bash

# =================================================================
# SCRIPT DE BACKUP AUTOMÁTICO - CAMPUS UPG
# =================================================================
# Autor: Sistema Automático
# Fecha: $(date +%Y-%m-%d)
# Descripción: Backup periódico de bases de datos Campus
# =================================================================

# Configuración
DB_NAME="${DB_DATABASE:-campus_dev}"
DB_USER="${DB_USER:-artacho}"
DB_PASS="${DB_PASSWORD:-M4rt1n.Ha}"
BACKUP_DIR="/var/www/backups"
LOG_FILE="/var/www/backups/backup.log"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="${BACKUP_DIR}/campus_dev_${DATE}.sql"
COMPRESSED_FILE="${BACKUP_FILE}.gz"

# Función de logging
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_FILE"
}

# Inicio del proceso
log_message "=== INICIO BACKUP AUTOMÁTICO ==="
log_message "Base de datos: $DB_NAME"
log_message "Directorio: $BACKUP_DIR"

# Verificar directorio de backups
if [ ! -d "$BACKUP_DIR" ]; then
    mkdir -p "$BACKUP_DIR"
    chmod 750 "$BACKUP_DIR"
    log_message "Directorio de backups creado: $BACKUP_DIR"
fi

# Realizar backup
log_message "Iniciando backup de $DB_NAME..."
mysqldump --no-tablespaces --skip-add-locks --skip-lock-tables \
          -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null

if [ $? -eq 0 ]; then
    log_message "Backup completado: $BACKUP_FILE"
    
    # Comprimir backup
    gzip "$BACKUP_FILE"
    if [ $? -eq 0 ]; then
        log_message "Backup comprimido: $COMPRESSED_FILE"
        
        # Obtener tamaño
        SIZE=$(du -h "$COMPRESSED_FILE" | cut -f1)
        log_message "Tamaño del backup: $SIZE"
        
        # Limpiar backups antiguos (mantener últimos 7 días)
        find "$BACKUP_DIR" -name "campus_dev_*.sql.gz" -mtime +7 -delete 2>/dev/null
        DELETED_COUNT=$(find "$BACKUP_DIR" -name "campus_dev_*.sql.gz" | wc -l)
        log_message "Backups mantenidos: $DELETED_COUNT archivos"
        
        log_message "=== BACKUP AUTOMÁTICO COMPLETADO ==="
        exit 0
    else
        log_message "ERROR: Fallo al comprimir backup"
        exit 1
    fi
else
    log_message "ERROR: Fallo en backup de $DB_NAME"
    exit 1
fi
