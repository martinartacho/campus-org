#!/bin/bash
echo "🔍 Analizando funciones en controladores..."
echo "=========================================="

# Buscar funciones en controladores
mapfile -t FUNCIONES < <(grep -RhoP 'function\s+\K\w+' app/Http/Controllers | sort -u)

USADAS=()
NO_USADAS=()

for FUNC in "${FUNCIONES[@]}"; do
    # Buscar la función en todo el proyecto (excepto vendor y node_modules)
    if grep -R --exclude-dir={vendor,node_modules} -n "\b$FUNC\b" . | grep -vq "function $FUNC"; then
        USADAS+=("$FUNC")
    else
        NO_USADAS+=("$FUNC")
    fi
done

# Mostrar primero las usadas
for FUNC in "${USADAS[@]}"; do
    ARCHIVO=$(grep -R "function $FUNC" app/Http/Controllers | cut -d: -f1)
    echo "✅ En uso: $FUNC (en $ARCHIVO)"
done

# Mostrar al final las posibles no usadas
for FUNC in "${NO_USADAS[@]}"; do
    ARCHIVO=$(grep -R "function $FUNC" app/Http/Controllers | cut -d: -f1)
    echo "⚠️ Posible no usada: $FUNC (en $ARCHIVO)"
done
