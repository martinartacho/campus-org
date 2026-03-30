# 🚨 **ADVERTÈNCIES DE SEGURETAT CRÍTIQUES** 🚨

## **⚠️ AVÍS MORTAL: APP_KEY**

### **🚫 PROHIBICIÓN ABSOLUTA**

```bash
# ❌ NUNCA HACER ESTO EN PRODUCCIÓN
# ❌ ESTO DESTRUIRÁ TODOS LOS DATOS BANCARIOS
# ❌ LOS PROFESORES PERDERÁN ACCESO AL SISTEMA
# ❌ LOS DATOS SERÁN IRRECUPERABLES SIN BACKUP

APP_KEY=base64:clave_anterior
APP_KEY=base64:clave_nueva  # 💥💥💥 DESASTRE TOTAL 💥💥💥
```

### **💥 CONSECUENCIAS INMEDIATAS**

1. **PÉRDIDA TOTAL** de datos bancarios encriptados
2. **Error "The payload is invalid"** en todo el sistema
3. **Dashboard inaccesible** para todos los profesores
4. **Corrupción permanente** de datos sensibles
5. **Imposibilidad de login** para usuarios afectados

### **🔐 IMPACTO EN CAMPOS AFECTADOS**

| Campo | Estado tras cambio | Recuperación |
|-------|-------------------|--------------|
| `iban` | 💥 Corrupto | ❌ Imposible sin backup |
| `bank_titular` | 💥 Corrupto | ❌ Imposible sin backup |
| `fiscal_id` | 💥 Corrupto | ❌ Imposible sin backup |
| `beneficiary_iban` | 💥 Corrupto | ❌ Imposible sin backup |
| `beneficiary_titular` | 💥 Corrupto | ❌ Imposible sin backup |
| `beneficiary_dni` | 💥 Corrupto | ❌ Imposible sin backup |

---

## **🔄 PROCEDIMIENTO SEGURO DE CAMBIO DE CLAVE**

### **PASO 0: VERIFICACIÓN CRÍTICA**

```bash
# ¿REALMENTE NECESITAS CAMBIAR LA APP_KEY?
# ¿Has considerado ALTERNATIVAS?
# ¿Tienes un BACKUP RECIENTE?

echo "⚠️  VERIFICAR ANTES DE CONTINUAR ⚠️"
read -p "¿Tienes backup de producción? (s/n): " confirm
if [[ $confirm != "s" ]]; then
    echo "❌ OBTÉN UN BACKUP ANTES DE CONTINUAR"
    exit 1
fi
```

### **PASO 1: BACKUP COMPLETO**

```bash
# Backup de base de datos
mysqldump -u usuario -p base_datos > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup de archivos de configuración
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
cp -r config/ config.backup.$(date +%Y%m%d_%H%M%S)

# Exportar datos sensibles actuales
php artisan banking:export-all-sensitive-data > sensitive_data_export.json
```

### **PASO 2: CAMVIO CONTROLADO**

```bash
# Generar nueva clave
php artisan key:generate

# INMEDIATAMENTE después del cambio:
php artisan banking:migrate-all-to-encryption --force
```

### **PASO 3: VERIFICACIÓN EXTENSIVA**

```bash
# Verificar todos los profesores
php artisan banking:verify-all-teachers

# Test de desencriptación
php artisan banking:test-decryption --all-users

# Verificar dashboard
php artisan banking:test-dashboard-access
```

---

## **🚨 ESCENARIOS DE EMERGENCIA**

### **ESCENARIO 1: CAMBIO ACCIDENTAL DE APP_KEY**

```bash
# SÍNTOMAS:
# - Error 500 en dashboard de profesores
# - "The payload is invalid" en logs
# - Profesores no pueden acceder

# ACCIÓN INMEDIATA:
php artisan banking:emergency-recovery

# VERIFICAR:
php artisan banking:check-system-status
```

### **ESCENARIO 2: DATOS CORRUPTOS**

```bash
# SÍNTOMAS:
# - Algunos IBANs muestran caracteres extraños
# - Errores de desencriptación esporádicos
# - Campos vacíos que deberían tener datos

# ACCIÓN INMEDIATA:
php artisan banking:recover-corrupted-data --auto-fix

# VERIFICAR:
php artisan banking:validate-all-iban-formats
```

