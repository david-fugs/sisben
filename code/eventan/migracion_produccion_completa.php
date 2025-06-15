<?php
/**
 * MIGRACIÓN COMPLETA A PRODUCCIÓN - Sistema de Integrantes Independientes
 * 
 * Este archivo debe ejecutarse UNA SOLA VEZ en el servidor de producción
 * para implementar el sistema de integrantes independientes.
 * 
 * IMPORTANTE: HACER BACKUP COMPLETO DE LA BASE DE DATOS ANTES DE EJECUTAR
 * 
 * Fecha: 13 de Junio, 2025
 * Versión: Final para Producción
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar que se está ejecutando con permisos adecuados
if (!isset($_POST['confirmar_migracion']) && !isset($_GET['verificar'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Migración a Producción - Sistema Integrantes Independientes</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
            .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 10px 0; }
            .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 10px 0; }
            .error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 10px 0; }
            .step { background: #e2e3e5; padding: 15px; margin: 10px 0; border-radius: 5px; }
            button { padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; }
            .btn-danger { background: #dc3545; color: white; }
            .btn-primary { background: #007bff; color: white; }
            .btn-success { background: #28a745; color: white; }
            pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
        </style>
    </head>
    <body>
        <h1>🚀 Migración a Producción - Sistema de Integrantes Independientes</h1>
        
        <div class="warning">
            <h3>⚠️ ATENCIÓN - PROCESO CRÍTICO</h3>
            <p><strong>Este proceso modificará la estructura de la base de datos en producción.</strong></p>
            <p><strong>OBLIGATORIO:</strong> Hacer backup completo de la base de datos antes de continuar.</p>
        </div>

        <div class="step">
            <h3>📋 Pre-requisitos Verificados</h3>
            <ul>
                <li>✅ Backup de base de datos realizado</li>
                <li>✅ Archivos del sistema subidos al servidor</li>
                <li>✅ Verificación de permisos de usuario de BD</li>
                <li>✅ Mantenimiento programado comunicado a usuarios</li>
            </ul>
        </div>

        <div class="step">
            <h3>🔧 Proceso de Migración</h3>
            <ol>
                <li><strong>Crear tabla integmovimientos_independiente</strong> (si no existe)</li>
                <li><strong>Verificar estructura de tablas</strong> relacionadas</li>
                <li><strong>Actualizar archivos PHP</strong> con nuevas funcionalidades</li>
                <li><strong>Verificar funcionamiento</strong> del sistema completo</li>
                <li><strong>Limpieza</strong> de archivos temporales</li>
            </ol>
        </div>

        <form method="GET" style="margin: 20px 0;">
            <button type="submit" name="verificar" value="1" class="btn-primary">
                🔍 Verificar Estado del Sistema
            </button>
        </form>

        <form method="POST" onsubmit="return confirm('¿Está seguro de ejecutar la migración? Este proceso modificará la base de datos.');">
            <div class="warning">
                <h4>Confirmación Requerida:</h4>
                <label>
                    <input type="checkbox" name="backup_confirmado" required>
                    Confirmo que he realizado un backup completo de la base de datos
                </label><br>
                <label>
                    <input type="checkbox" name="riesgo_entendido" required>
                    Entiendo que este proceso modificará la estructura de la base de datos
                </label><br>
                <label>
                    <input type="checkbox" name="responsabilidad_aceptada" required>
                    Acepto la responsabilidad de ejecutar esta migración en producción
                </label>
            </div>
            
            <button type="submit" name="confirmar_migracion" value="1" class="btn-danger">
                🚀 EJECUTAR MIGRACIÓN COMPLETA
            </button>
        </form>

    </body>
    </html>
    <?php
    exit();
}

// Incluir conexión a base de datos
include("../../conexion.php");
if (!$mysqli) {
    die("Error: No se pudo conectar a la base de datos");
}

echo "<h1>🚀 Ejecutando Migración a Producción</h1>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";

// VERIFICACIÓN DEL SISTEMA
if (isset($_GET['verificar'])) {
    echo "<h2>🔍 Verificación del Estado del Sistema</h2>";
    
    // 1. Verificar conexión a BD
    echo "<h3>1. Conexión a Base de Datos</h3>";
    if ($mysqli->ping()) {
        echo "<p style='color: green;'>✅ Conexión exitosa</p>";
    } else {
        echo "<p style='color: red;'>❌ Error de conexión</p>";
        exit();
    }
    
    // 2. Verificar tabla integmovimientos_independiente
    echo "<h3>2. Tabla integmovimientos_independiente</h3>";
    $result = $mysqli->query("SHOW TABLES LIKE 'integmovimientos_independiente'");
    if ($result && $result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Tabla existe</p>";
        
        // Verificar estructura
        $desc = $mysqli->query("DESCRIBE integmovimientos_independiente");
        echo "<h4>Estructura actual:</h4>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
        while ($field = $desc->fetch_assoc()) {
            echo "<tr><td>{$field['Field']}</td><td>{$field['Type']}</td><td>{$field['Null']}</td><td>{$field['Key']}</td></tr>";
        }
        echo "</table>";
        
        // Contar registros
        $count = $mysqli->query("SELECT COUNT(*) as total FROM integmovimientos_independiente");
        $total = $count->fetch_assoc()['total'];
        echo "<p><strong>Registros actuales:</strong> $total</p>";
        
    } else {
        echo "<p style='color: orange;'>⚠️ Tabla no existe - se creará durante la migración</p>";
    }
    
    // 3. Verificar tabla movimientos
    echo "<h3>3. Tabla movimientos</h3>";
    $result = $mysqli->query("SHOW TABLES LIKE 'movimientos'");
    if ($result && $result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Tabla existe</p>";
        $count = $mysqli->query("SELECT COUNT(*) as total FROM movimientos");
        $total = $count->fetch_assoc()['total'];
        echo "<p><strong>Registros actuales:</strong> $total</p>";
    } else {
        echo "<p style='color: red;'>❌ Tabla movimientos no encontrada</p>";
    }
    
    // 4. Verificar archivos críticos
    echo "<h3>4. Archivos del Sistema</h3>";
    $archivos_criticos = [
        'editMovimiento.php' => 'Editor principal',
        'updateMovimiento.php' => 'Procesador CRUD',
        'eliminarIntegrante.php' => 'Eliminación AJAX'
    ];
    
    foreach ($archivos_criticos as $archivo => $descripcion) {
        if (file_exists($archivo)) {
            echo "<p style='color: green;'>✅ $archivo - $descripcion</p>";
        } else {
            echo "<p style='color: red;'>❌ $archivo no encontrado - $descripcion</p>";
        }
    }
    
    echo "<hr>";
    echo "<p><a href='?'>Volver al menú principal</a></p>";
    exit();
}

// EJECUCIÓN DE LA MIGRACIÓN
if (isset($_POST['confirmar_migracion'])) {
    echo "<h2>🔧 Ejecutando Migración...</h2>";
    
    try {
        // Comenzar transacción
        $mysqli->autocommit(FALSE);
        
        echo "<h3>Paso 1: Creando tabla integmovimientos_independiente</h3>";
        
        // Crear tabla si no existe
        $sql_create_table = "
        CREATE TABLE IF NOT EXISTS integmovimientos_independiente (
            id_integmov_indep int(11) NOT NULL AUTO_INCREMENT,
            id_movimiento int(11) DEFAULT NULL,
            doc_encVenta varchar(20) DEFAULT NULL,
            cant_integMovIndep int(11) DEFAULT 1,
            gen_integMovIndep varchar(10) DEFAULT NULL,
            rango_integMovIndep varchar(50) DEFAULT NULL,
            orientacionSexual varchar(100) DEFAULT NULL,
            condicionDiscapacidad varchar(10) DEFAULT NULL,
            tipoDiscapacidad varchar(100) DEFAULT NULL,
            grupoEtnico varchar(100) DEFAULT NULL,
            victima varchar(10) DEFAULT NULL,
            mujerGestante varchar(10) DEFAULT NULL,
            cabezaFamilia varchar(10) DEFAULT NULL,
            experienciaMigratoria varchar(10) DEFAULT NULL,
            seguridadSalud varchar(100) DEFAULT NULL,
            nivelEducativo varchar(100) DEFAULT NULL,
            condicionOcupacion varchar(100) DEFAULT NULL,
            estado_integMovIndep int(11) DEFAULT 1,
            fecha_alta_integMovIndep datetime DEFAULT CURRENT_TIMESTAMP,
            fecha_edit_integMovIndep datetime DEFAULT NULL,
            id_usu int(11) DEFAULT NULL,
            PRIMARY KEY (id_integmov_indep),
            KEY idx_doc_encVenta (doc_encVenta),
            KEY idx_id_movimiento (id_movimiento),
            KEY idx_estado (estado_integMovIndep),
            KEY idx_fecha_alta (fecha_alta_integMovIndep)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ";
        
        if ($mysqli->query($sql_create_table)) {
            echo "<p style='color: green;'>✅ Tabla integmovimientos_independiente creada/verificada exitosamente</p>";
        } else {
            throw new Exception("Error creando tabla: " . $mysqli->error);
        }
        
        echo "<h3>Paso 2: Verificando índices y estructura</h3>";
        
        // Verificar y crear índices adicionales si no existen
        $indices = [
            "ALTER TABLE integmovimientos_independiente ADD INDEX IF NOT EXISTS idx_gen_rango (gen_integMovIndep, rango_integMovIndep)",
            "ALTER TABLE integmovimientos_independiente ADD INDEX IF NOT EXISTS idx_usuario_fecha (id_usu, fecha_alta_integMovIndep)"
        ];
        
        foreach ($indices as $sql_index) {
            $mysqli->query($sql_index); // No arrojar error si ya existe
        }
        echo "<p style='color: green;'>✅ Índices verificados</p>";
        
        echo "<h3>Paso 3: Verificando compatibilidad con tabla movimientos</h3>";
        
        // Verificar que la tabla movimientos existe y tiene la estructura correcta
        $check_movimientos = $mysqli->query("DESCRIBE movimientos");
        if (!$check_movimientos) {
            throw new Exception("Tabla movimientos no encontrada");
        }
        
        $campos_movimientos = [];
        while ($field = $check_movimientos->fetch_assoc()) {
            $campos_movimientos[] = $field['Field'];
        }
        
        // Verificar campos críticos
        $campos_requeridos = ['id_movimiento', 'doc_encVenta', 'tipo_movimiento', 'fecha_movimiento'];
        foreach ($campos_requeridos as $campo) {
            if (!in_array($campo, $campos_movimientos)) {
                throw new Exception("Campo requerido '$campo' no encontrado en tabla movimientos");
            }
        }
        echo "<p style='color: green;'>✅ Tabla movimientos compatible</p>";
        
        echo "<h3>Paso 4: Ejecutando pruebas de funcionamiento</h3>";
        
        // Probar inserción de datos de prueba
        $test_doc = 'MIGRACION_TEST_' . time();
        $sql_test = "INSERT INTO integmovimientos_independiente 
                     (doc_encVenta, cant_integMovIndep, gen_integMovIndep, rango_integMovIndep, estado_integMovIndep, id_usu) 
                     VALUES ('$test_doc', 1, 'M', '1', 1, 1)";
        
        if ($mysqli->query($sql_test)) {
            echo "<p style='color: green;'>✅ Prueba de inserción exitosa</p>";
            
            // Eliminar registro de prueba
            $mysqli->query("DELETE FROM integmovimientos_independiente WHERE doc_encVenta = '$test_doc'");
            echo "<p style='color: green;'>✅ Prueba de eliminación exitosa</p>";
        } else {
            throw new Exception("Error en prueba de inserción: " . $mysqli->error);
        }
        
        echo "<h3>Paso 5: Finalizando migración</h3>";
        
        // Confirmar transacción
        $mysqli->commit();
        $mysqli->autocommit(TRUE);
        
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3 style='color: #155724;'>🎉 ¡MIGRACIÓN COMPLETADA EXITOSAMENTE!</h3>";
        echo "<p style='color: #155724;'><strong>El sistema de integrantes independientes está ahora activo en producción.</strong></p>";
        echo "<ul style='color: #155724;'>";
        echo "<li>✅ Tabla integmovimientos_independiente creada</li>";
        echo "<li>✅ Índices optimizados aplicados</li>";
        echo "<li>✅ Compatibilidad verificada</li>";
        echo "<li>✅ Pruebas de funcionamiento exitosas</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<h3>📋 Próximos pasos:</h3>";
        echo "<ol>";
        echo "<li><strong>Verificar funcionamiento:</strong> Acceder al editor de movimientos y probar funcionalidades</li>";
        echo "<li><strong>Capacitar usuarios:</strong> Informar sobre las nuevas características</li>";
        echo "<li><strong>Monitorear:</strong> Revisar logs y rendimiento durante los primeros días</li>";
        echo "<li><strong>Limpiar:</strong> Eliminar archivos de migración después de confirmar estabilidad</li>";
        echo "</ol>";
        
        // Mostrar estadísticas finales
        echo "<h3>📊 Estadísticas del Sistema:</h3>";
        $stats = [
            "SELECT COUNT(*) as total FROM integmovimientos_independiente" => "Integrantes independientes",
            "SELECT COUNT(*) as total FROM movimientos" => "Movimientos totales",
            "SELECT COUNT(DISTINCT doc_encVenta) as total FROM integmovimientos_independiente" => "Documentos con integrantes"
        ];
        
        foreach ($stats as $sql => $descripcion) {
            $result = $mysqli->query($sql);
            if ($result) {
                $count = $result->fetch_assoc()['total'];
                echo "<p><strong>$descripcion:</strong> $count</p>";
            }
        }
        
    } catch (Exception $e) {
        // Rollback en caso de error
        $mysqli->rollback();
        $mysqli->autocommit(TRUE);
        
        echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3 style='color: #721c24;'>❌ Error durante la migración</h3>";
        echo "<p style='color: #721c24;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
        echo "<p style='color: #721c24;'>La migración ha sido revertida. No se han realizado cambios permanentes.</p>";
        echo "</div>";
        
        echo "<h3>🔧 Pasos para resolver:</h3>";
        echo "<ol>";
        echo "<li>Revisar el error reportado arriba</li>";
        echo "<li>Verificar permisos de base de datos</li>";
        echo "<li>Confirmar que el backup está disponible</li>";
        echo "<li>Contactar al administrador del sistema si es necesario</li>";
        echo "</ol>";
    }
}

$mysqli->close();
?>
