#!/bin/bash

# Script de despliegue para Laravel con configuración de permisos
# Uso: ./deploy.sh

echo "🚀 Iniciando despliegue de Laravel..."

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para verificar si un comando se ejecutó correctamente
check_command() {
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ $1${NC}"
    else
        echo -e "${RED}❌ Error en: $1${NC}"
        exit 1
    fi
}

# Función para configurar permisos
setup_permissions() {
    echo "🔧 Configurando permisos..."
    
    # Permisos para directorios storage y bootstrap/cache
    chmod -R 775 storage/
    check_command "Permisos storage/"
    
    chmod -R 775 bootstrap/cache/
    check_command "Permisos bootstrap/cache/"
    
    # Permisos para archivos públicos
    chmod -R 755 public/
    check_command "Permisos public/"
    
    # Asegurar que el archivo .env tenga los permisos correctos
    chmod 644 .env
    check_command "Permisos .env"
    
    # Permisos para el script artisan
    chmod +x artisan
    check_command "Permisos artisan"
}

# 1. Actualizar dependencias
echo "📦 Actualizando dependencias de Composer..."
composer install --no-dev --optimize-autoloader
check_command "Composer install"

# 2. Configurar permisos
setup_permissions

# 3. Limpiar caché
echo "🧹 Limpiando caché..."
php artisan cache:clear
check_command "Cache clear"

php artisan config:clear
check_command "Config clear"

php artisan route:clear
check_command "Route clear"

php artisan view:clear
check_command "View clear"

# 4. Optimizar Laravel
echo "⚡ Optimizando Laravel..."
php artisan config:cache
check_command "Config cache"

php artisan route:cache
check_command "Route cache"

php artisan view:cache
check_command "View cache"

# 5. Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force
check_command "Migraciones"

# 6. Optimizar autoloader
echo "🔍 Optimizando autoloader..."
composer dump-autoload --optimize
check_command "Optimize autoloader"

# 7. Verificar permisos finales
echo "🔍 Verificando permisos finales..."
php artisan setup:permissions --fix
check_command "Verificación final de permisos"

echo -e "${GREEN}🎉 Despliegue completado exitosamente!${NC}"
echo ""
echo "📋 Resumen de acciones ejecutadas:"
echo "   ✅ Actualización de dependencias"
echo "   ✅ Configuración de permisos"
echo "   ✅ Limpieza de caché"
echo "   ✅ Optimización de Laravel"
echo "   ✅ Ejecución de migraciones"
echo "   ✅ Verificación final de permisos"
echo ""
echo "🌐 La aplicación está lista para producción!"