### **ESCENARIO 3: MIGRACIÓN FALLIDA**

```bash
# SÍNTOMAS:
# - Mezcla de datos encriptados y no encriptados
# - Doble encriptación detectada
# - Formularios no guardan correctamente

# ACCIÓN INMEDIATA:
php artisan banking:fix-migration-issues --comprehensive

# VERIFICAR:
php artisan banking:audit-encryption-status
```

---

## **📋 CHECKLIST DE SEGURIDAD OBLIGATORIO**

### **✅ ANTES DE CUALQUIER CAMBIO**

- [ ] **BACKUP COMPLETO** de base de datos (< 1 hora)
- [ ] **BACKUP DE .ENV** y archivos de configuración
- [ ] **EXPORTAR DATOS SENSIBLES** actuales
- [ ] **DOCUMENTAR PROCEDIMIENTO** completo
- [ ] **TENER PLAN DE ROLLBACK** preparado
- [ ] **NOTIFICAR A EQUIPO** del mantenimiento
- [ ] **PREPARAR COMANDOS** de emergencia
- [ ] **TEST EN STAGING** primero

### **✅ DESPUÉS DE CUALQUIER CAMBIO**

- [ ] **VERIFICAR ACCESO** de todos los profesores
- [ ] **COMPROBAR DESENCRIPTACIÓN** de datos bancarios
- [ ] **VALIDAR FORMULARIOS** de edición
- [ ] **REVISAR LOGS** por errores
- [ ] **CONFIRMAR INTEGRIDAD** de datos
- [ ] **DOCUMENTAR RESULTADOS** del cambio
- [ ] **ACTUALIZAR DOCUMENTACIÓN** si es necesario

---

## **🔐 DETALLES TÉCNICOS DE SEGURIDAD**

### **Algoritmo de Encriptación**
- **Tipo**: AES-256-CBC
- **Clave**: APP_KEY (32 bytes)
- **IV**: 16 bytes (aleatorio por encriptación)
- **MAC**: HMAC-SHA256 para integridad
- **Codificación**: Base64 para almacenamiento

### **Protección de Datos**
```php
// Nivel de seguridad implementado
class BankingEncryptionService
{
    // ✅ Encriptación AES-256
    // ✅ Validación de formato IBAN
    // ✅ Detección de datos corruptos
    // ✅ Masking automático
    // ✅ Manejo seguro de errores
    // ✅ Logging de operaciones críticas
}
```

### **Campos Críticos Protegidos**
```php
// Prioridad 1: CRÍTICO
$banking_fields = [
    'iban',              // IBAN bancario
    'bank_titular',       // Titular cuenta
    'fiscal_id',          // ID fiscal
];

// Prioridad 2: ALTO
$beneficiary_fields = [
    'beneficiary_iban',   // IBAN beneficiario
    'beneficiary_titular', // Titular beneficiario
    'beneficiary_dni',      // DNI beneficiario
];
```

---

## **📞 CONTACTO DE EMERGENCIA**

### **PARA INCIDENTES DE SEGURIDAD CRÍTICOS**

🚨 **Emergencia Inmediata**:
- **Email**: security@campus.org
- **Teléfono**: [+34] XXX XXX XXX
- **Slack**: #security-emergency

🔧 **Soporte Técnico**:
- **Email**: support@campus.org
- **GitHub Issues**: https://github.com/org/campus/issues
- **Documentación**: https://docs.campus.org

---

## **⚠️ ADVERTENCIA FINAL**

**ESTE SISTEMA MANEJA DATOS BANCARIOS REALES**
**CUALQUIER ERROR PUEDE AFECTAR A PERSONAS REALES**
**SEGUIR ESTRICTAMENTE LOS PROCEDIMIENTOS DOCUMENTADOS**
**LA SEGURIDAD DE LOS DATOS ES RESPONSABILIDAD DE TODOS**

**¿ESTÁS SEGURO DE QUE QUIERES CONTINUAR?** 🚨⚠️🚨

---
**Versión**: 2.0 - Sistema de Encriptación Bancaria  
**Actualización**: $(date)  
**Prioridad**: CRÍTICA  
**Aprobado**: Equipo de Seguridad Campus
