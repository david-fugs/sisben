# CORRECCIÓN SQL ERROR EN exportarAll.php

**Fecha:** 2025-03-07  
**Archivos modificados:** 
- `code/exportares/exportarAll.php` (línea 709-751)
- `code/eventan/movimientosEncuesta.php` (validación de integrantes)

---

## 🔴 ERROR ENCONTRADO

```
Fatal error: Uncaught mysqli_sql_exception: Unknown column 'm.gen_integVenta' in 'field list'
```

---

## 🔍 CAUSA DEL PROBLEMA

La consulta SQL para la hoja "Rangos Barrio - Movimientos" intentaba acceder a columnas que **NO EXISTEN** en la tabla `movimientos`:

### Columnas Incorrectas Usadas:
- ❌ `m.gen_integVenta` 
- ❌ `m.rango_integVenta`

### Diferencia con Sistema de Ventanilla:
**Ventanilla:**
- Tabla `encventanilla` SÍ guarda género y edad de la persona principal
- Tabla `integventanilla` guarda integrantes adicionales
- Por eso puede contar: 1 (persona principal) + N integrantes

**Movimientos:**
- Tabla `movimientos` NO guarda género ni edad de la persona principal
- Solo guarda: doc_encVenta, nom_encVenta, tipo_movimiento, etc.
- Tabla `integmovimientos_independiente` guarda TODOS los integrantes
- **IMPORTANTE:** El PRIMER integrante ES la persona principal

---

## ✅ SOLUCIÓN APLICADA

### 1. Corrección de Nombres de Columnas

#### Columnas Correctas en integmovimientos_independiente:
- ✅ `gen_integMovIndep` (no gen_integVenta)
- ✅ `rango_integMovIndep` (no rango_integVenta)

#### Query Corregida:
```sql
SELECT 
    b.nombre_bar,
    c.nombre_com,
    -- ANTES (INCORRECTO):
    -- SUM(CASE WHEN m.gen_integVenta = 'M' AND m.rango_integVenta LIKE '0 -%' THEN 1 ELSE 0 END +
    --     CASE WHEN im.gen_integVenta = 'M' AND im.rango_integVenta LIKE '0 -%' THEN 1 ELSE 0 END) AS masculino_0_6,
    
    -- AHORA (CORRECTO):
    SUM(CASE WHEN im.gen_integMovIndep = 'M' AND im.rango_integMovIndep LIKE '0 -%' THEN 1 ELSE 0 END) AS masculino_0_6,
    SUM(CASE WHEN im.gen_integMovIndep = 'F' AND im.rango_integMovIndep LIKE '0 -%' THEN 1 ELSE 0 END) AS femenino_0_6,
    -- ... (igual para todos los rangos de edad)
FROM movimientos m
LEFT JOIN integmovimientos_independiente im ON m.id_movimiento = im.id_movimiento
LEFT JOIN barrios b ON m.id_bar = b.id_bar
LEFT JOIN comunas c ON b.id_com = c.id_com
GROUP BY b.nombre_bar, b.id_bar, c.nombre_com
```

### 2. Validación de Integrantes en Formulario

Se agregó validación JavaScript en `movimientosEncuesta.php` para **requerir mínimo 1 integrante**:

```javascript
$('#form_contacto').on('submit', function(e) {
    var tipoMovimiento = $('#selectEF').val();
    var totalIntegrantes = $('#integrantes-container .formulario-dinamico').length;
    
    // Solo validar integrantes si NO es "Retiro ficha"
    if (tipoMovimiento !== 'Retiro ficha' && totalIntegrantes === 0) {
        e.preventDefault();
        alert('⚠️ Debe agregar al menos 1 integrante...');
        return false;
    }
});
```

---

## 📊 LÓGICA DEL SISTEMA DE MOVIMIENTOS

### Captura de Datos - Flujo Completo:

1. **Usuario ingresa documento** en `movimientosEncuesta.php`
2. **Si documento NO existe:**
   - Sistema automáticamente agrega 1 integrante
   - Este primer integrante = Persona Principal
3. **Si documento SÍ existe:**
   - Carga integrantes existentes (solo lectura)
   - Usuario puede agregar nuevo movimiento
4. **Usuario llena formulario:**
   - Primer integrante: género, edad, orientación, etc. (Persona Principal)
   - Integrantes adicionales: familiares o grupo
5. **Validación antes de enviar:**
   - Requiere mínimo 1 integrante (excepto "Retiro ficha")
   - El primer integrante debe tener género y rango de edad

### Almacenamiento en Base de Datos:

#### Tabla `movimientos`:
```
✅ Almacena:
- doc_encVenta (documento)
- nom_encVenta (nombre)
- tipo_movimiento (Inclusión, Inconformidad, etc.)
- fecha_movimiento
- id_bar, id_com (barrio, comuna)
- integra_encVenta (total de integrantes)

❌ NO almacena:
- Género de persona principal
- Edad de persona principal
```

