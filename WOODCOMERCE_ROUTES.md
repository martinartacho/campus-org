# WoodComerce v2 - Sistema de Exportación WooCommerce

## Rutas Principales

### 1. Interfaz Principal
```
GET /campus/courses/woodcomerce
```
- **Acceso:** Sin autenticación (rutas públicas)
- **Descripción:** Página principal de exportación WooCommerce
- **Vista:** `campus.courses.woodcomerce`
- **Controller:** `WoodComerceController@index`
- **Layout:** `campus.shared.layout`

### 2. Exportación Completa
```
GET /campus/courses/woodcomerce/export
```
- **Acceso:** Sin autenticación
- **Descripción:** Genera y descarga CSV completo con todos los cursos
- **Controller:** `WoodComerceController@export`
- **Formato:** Descarga directa de archivo CSV

### 3. Exportación Seleccionada
```
POST /campus/courses/woodcomerce/export-selected
```
- **Acceso:** Sin autenticación
- **Descripción:** Genera y descarga CSV con cursos seleccionados
- **Controller:** `WoodComerceController@exportSelected`
- **Parámetros:** `course_ids[]` (array de IDs)
- **Formato:** JSON con file_url para descarga

### 4. Descarga de Archivos
```
GET /campus/courses/woodcomerce/download/{filename}
```
- **Acceso:** Sin autenticación
- **Descripción:** Descarga archivo CSV generado
- **Controller:** `WoodComerceController@download`
- **Formato:** Descarga directa de archivo

## Rutas API

### 5. Lista de Cursos
```
GET /api/courses/list
```
- **Acceso:** Sin autenticación
- **Descripción:** Lista de cursos para select multi-select
- **Controller:** `Api\CoursesController@list`
- **Formato:** JSON con lista de cursos (id, code, title, format, price, parent_id)

## URLs de Testing

### Desarrollo Local
```
http://campus-local.test/campus/courses/woodcomerce
```

### Producción
```
https://campus.org/campus/courses/woodcomerce
```

## Flujo de Uso

### 1. Acceso a la Interfaz
1. Navegar a: `/campus/courses/woodcomerce`
2. Interfaz accesible sin autenticación

### 2. Exportación Completa
1. Hacer clic en "Exportar CSV Completo"
2. Descargar archivo CSV automáticamente
3. Contiene todos los cursos activos y públicos

### 3. Exportación Seleccionada
1. Seleccionar cursos específicos en el multi-select
2. Usar casos de test predefinidos (AOBERTA, CHIKUNG)
3. Hacer clic en "Exportar Cursos Seleccionados"
4. Descargar CSV generado dinámicamente

## Funcionalidades Disponibles

### Funcionalidad Simplificada
- **Botón:** "Exportar CSV Completo"
- **Alcance:** Todos los cursos (78 cursos)
- **Uso:** Exportación masiva rápida

### Funcionalidad Avanzada
- **Select:** Multi-select con búsqueda
- **Casos de Test:** AOBERTA Digital, Chi Kung
- **Validación:** Selección mínima requerida
- **Feedback:** Mensajes de estado en tiempo real

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

## Integración con Dashboard

### Acción Rápida Admin
- **Ubicación:** Dashboard admin
- **Estilo:** Tarjeta amarilla con icono de descarga
- **Acceso:** Directo a `/campus/courses/woodcomerce`
- **Roles:** Admin y super-admin

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
- **Web:** Sin middleware (rutas públicas)
- **API:** Sin autenticación

### Permisos
- **Exportación:** Acceso público sin restricciones
- **API:** Acceso público para select de cursos
- **Testing:** Mismos permisos que exportación

## Archivos Generados

### Ubicación
```
storage/app/exports/
```

### Nomenclatura
- **Completo:** `wc-export-YYYY-MM-DD-HH-II-SS.csv`
- **Seleccionado:** `wc-selected-export-YYYY-MM-DD-HH-II-SS.csv`

### Limpieza
- Archivos temporales eliminados automáticamente
- Logs limpios después de desarrollo

