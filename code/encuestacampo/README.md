# Módulo de Encuestas de Campo

Este módulo implementa un sistema completo para gestionar encuestas de campo del SISBEN, separado del sistema de encuestas de ventanilla.

## Archivos creados

### Scripts principales
- `addsurvey1.php` - Formulario para crear nuevas encuestas de campo
- `showsurvey.php` - Lista y búsqueda de encuestas realizadas (con diseño simple y menos colores)
- `editsurvey.php` - Editar encuestas existentes
- `viewsurvey.php` - Ver detalles completos de una encuesta
- `deletesurvey.php` - Eliminar encuestas

### Scripts de procesamiento
- `processsurvey.php` - Procesa el formulario de nueva encuesta
- `updatesurvey.php` - Procesa las actualizaciones de encuestas

### Scripts auxiliares
- `obtener_barrios.php` - API para obtener lista de barrios urbanos
- `obtener_veredas.php` - API para obtener lista de veredas rurales

### Base de datos
- `crear_tabla_encuestacampo.sql` - Script SQL para crear las tablas necesarias

## Tablas de base de datos

### `encuestacampo`
Tabla principal que almacena la información del hogar encuestado:
- Información básica: fecha, dirección, teléfono
- Localización: zona (urbana/rural), barrio/vereda, comuna
- Información SISBEN: sisbenizado, ficha, puntaje
- Vivienda: estrato, tipo, material, servicios públicos
- Metadatos: fechas de creación/edición, usuario, estado

### `integcampo`
Tabla de integrantes del hogar:
- Identificación: tipo y número de documento
- Datos personales: nombres, apellidos, fecha nacimiento, sexo
- Información familiar: parentesco, estado civil
- Información socioeconómica: educación, ocupación, ingresos
- Condiciones especiales: discapacidad, embarazo, lactancia, etnia
- Metadatos: fechas, observaciones, estado

## Características principales

### Formulario adaptativo
- Se adapta según la zona seleccionada (urbana/rural)
- Campos condicionales para información SISBEN
- Validación de campos requeridos

### Gestión de integrantes
- Agregar múltiples integrantes dinámicamente
- Formularios completos para cada integrante
- Eliminación individual de integrantes

### Búsqueda y filtros
- Búsqueda por dirección
- Filtros por zona, estado SISBEN, fecha
- Paginación de resultados

### Diseño simplificado
- Colores sobrios y profesionales
- Interface limpia y funcional
- Menos elementos visuales distractores comparado con el módulo de ventanilla

### Permisos
- Los usuarios solo pueden editar/eliminar sus propias encuestas
- Los administradores tienen acceso completo

## Diferencias con el módulo de ventanilla

1. **Tablas separadas**: Usa `encuestacampo` e `integcampo` en lugar de `encventanilla` e `integventanilla`
2. **Campos específicos**: Incluye campos adicionales relevantes para encuestas de campo
3. **Diseño simplificado**: Menos colores y efectos visuales
4. **Localización**: Diferenciación clara entre zona urbana (barrios) y rural (veredas)

## Instalación

1. Ejecutar el script SQL para crear las tablas:
```sql
source crear_tabla_encuestacampo.sql
```

2. Verificar que las tablas se crearon correctamente:
```sql
SHOW TABLES LIKE '%campo%';
```

3. Agregar enlaces en el menú principal del sistema según sea necesario.

## Uso

1. **Crear encuesta**: Acceder a `addsurvey1.php`
2. **Ver encuestas**: Acceder a `showsurvey.php`
3. **Editar encuesta**: Desde la lista, hacer clic en el botón de editar
4. **Ver detalles**: Desde la lista, hacer clic en el botón de ver
5. **Eliminar encuesta**: Desde la lista, hacer clic en el botón de eliminar (con confirmación)

## Seguridad

- Validación de sesión en todos los archivos
- Uso de prepared statements para prevenir SQL injection
- Verificación de permisos antes de operaciones de edición/eliminación
- Transacciones para mantener consistencia de datos
