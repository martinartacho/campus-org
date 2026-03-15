# 🔐 Guía de Encriptación de Datos Sensibles

## 📋 Resumen
**Fecha:** 2026-03-15  
**Rama:** Security_dades_secrets  
**Versión Laravel:** 9+  
**Estado:** ✅ Implementado

---

## 🎯 ¿Qué es Laravel Encrypted Casts?

Laravel 9+ permite encriptar automáticamente datos sensibles usando el cast `encrypted`. Esto protege la información en la base de datos mientras mantiene el acceso transparente desde el código.

### **Ejemplo:**
```php
protected $casts = [
    'iban' => 'encrypted',
];
```

---

## 🔐 Implementación

### **📁 Modelos Actualizados:**

#### **1. CampusTeacher**
```php
protected $casts = [
    'hiring_date' => 'date',
    'areas' => 'array',
    'metadata' => 'array',
    // 🔐 Datos sensibles encriptados
    'iban' => 'encrypted',
    'bank_titular' => 'encrypted',
    'fiscal_id' => 'encrypted',
    'dni' => 'encrypted',
    'phone' => 'encrypted',
    'address' => 'encrypted',
    'postal_code' => 'encrypted',
    'email' => 'encrypted'
];
```

#### **2. CampusTeacherPayment**
```php
protected $casts = [
    'metadata' => 'array',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    // 🔐 Datos sensibles encriptados
    'first_name' => 'encrypted',
    'last_name' => 'encrypted',
    'fiscal_id' => 'encrypted',
    'postal_code' => 'encrypted',
    'city' => 'encrypted',
    'iban' => 'encrypted',
    'bank_titular' => 'encrypted',
    'fiscal_situation' => 'encrypted',
    'invoice' => 'encrypted',
    'observacions' => 'encrypted'
];
```

#### **3. User**
```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'fcm_token' => 'encrypted', // 🔐 Token FCM sensible
        'email' => 'encrypted', // 🔐 Email encriptado
    ];
}
```

---

## 🗄️ Migración de Datos Existentes

### **📋 Migración Creada:**
```
database/migrations/2026_03_15_130127_encrypt_sensitive_teacher_data.php
```

### **🔄 Proceso:**
1. **Leer datos existentes** de cada modelo
2. **Reasignar valores** para activar encriptación
3. **Guardar registros** con datos encriptados
4. **Verificar** encriptación correcta

---

## 🛠️ Comando de Verificación

### **🔍 Comando Creado:**
```bash
php artisan encryption:verify --demo
```

### **📊 Funcionalidades:**
- **Demostración:** Muestra ejemplo de encriptación
- **Verificación:** Revisa datos encriptados en cada modelo
- **Estadísticas:** Muestra conteo de datos encriptados
- **Validación:** Confirma que los datos están protegidos

---

## 🔓 ¿Cómo Funciona?

### **🔄 Flujo de Encriptación:**

#### **Guardado:**
```php
$teacher->iban = 'ES1234567890123456789012345';
$teacher->save(); // Laravel encripta automáticamente
```

#### **Lectura:**
```php
$teacher = CampusTeacher::find(1);
echo $teacher->iban; // Laravel desencripta automáticamente
```

#### **Base de Datos:**
```sql
-- Datos encriptados en BD
SELECT iban FROM campus_teachers WHERE id = 1;
-- Resultado:eyJpdiI6IjJ... (texto encriptado)
```

---

## 🛡️ Beneficios de Seguridad

### **✅ Ventajas:**
- **Protección GDPR:** Cumplimiento con regulaciones
- **Seguridad BD:** Datos sensibles protegidos en reposo
- **Acceso transparente:** Sin cambios en el código
- **Automático:** Laravel maneja encriptación/desencriptación
- **Reversible:** Puede desactivarse si es necesario

### **🔒 Nivel de Seguridad:**
- **Algoritmo:** AES-256-CBC (por defecto Laravel)
- **Clave:** APP_KEY de .env
- **Integridad:** HMAC para verificar datos
- **Salting:** Único por registro

---

## 📊 Datos Protegidos