#### Tabla `integmovimientos_independiente`:
```
✅ Almacena TODOS los integrantes (incluida persona principal):
- id_integmov_indep (autoincrement) 
- id_movimiento (FK)
- gen_integMovIndep (género)
- rango_integMovIndep (rango de edad)
- orientacionSexual
- condicionDiscapacidad
- ... (otros campos demográficos)

🔑 PRIMER integrante (id más bajo) = Persona Principal
🔑 Resto de integrantes = Familia/grupo
```

---

## 📊 IMPACTO DE LA CORRECCIÓN

### Sistema de Movimientos - Conteo Correcto:

La consulta SQL ahora cuenta **TODOS los integrantes** desde `integmovimientos_independiente`, lo cual es correcto porque:

- ✅ **Primer integrante** (id más bajo) = Persona principal
- ✅ **Resto de integrantes** = Integrantes adicionales
- ✅ **SUM(...)** cuenta todos por género y rango de edad

### Comparación con Otros Sistemas:

| Sistema | Persona Principal | Integrantes Adicionales | Almacenamiento | Conteo Total |
|---------|------------------|------------------------|----------------|--------------|
| **Ventanilla** | En `encventanilla`<br>(género/edad en tabla principal) | En `integventanilla` | 2 tablas separadas | Persona + Integrantes |
| **Información** | En `informacion`<br>(género/edad en tabla principal) | En `integrantes`<br>(si existe tabla) | 2 tablas separadas | Persona + Integrantes |
| **Movimientos** | En `integmovimientos_independiente`<br>(primer registro) | En `integmovimientos_independiente`<br>(resto de registros) | **1 tabla unificada** | Todos los integrantes |

### Ventaja del Sistema de Movimientos:

✅ **Estructura unificada:** Todos los integrantes en una sola tabla
✅ **Captura demográfica completa:** Género, edad, orientación, discapacidad, etnia, etc.
✅ **Validación automática:** Sistema requiere al menos 1 integrante (persona principal)
✅ **Precarga inteligente:** Autocompleta 1 integrante cuando documento no existe

---

## 📋 ESTRUCTURA DE TABLAS

### Tabla `movimientos`:
```
- id_movimiento (PK)
- doc_encVenta
- nom_encVenta
- tipo_movimiento
- fecha_movimiento
- observacion
- id_usu
- fec_reg_encVenta
- tipo_documento
- departamento_expedicion
- ciudad_expedicion
- fecha_expedicion
- dir_encVenta
- zona_encVenta
- id_com (comuna)
- id_bar (barrio)
- otro_bar_ver_encVenta
- integra_encVenta (total integrantes)
- num_ficha_encVenta
- sisben_nocturno
- estado_ficha
- fecha_alta_movimiento
- fecha_edit_movimiento

❌ NO TIENE: gen_integVenta, rango_integVenta
```

### Tabla `integmovimientos_independiente`:
```
- id_integmov_indep (PK autoincrement)
- id_movimiento (FK → movimientos)
- doc_encVenta
- cant_integMovIndep (siempre 1)
- gen_integMovIndep ✅ (M/F/O)
- rango_integMovIndep ✅ (0 - 6, 7 - 12, etc.)
- orientacionSexual
- condicionDiscapacidad
- tipoDiscapacidad
- grupoEtnico
- victima
- mujerGestante
- cabezaFamilia
- experienciaMigratoria
- seguridadSalud
- nivelEducativo
- condicionOcupacion
- estado_integMovIndep
- fecha_alta_integMovIndep
- fecha_edit_integMovIndep
- id_usu

🔑 Primer registro (menor id_integmov_indep) = Persona Principal
```

---

## ✅ ESTADO ACTUAL

- [x] Error SQL corregido en exportarAll.php
- [x] Consulta usa nombres de columnas correctos
- [x] Validación agregada: requiere mínimo 1 integrante
- [x] Sistema precarga automáticamente 1 integrante cuando documento no existe
- [x] Comentarios en código actualizados para explicar lógica
- [x] No hay errores de sintaxis en PHP
- [x] Documentación completa creada

**El sistema ahora puede generar el Excel correctamente e incluye a la persona principal en los conteos de rangos de edad por barrio.**

---

## 🎯 RESUMEN EJECUTIVO

### Problema:
- SQL intentaba leer género/edad desde tabla `movimientos` (columnas no existen)

### Solución:
- Corregir nombres de columnas: `gen_integMovIndep`, `rango_integMovIndep`
- Contar TODOS los integrantes desde `integmovimientos_independiente`
- Agregar validación para requerir mínimo 1 integrante

### Resultado:
- ✅ Exportar Excel funciona sin errores
- ✅ Persona principal SÍ se cuenta (es el primer integrante)
- ✅ Rangos de edad por barrio incluyen todos los movimientos
- ✅ Sistema validado y documentado

---

*Corrección realizada: 2025-03-07*  
*Archivos modificados: exportarAll.php, movimientosEncuesta.php*
