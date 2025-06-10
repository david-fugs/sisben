# Plan de Migración - Tabla Movimientos

## IMPORTANTE: HACER BACKUP DE LA BASE DE DATOS ANTES DE EJECUTAR

## Paso 1: Modificación de la Base de Datos

### 1.1. Crear tabla temporal con nueva estructura
```sql
CREATE TABLE movimientos_nuevo (
    id_movimiento int(11) NOT NULL AUTO_INCREMENT,
    doc_encVenta varchar(20) NOT NULL,
    tipo_movimiento varchar(100) NOT NULL, -- 'inclusion', 'Inconformidad por clasificacion', etc.
    fecha_movimiento datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    observacion text,
    id_usu int(11) NOT NULL,
    id_encuesta int(11) DEFAULT NULL,
    id_informacion int(11) DEFAULT NULL,
    PRIMARY KEY (id_movimiento),
    KEY idx_doc_encVenta (doc_encVenta),
    KEY idx_fecha_movimiento (fecha_movimiento),
    KEY idx_tipo_movimiento (tipo_movimiento),
    KEY idx_id_usu (id_usu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### 1.2. Verificar la nueva estructura
```sql
DESCRIBE movimientos_nuevo;
```

### 1.3. Una vez verificada, renombrar tablas
```sql
RENAME TABLE movimientos TO movimientos_old;
RENAME TABLE movimientos_nuevo TO movimientos;
```

## Paso 2: Modificaciones en el Código

### 2.1. updateEncuesta.php - YA MODIFICADO ✅
- Se cambió de sistema de contadores a registro individual
- Cada movimiento genera un registro nuevo con fecha, tipo y observación

### 2.2. exportarEncuestador.php - PENDIENTE DE MODIFICAR
- Cambiar consulta SQL para nueva estructura
- Modificar encabezados de Excel
- Ajustar campos de salida

### 2.3. Otros archivos que pueden necesitar modificación:
- editencInfo.php
- addencVentanillaFamily1.php  
- editencVentanilla.php
- deleteencVentanilla1.php
- reportes varios (report17.php, report18.php, etc.)

## Paso 3: Estructura de la Nueva Tabla

### Campos anteriores (CONTADORES):
- inclusion (int)
- inconfor_clasificacion (int)  
- datos_persona (int)
- retiro_ficha (int)
- retiro_personas (int)
- retiro_personas_inconformidad (int)
- cantidad_informacion (int)
- cantidad_encuesta (int)

### Campos nuevos (REGISTRO INDIVIDUAL):
- id_movimiento (PRIMARY KEY)
- doc_encVenta (varchar)
- tipo_movimiento (varchar) - valores: 'inclusion', 'Inconformidad por clasificacion', 'modificación datos persona', 'Retiro ficha', 'Retiro personas', 'ENCUESTA NUEVA'
- fecha_movimiento (datetime)
- observacion (text)
- id_usu (int)
- id_encuesta (int, nullable)
- id_informacion (int, nullable)

## Paso 4: Ventajas de la Nueva Estructura

1. **Trazabilidad completa**: Cada movimiento tiene fecha y hora exacta
2. **Historial detallado**: Se puede ver cronológicamente qué pasó con cada documento
3. **Información completa**: Observaciones específicas por movimiento
4. **Escalabilidad**: Fácil agregar nuevos tipos de movimiento
5. **Reportes detallados**: Posibilidad de generar reportes más granulares

## Paso 5: Migracion de Datos (Opcional)

Si se desean migrar los datos existentes, se puede crear un script que:
1. Lea cada registro de movimientos_old
2. Genere registros individuales en movimientos basado en los contadores
3. Use la fecha actual o una fecha estimada para los registros históricos

## Notas Importantes

- La nueva estructura cambia fundamentalmente cómo se almacenan los movimientos
- Los reportes existentes necesitarán ser actualizados
- Se recomienda probar primero en un ambiente de desarrollo
- Mantener movimientos_old como respaldo por un tiempo
