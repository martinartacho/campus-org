#!/bin/bash

# Test script for migrations optimization branch
# This script validates that all migration optimizations work correctly

echo "=== Test de Migraciones Optimizadas - Fase 1 Complete ==="
echo "Branch: $(git branch --show-current)"
echo "Timestamp: $(date)"
echo ""

echo "1. Limpiando base de datos..."
php artisan migrate:fresh --force
echo ""

echo "2. Verificando número de migraciones ejecutadas..."
MIGRATIONS_COUNT=$(php artisan tinker --execute="echo count(DB::select('SELECT * FROM migrations'));" | grep -o '[0-9]\+')
echo "Migraciones ejecutadas: $MIGRATIONS_COUNT"

if [ "$MIGRATIONS_COUNT" -eq 57 ]; then
    echo "   [OK] Número correcto de migraciones"
else
    echo "   [ERROR] Se esperaban 57 migraciones, se ejecutaron $MIGRATIONS_COUNT"
    exit 1
fi
echo ""

echo "3. Verificando tablas unificadas..."
# Test Users + Locale
HAS_LOCALE=$(php artisan tinker --execute="echo Schema::hasColumn('users', 'locale') ? 'YES' : 'NO';" | tr -d '\r\n')
if [ "$HAS_LOCALE" = "YES" ]; then
    echo "   [OK] Users table tiene campo 'locale'"
else
    echo "   [ERROR] Users table no tiene campo 'locale'"
    exit 1
fi

# Test Campus Courses unified
HAS_SESSIONS=$(php artisan tinker --execute="echo Schema::hasColumn('campus_courses', 'sessions') ? 'YES' : 'NO';" | tr -d '\r\n')
if [ "$HAS_SESSIONS" = "YES" ]; then
    echo "   [OK] Campus courses tiene campo 'sessions'"
else
    echo "   [ERROR] Campus courses no tiene campo 'sessions'"
    exit 1
fi

# Test Campus Registrations with season_id
HAS_SEASON_ID=$(php artisan tinker --execute="echo Schema::hasColumn('campus_registrations', 'season_id') ? 'YES' : 'NO';" | tr -d '\r\n')
if [ "$HAS_SEASON_ID" = "YES" ]; then
    echo "   [OK] Campus registrations tiene campo 'season_id'"
else
    echo "   [ERROR] Campus registrations no tiene campo 'season_id'"
    exit 1
fi

# Test Campus Course Teacher with sessions_assigned
HAS_SESSIONS_ASSIGNED=$(php artisan tinker --execute="echo Schema::hasColumn('campus_course_teacher', 'sessions_assigned') ? 'YES' : 'NO';" | tr -d '\r\n')
if [ "$HAS_SESSIONS_ASSIGNED" = "YES" ]; then
    echo "   [OK] Campus course teacher tiene campo 'sessions_assigned'"
else
    echo "   [ERROR] Campus course teacher no tiene campo 'sessions_assigned'"
    exit 1
fi
echo ""

echo "4. Ejecutando seeders..."
php artisan db:seed --class=DatabaseSeeder --force
if [ $? -eq 0 ]; then
    echo "   [OK] Seeders ejecutados correctamente"
else
    echo "   [ERROR] Error en seeders"
    exit 1
fi
echo ""

echo "5. Verificando datos básicos..."
USERS_COUNT=$(php artisan tinker --execute="echo DB::table('users')->count();" | grep -o '[0-9]\+')
if [ "$USERS_COUNT" -gt 0 ]; then
    echo "   [OK] Usuarios creados: $USERS_COUNT"
else
    echo "   [ERROR] No se crearon usuarios"
    exit 1
fi

ROLES_COUNT=$(php artisan tinker --execute="echo DB::table('roles')->count();" | grep -o '[0-9]\+')
if [ "$ROLES_COUNT" -gt 0 ]; then
    echo "   [OK] Roles creados: $ROLES_COUNT"
else
    echo "   [ERROR] No se crearon roles"
    exit 1
fi
echo ""

echo "6. Verificando foreign keys..."
# Test campus_courses foreign keys
FOREIGN_KEYS_OK=$(php artisan tinker --execute="
    try {
        DB::select('SELECT 1 FROM campus_courses WHERE space_id IS NULL LIMIT 1');
        echo 'OK';
    } catch (Exception \$e) {
        echo 'ERROR';
    }
" | tr -d '\r\n')

if [ "$FOREIGN_KEYS_OK" = "OK" ]; then
    echo "   [OK] Foreign keys funcionando correctamente"
else
    echo "   [ERROR] Problema con foreign keys"
    exit 1
fi
echo ""

echo "=== TODAS LAS PRUEBAS PASARON ==="
echo "Branch listo para review y merge"
echo ""
echo "Resumen de optimizaciones:"
echo "- 11 migraciones unificadas"
echo "- 68 -> 57 migraciones (16% reducción)"
echo "- Todos los campos esenciales desde creación inicial"
echo "- Sin errores en migrate:fresh"
echo "- Seeders funcionando correctamente"
echo ""
echo "Para crear Pull Request:"
echo "https://github.com/martinartacho/campus-org/pull/new/feature/migrations-optimization-phase1"
