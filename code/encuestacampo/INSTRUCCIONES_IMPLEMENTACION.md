# Implementación de Campo EDAD y Gestión de Fotos
## Sistema SISBEN - Encuesta de Campo

---

## ✅ CAMBIOS IMPLEMENTADOS

### 1. Campo EDAD Calculado
Se agregó un campo que calcula automáticamente la edad basándose en la fecha de nacimiento:

#### Archivos modificados:
- **encuesta_campo.php**: 
  - Campo "EDAD" agregado junto a "Fecha de Nacimiento"
  - JavaScript que calcula la edad automáticamente
  - Campo de solo lectura (no se envía al servidor)

- **editEncuesta.php**:
  - Campo "EDAD" agregado junto a "Fecha de Nacimiento"
  - JavaScript que calcula la edad al cargar y al cambiar la fecha
  - Campo de solo lectura (no se envía al servidor)

**Funcionamiento:**
- Al seleccionar/cambiar la fecha de nacimiento, automáticamente se calcula y muestra la edad
- El cálculo considera si ya cumplió años en el año actual
- Es solo informativo, NO se guarda en la base de datos

---

### 2. Sistema de Gestión de Fotos

#### 📁 Estructura de Carpetas
Se creó la carpeta: `c:\xampp\htdocs\sisben\documentos\`
- Las fotos se organizan por número de documento: `documentos/{numero_documento}/foto_encuesta_{id}.jpg`

#### Archivos modificados:

**A. encuesta_campo.php** (Crear nueva encuesta):
- ✅ Sección "Fotografía del Encuestado" agregada
- ✅ Input de tipo file con atributo `capture="camera"` (permite usar cámara en móviles)
- ✅ Vista previa de la imagen antes de guardar
- ✅ Acepta: JPG, JPEG, PNG, GIF

**B. processsurvey.php** (Procesar nueva encuesta):
- ✅ Procesamiento de foto después de insertar encuesta
- ✅ Validación de extensiones permitidas
- ✅ Creación automática de carpeta por número de documento
- ✅ Nombre del archivo: `foto_encuesta_{id_encuesta}.{extension}`
- ✅ Actualización de BD con la ruta de la foto

**C. editEncuesta.php** (Editar encuesta):
- ✅ Sección "Fotografía del Encuestado" agregada
- ✅ Muestra foto actual si existe
- ✅ Botón "Descargar" para obtener la foto
- ✅ Botón "Eliminar" para marcar foto para eliminación
- ✅ Input para subir/tomar nueva foto
- ✅ Vista previa al seleccionar nueva imagen

**D. updatesurvey.php** (Actualizar encuesta):
- ✅ Procesamiento de eliminación de foto
- ✅ Procesamiento de actualización de foto
- ✅ Elimina foto anterior al subir nueva
- ✅ Actualiza BD con nueva ruta

---

## 🗄️ CAMBIOS EN BASE DE DATOS

### Script SQL a Ejecutar:
Se creó el archivo: **agregar_columna_foto.sql**

```sql
ALTER TABLE `encuestacampo` 
ADD COLUMN IF NOT EXISTS `foto_encuestado` VARCHAR(255) NULL DEFAULT NULL 
COMMENT 'Ruta de la foto del encuestado' 
AFTER `obs_encVenta`;
```

**⚠️ IMPORTANTE:** Debes ejecutar este script en la base de datos antes de usar el sistema.

### Opciones para ejecutar:
1. **phpMyAdmin**: 
   - Abre phpMyAdmin (http://localhost/phpmyadmin)
   - Selecciona la base de datos `sisben`
   - Ve a la pestaña "SQL"
   - Pega el contenido del archivo `agregar_columna_foto.sql`
   - Click en "Continuar"

2. **Línea de comandos**:
   ```bash
   mysql -u root -p sisben < "c:\xampp\htdocs\sisben\code\encuestacampo\agregar_columna_foto.sql"
   ```

3. **Desde el navegador** (si tienes acceso a ejecutar consultas):
   ```
   ALTER TABLE encuestacampo ADD COLUMN foto_encuestado VARCHAR(255) NULL DEFAULT NULL;
   ```

---

## 🎯 FUNCIONALIDADES IMPLEMENTADAS

### En Crear Encuesta (encuesta_campo.php):
1. ✅ Campo EDAD se calcula automáticamente
2. ✅ Puede tomar foto con la cámara del dispositivo
3. ✅ Puede subir una foto existente
4. ✅ Vista previa de la foto antes de guardar
5. ✅ Foto es opcional (no obligatoria)

### En Editar Encuesta (editEncuesta.php):
1. ✅ Campo EDAD se calcula automáticamente al cargar
2. ✅ Muestra la foto actual si existe
3. ✅ Permite descargar la foto actual
4. ✅ Permite eliminar la foto actual
5. ✅ Permite tomar/subir una nueva foto
6. ✅ Al subir nueva foto, se reemplaza la anterior
7. ✅ Vista previa de nueva foto antes de guardar

---

## 📝 DECISIÓN DE DISEÑO

### ¿Por qué usar número de documento en lugar de ID de encuesta?

Se decidió usar el **número de documento** para organizar las fotos por las siguientes razones:

✅ **Ventajas:**
1. **Unicidad por persona**: Una persona puede tener múltiples encuestas, pero un solo documento
2. **Fácil búsqueda**: Los operadores buscan por documento, no por ID de encuesta
3. **Organización lógica**: Todas las fotos de una persona en una sola carpeta
4. **Facilita auditorías**: Verificar fotos por persona es más intuitivo
5. **Migración de datos**: Si se migra el sistema, el documento permanece constante

**Estructura resultante:**
```
documentos/
├── 12345678/
│   ├── foto_encuesta_1.jpg
│   ├── foto_encuesta_5.jpg
│   └── foto_encuesta_12.jpg
├── 87654321/
│   └── foto_encuesta_2.jpg
└── ...
```

---

## 🔧 TESTING RECOMENDADO

### Pruebas a realizar:

1. **Crear nueva encuesta:**
   - [ ] Seleccionar fecha de nacimiento → verificar que se calcula la edad
   - [ ] Tomar foto con cámara → verificar vista previa
   - [ ] Subir foto desde archivo → verificar vista previa
   - [ ] Guardar encuesta con foto → verificar que se guarda correctamente
   - [ ] Verificar que se crea la carpeta `documentos/{numero_documento}/`

2. **Editar encuesta existente:**
   - [ ] Abrir encuesta con foto → verificar que se muestra
   - [ ] Descargar foto → verificar descarga
   - [ ] Eliminar foto → guardar → verificar que se elimina del servidor
   - [ ] Subir nueva foto → verificar que reemplaza la anterior
   - [ ] Cambiar fecha nacimiento → verificar recálculo de edad

3. **Validaciones:**
   - [ ] Intentar subir archivo no permitido (ej: .txt) → debe rechazar
   - [ ] Foto muy grande → verificar funcionamiento
   - [ ] Sin foto → verificar que el formulario funciona normalmente

---

## 📱 COMPATIBILIDAD MÓVIL

El atributo `capture="camera"` en el input file permite:
- En dispositivos móviles: Abre directamente la cámara
- En desktop: Funciona como selector de archivos normal
- Compatible con Android e iOS

---

## ⚠️ CONSIDERACIONES DE SEGURIDAD

1. **Validación de extensiones**: Solo se permiten JPG, JPEG, PNG, GIF
2. **Escape de datos**: Todos los valores se escapan con `mysqli_real_escape_string`
3. **Permisos de carpeta**: La carpeta `documentos` tiene permisos 0777 (ajustar según necesidad)
4. **Tamaño de archivo**: No hay límite configurado, considerar agregar validación de tamaño máximo

---

## 🚀 PRÓXIMOS PASOS

1. ✅ Ejecutar el script SQL para agregar la columna `foto_encuestado`
2. ✅ Verificar permisos de escritura en la carpeta `documentos`
3. ✅ Realizar pruebas en entorno de desarrollo
4. ✅ Ajustar estilos CSS si es necesario
5. ✅ Considerar agregar validación de tamaño máximo de archivo (opcional)

---

## 📞 SOPORTE

Si encuentras algún problema:
1. Verifica que la columna `foto_encuestado` existe en la BD
2. Verifica permisos de la carpeta `documentos`
3. Revisa el log de errores de PHP: `c:\xampp\apache\logs\error.log`
4. Verifica la consola del navegador para errores JavaScript

---

**✨ Implementación completada exitosamente!**
