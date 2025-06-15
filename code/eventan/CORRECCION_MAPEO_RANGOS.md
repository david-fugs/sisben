# CORRECCIÓN: Mapeo de Rangos de Edad - Integrantes Independientes

## Problema Resuelto
**Fecha:** $(Get-Date)  
**Archivo afectado:** `editMovimiento.php`  
**Líneas modificadas:** 686-692

## Descripción del Issue
Los rangos de edad de integrantes existentes no se mostraban seleccionados correctamente en el formulario de edición. Esto ocurría porque:

1. **En la base de datos:** El campo `rango_integMovIndep` almacena valores numéricos (1-7)
2. **En el formulario:** Los `<option value="">` utilizan valores de texto ("0 - 6", "7 - 12", etc.)
3. **Comparación incorrecta:** Se comparaba el valor numérico contra el texto

## Mapeo de Valores

### Base de Datos → Formulario
```php
$rango_edad_texto = [
    1 => "0 - 6",
    2 => "7 - 12", 
    3 => "13 - 17",
    4 => "18 - 28",
    5 => "29 - 45",
    6 => "46 - 64",
    7 => "Mayor o igual a 65"
];
```

## Solución Implementada

### ANTES (Incorrecto):
```php
<option value="0 - 6" <?php echo (($integrante['rango_integMovIndep'] ?? '') == '0 - 6') ? 'selected' : ''; ?>>0 - 6</option>
```
☝️ Comparaba el número (ej: 1) contra el texto ("0 - 6") → Nunca coincidía

### DESPUÉS (Correcto):
```php
<option value="0 - 6" <?php echo (($integrante['rango_integMovIndep_texto'] ?? '') == '0 - 6') ? 'selected' : ''; ?>>0 - 6</option>
```
☝️ Compara el texto convertido ("0 - 6") contra el texto ("0 - 6") → Coincide correctamente

## Proceso de Conversión

1. **Al cargar integrantes existentes** (líneas 67-73):
   ```php
   if (isset($integrante['rango_integMovIndep']) && is_numeric($integrante['rango_integMovIndep'])) {
       $integrante['rango_integMovIndep_texto'] = $rango_edad_texto[$integrante['rango_integMovIndep']] ?? '';
   } else {
       $integrante['rango_integMovIndep_texto'] = '';
   }
   ```

2. **En el formulario** (líneas 686-692):
   - Se utiliza `$integrante['rango_integMovIndep_texto']` para las comparaciones
   - Esto asegura que el select muestre la opción correcta preseleccionada

## Resultado
✅ **Los rangos de edad ahora se muestran correctamente seleccionados** en integrantes existentes  
✅ **Mantiene compatibilidad** con nuevos integrantes (sin afectar la funcionalidad de creación)  
✅ **No requiere cambios en la base de datos** (sigue usando valores numéricos)

## Archivos de Verificación
- `test_rango_mapping.php` - Script de prueba para verificar el mapeo
- Utilizar para validar que la conversión funciona correctamente

## Notas Técnicas
- La conversión se realiza en PHP durante la carga de datos
- Los valores enviados al servidor siguen siendo texto (manteniendo compatibilidad con `updateMovimiento.php`)
- El mapping es bidireccional: se convierte para mostrar y se convierte de vuelta para guardar
