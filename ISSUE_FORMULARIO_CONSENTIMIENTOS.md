# 🐛 Issue: Formulario Consentimientos - Crítico

## 📋 Información del Issue
- **Fecha:** 2026-03-20
- **Rama:** feature/mejoras-consentimientos-pdf
- **Tester:** Admin (campus.upg.cat)
- **Estado:** ❌ NO APTA PARA MERGE
- **Prioridad:** 🚨 CRÍTICA

## 🚨 Problemas Detectados

### 1. 🔐 Permisos Treasury (Relacionado Multiuser)
```
❌ Error: 403 User does not have the right roles
📍 URL: https://campus.upg.cat/treasury/dashboard
👤 Usuario: Admin
🔍 Relacionado con rama multiuser (permisos)
📝 Necesita corrección antes de merge
```

### 2. 📝 Formulario Consentimientos - Múltiples Fallos

#### 2.1 Opción "Renuncio al cobrament waived"
```
❌ NO funciona la opción waived_fee
🔍 Selecciona "Renuncio al cobrament waived"
❌ Botón "Guardar y enviar" - SIN ACCIÓN
❌ No procesa correctamente la opción
```

#### 2.2 Opción "Accepto cobrament (own)"
```
❌ Tampoco funciona correctamente
🔍 Selecciona "Accepto cobrament (own)"
❌ Botón "Guardar borrador" - FUNCIONA
❌ Botón "Guardar dades, crear PDF i finalitzar" - SIN ACCIÓN
```

#### 2.3 Validación de Campos
```
❌ Campos marcados como required pero mantienen datos previos
🔍 Ejemplo visible:
   - Nom *: Pepe (con validation.required)
   - Cognoms *: Prat i Soler (con validation.required)
   - Correu electrònic *: fempinyapp@gmail.com (con validation.required)
   - Telèfon *: +34 600 111 222 (con validation.required)
❌ La validación required no limpia los campos
❌ Los datos previos permanecen visibles
```

#### 2.4 Flujo de Guardado
```
❌ "Guardar borrador" - Funciona OK
❌ "Guardar y enviar" - NO funciona
❌ "Guardar dades, crear PDF i finalitzar" - NO funciona
❌ No completa el proceso de consentimiento
```

## 📍 URL de Testing
- **Login:** https://campus.upg.cat/admin/dashboard
- **Acceso Treasury:** https://campus.upg.cat/treasury/dashboard (❌ 403)
- **Formulario:** https://campus.upg.cat/teacher/access/acd40152-fdf6-4703-a99d-7e07501c5596/payments/BETTES-001

## 👤 Usuario de Testing
- **Email:** fempinyapp@gmail.com
- **Nombre:** Pepe Prat i Soler
- **Teléfono:** +34 600 111 222
- **DNI:** 12345678A

## 🎯 Pasos para Reproducir
1. Hacer login como admin
2. Intentar acceder a treasury/dashboard → 403
3. Acceder a formulario de consentimientos
4. Seleccionar "Renuncio al cobrament waived"
5. Click "Guardar y enviar" → Sin acción
6. Probar "Accepto cobrament (own)"
7. Click "Guardar dades, crear PDF i finalitzar" → Sin acción
8. Observar campos con validación required pero con datos previos

## 🔄 Relación con Otras Ramas
- **multiuser:** Problemas de permisos relacionados
- **Security_dades_secrets:** Puede afectar validación de datos sensibles
- **main:** Funcionalidad base estable

## ✅ Requisitos para Merge
1. **🔧 Corregir permisos treasury** (relacionado multiuser)
2. **📝 Arreglar lógica waived_fee** en formulario
3. **🔍 Corregir validación de campos required**
4. **⚡ Implementar acción en botones finales**
5. **🧪 Testing completo del flujo**

## 📊 Impacto
- **🚨 CRÍTICO:** Formulario no funcional
- **🚫 BLOQUEANTE:** No se puede hacer merge
- **👥 Usuarios afectados:** Todos los profesores
- **💳 Procesos afectados:** Consentimientos de pago

## 🏷️ Etiquetas
- `bug-critico`
- `formulario-consentimientos`
- `permisos-treasury`
- `validation-error`
- `no-merge-ready`
- `testing-failed`

---
**📅 Creado:** 2026-03-20  
**🔄 Estado:** Pendiente de corrección  
**👤 Reportado por:** Testing en producción