### **🏫 CampusTeacher:**
- **IBAN:** Datos bancarios
- **Bank Titular:** Titular de cuenta
- **Fiscal ID:** Identificación fiscal
- **DNI:** Documento nacional
- **Phone:** Teléfono
- **Address:** Dirección
- **Postal Code:** Código postal
- **Email:** Correo electrónico

### **💳 CampusTeacherPayment:**
- **Todos los datos personales** del pago
- **IBAN y datos bancarios**
- **Información fiscal**
- **Observaciones privadas**

### **👤 User:**
- **Email:** Correo electrónico
- **FCM Token:** Token de notificaciones

---

## 🎯 Uso Práctico

### **📝 Ejemplo de Código:**
```php
// Crear profesor con datos sensibles
$teacher = new CampusTeacher([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'iban' => 'ES1234567890123456789012345', // Se encriptará
    'email' => 'john@example.com' // Se encriptará
]);

$teacher->save();

// Acceder a datos (desencriptación automática)
echo $teacher->iban; // 'ES1234567890123456789012345'
```

### **🔍 Búsqueda y Filtros:**
```php
// Búsqueda por datos encriptados (requiere encriptar el valor de búsqueda)
$iban = 'ES1234567890123456789012345';
$encryptedIban = encrypt($iban);

$teachers = CampusTeacher::where('iban', $encryptedIban)->get();
```

---

## ⚠️ Consideraciones Importantes

### **🚨 Limitaciones:**
- **Búsquedas:** No se puede buscar directamente por datos encriptados
- **Queries LIKE:** No funcionan con campos encriptados
- **Exportación:** Datos se exportan encriptados
- **Backup:** Contiene datos encriptados

### **✅ Soluciones:**
- **Índices separados:** Para búsquedas frecuentes
- **Hashing:** Para comparaciones sin encriptación
- **Vistas:** Para mostrar datos desencriptados
- **Exportación controlada:** Desencriptar en capa de aplicación

---

## 🔧 Mantenimiento

### **📋 Tareas Regulares:**
1. **Verificar encriptación:** `php artisan encryption:verify`
2. **Rotación de claves:** Si se cambia APP_KEY
3. **Backup de claves:** Guardar APP_KEY segura
4. **Monitorización:** Revisar acceso a datos sensibles

### **🔄 Rotación de Claves:**
```bash
# Generar nueva clave
php artisan key:generate

# Reencriptar todos los datos (requiere script personalizado)
php artisan encryption:rekey
```

---

## 🎓 Mejores Prácticas

### **✅ Recomendaciones:**
1. **Identificar datos sensibles** antes de encriptar
2. **Documentar campos encriptados**
3. **Limitar acceso** a datos desencriptados
4. **Auditar accesos** a información sensible
5. **Backup seguro** de APP_KEY

### **🛡️ Seguridad Adicional:**
- **Permisos:** Control de acceso a nivel de aplicación
- **Logging:** Registrar accesos a datos sensibles
- **Monitorización:** Alertas de accesos anómalos
- **Formación:** Educar al equipo sobre manejo de datos

---

## 📞 Soporte y Troubleshooting

### **🔧 Comandos Útiles:**
```bash
# Verificar encriptación
php artisan encryption:verify --demo

# Limpiar caché
php artisan config:clear
php artisan cache:clear

# Verificar APP_KEY
php artisan key:show
```

### **❌ Problemas Comunes:**
- **Datos no encriptados:** Ejecutar migración
- **Error de desencriptación:** Verificar APP_KEY
- **Performance:** Considerar índices adicionales
- **Backup:** Asegurar compatibilidad

---

## 🎯 Conclusión

### **✅ Implementación Exitosa:**
- **Datos sensibles protegidos** en base de datos
- **Acceso transparente** desde código
- **Cumplimiento GDPR** y normativas
- **Mantenimiento sencillo** con comandos

### **🔐 Seguridad Mejorada:**
- **Protección en reposo** contra accesos no autorizados
- **Encriptación automática** sin cambios en código
- **Verificación continua** con comandos dedicados
- **Documentación completa** para mantenimiento

---

**📅 Última actualización:** 2026-03-15  
**🔄 Versión:** v1.0.0  
**📍 Rama:** Security_dades_secrets  
**✅ Estado:** IMPLEMENTADO Y VERIFICADO
