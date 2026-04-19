# WoodComerce - Sistema de Exportación WooCommerce

## Rutas Principales

### 1. Interfaz Principal
```
GET /campus/courses/woodcomerce
```
- **Acceso:** Roles: super-admin, admin, manager
- **Descripción:** Página principal de exportación WooCommerce
- **Vista:** `campus.courses.woodcomerce`
- **Controller:** `WoodComerceController@index`

### 2. Exportación Completa
```
GET /campus/courses/woodcomerce/export
```
- **Acceso:** Roles: super-admin, admin, manager
- **Descripción:** Genera y descarga CSV completo
- **Controller:** `WoodComerceController@export`
- **Formato:** Descarga directa de archivo CSV

### 3. Vista Previa
```
GET /campus/courses/woodcomerce/preview
```
- **Acceso:** Roles: super-admin, admin, manager
- **Descripción:** Muestra vista previa de primeros 10 productos
- **Controller:** `WoodComerceController@preview`
- **Formato:** JSON con datos de preview

### 4. Testing Específico
```
POST /campus/courses/woodcomerce/test
```
- **Acceso:** Roles: super-admin, admin, manager
- **Descripción:** Testing con cursos seleccionados
- **Controller:** `WoodComerceController@test`
- **Parámetros:** `course_ids[]` (array de IDs)
- **Formato:** JSON con resultados del test

## Rutas API

### 5. Lista de Cursos
```
GET /api/courses/list
```
- **Acceso:** Autenticación API requerida
- **Descripción:** Lista de cursos para select de testing
- **Controller:** `Api\CoursesController@list`
- **Formato:** JSON con lista de cursos (id, code, title, format, price, parent_id)

## URLs de Testing

### Desarrollo Local
```
http://campus-org.test/campus/courses/woodcomerce
```

### Producción
```
https://dev.campus.org/campus/courses/woodcomerce
```

## Flujo de Testing Recomendado

### 1. Acceso a la Interfaz
1. Iniciar sesión con rol manager/admin/super-admin
2. Navegar a: `/campus/courses/woodcomerce`

### 2. Vista Previa
1. Hacer clic en "Vista Previa"
2. Verificar estructura de productos variables y variaciones
3. Comprobar mapeo de categorías y atributos

### 3. Testing Específico
1. Seleccionar cursos específicos del select
2. Hacer clic en "Probar Seleccionados"
3. Verificar resultados del test

### 4. Exportación Completa
1. Hacer clic en "Exportar CSV Completo"
2. Descargar archivo CSV
3. Validar estructura en WooCommerce

## Casos de Test Reales

### Caso 1: "Aula oberta al món digital"
- **Cursos:** AOBERTA-001 (parent, ID:32), AOBERTA-002 (child, ID:59)
- **Validar:** Producto variable + variación online
- **Precios:** Presencial (50,00) vs Online (30,00)
- **IDs para testing:** 32,59

### Caso 2: "Chi Kung"
- **Cursos:** CHIKUNG-001 (parent, ID:2), CHIKUNG-002 (child, ID:48), CHIKUNG-003 (child, ID:49)
- **Validar:** Producto variable + 2 variaciones
- **Atributos:** Dilluns vs Dijous
- **IDs para testing:** 2,48,49

## Lógica de Procesamiento

### Productos Variables
- **Condición:** `parent_id IS NULL` Y tiene hijos
- **SKU:** `course.code` del parent
- **Stock:** Suma de `max_students` de todas las variaciones
- **Precio:** Máximo de precios de variaciones

### Variaciones
- **Condición:** `parent_id IS NOT NULL`
- **SKU:** `course.code` de la variación
- **Parent:** `course.code` del parent (NO el ID)
- **Stock:** `max_students` de la variación
- **Precio:** `price` de la variación

### Productos Simples
- **Condición:** `parent_id IS NULL` Y sin hijos
- **SKU:** `course.code`
- **Stock:** `max_students`
- **Precio:** `price`

## Validaciones Esperadas

### Estructura CSV
- **Productos Variables:** type=variable, SKU=parent_code
- **Variaciones:** type=variation, SKU=child_code, parent=parent_code
- **Atributos:** Format, Horario, Ubicación (detectados automáticamente)
- **Stock:** Basado en max_students

### Mapeo de Datos
- **Categorías:** Dinámicas desde campus_categories
- **Precios:** Correctos para cada variación
- **Parent SKU:** Código del curso parent (ej: AOBERTA-001)

## Errores Comunes

### 403 Forbidden
- **Causa:** Usuario sin rol adecuado
- **Solución:** Asignar rol manager/admin/super-admin

### 401 Unauthorized (API)
- **Causa:** Token API inválido
- **Solución:** Verificar autenticación API

### 500 Server Error
- **Causa:** Error en proceso ETL
- **Solución:** Revisar logs en `storage/logs/laravel.log`

## Logs y Debugging

### Activar Logging
```php
Log::info('WoodComerce: Proceso iniciado');
Log::error('WoodComerce: ' . $e->getMessage());
```

### Ver Logs
```bash
tail -f storage/logs/laravel.log | grep "WoodComerce"
```

## Campos Requeridos WooCommerce

### Cabeceras CSV
- type: Tipo de producto (variable/variation/simple)
- sku: Código único del producto
- name: Nombre del producto
- published: 1 para publicado, 0 para borrador
- description: Descripción del producto
- regular_price: Precio base
- manage_stock: yes/no para gestión de stock
- stock_quantity: Cantidad disponible
- categories: Categorías separadas por >
- attributes: Atributos del producto (JSON)
- default_attributes: Atributos por defecto (JSON)
- parent: SKU del producto parent (solo variaciones)
- attribute_values: Valores de atributos (JSON)

## Seguridad

### Middleware Aplicado
- **Web:** `role:super-admin|admin|manager`
- **API:** `auth:api`

### Permisos Requeridos
- **Exportación:** Roles de gestión
- **API:** Autenticación válida
- **Testing:** Mismos permisos que exportación
