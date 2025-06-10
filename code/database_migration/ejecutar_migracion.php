<?php
// Script para migrar la tabla movimientos de estructura de contadores a registros individuales
// IMPORTANTE: Hacer backup antes de ejecutar

include("../../conexion.php");
header("Content-Type: text/html;charset=utf-8");

echo "<h2>🔄 Migración de Tabla Movimientos</h2>";
echo "<p><strong>⚠️ IMPORTANTE:</strong> Asegúrese de haber hecho un backup de la base de datos antes de continuar.</p>";

if (isset($_POST['ejecutar_migracion'])) {
    
    echo "<h3>📊 Iniciando migración...</h3>";
    
    try {
        // 1. Crear nueva tabla
        echo "<p>1. Creando nueva estructura de tabla...</p>";
        $sql_create = "
        CREATE TABLE movimientos_nuevo (
            id_movimiento int(11) NOT NULL AUTO_INCREMENT,
            doc_encVenta varchar(20) NOT NULL,
            tipo_movimiento varchar(100) NOT NULL,
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
        ";
        
        if ($mysqli->query($sql_create)) {
            echo "<p>✅ Tabla movimientos_nuevo creada exitosamente.</p>";
        } else {
            throw new Exception("Error creando tabla: " . $mysqli->error);
        }
        
        // 2. Verificar si queremos migrar datos existentes
        if (isset($_POST['migrar_datos'])) {
            echo "<p>2. Migrando datos existentes...</p>";
            
            // Verificar si existe la tabla antigua con datos
            $check_old = "SHOW TABLES LIKE 'movimientos'";
            $result_check = $mysqli->query($check_old);
            
            if ($result_check && $result_check->num_rows > 0) {
                echo "<p>📋 Tabla movimientos anterior encontrada. Iniciando migración de datos...</p>";
                
                // Obtener datos de la tabla antigua
                $sql_old_data = "SELECT * FROM movimientos";
                $result_old = $mysqli->query($sql_old_data);
                
                if ($result_old) {
                    $migrated_count = 0;
                    
                    while ($row = $result_old->fetch_assoc()) {
                        $doc = $mysqli->real_escape_string($row['doc_encVenta'] ?? '');
                        $obs = $mysqli->real_escape_string($row['observacion'] ?? '');
                        $id_usu = $row['id_usu'] ?? 0;
                        $id_encuesta = $row['id_encuesta'] ?? null;
                        $id_informacion = $row['id_informacion'] ?? null;
                        
                        // Migrar cada tipo de movimiento como registros separados
                        $tipos_movimientos = [
                            'inclusion' => $row['inclusion'] ?? 0,
                            'Inconformidad por clasificacion' => $row['inconfor_clasificacion'] ?? 0,
                            'modificación datos persona' => $row['datos_persona'] ?? 0,
                            'Retiro ficha' => $row['retiro_ficha'] ?? 0,
                            'Retiro personas' => $row['retiro_personas'] ?? 0,
                        ];
                        
                        foreach ($tipos_movimientos as $tipo => $cantidad) {
                            if ($cantidad > 0) {
                                // Crear un registro por cada ocurrencia
                                for ($i = 0; $i < $cantidad; $i++) {
                                    $sql_insert = "INSERT INTO movimientos_nuevo 
                                        (doc_encVenta, tipo_movimiento, fecha_movimiento, observacion, id_usu, id_encuesta, id_informacion)
                                        VALUES 
                                        ('$doc', '$tipo', NOW(), '$obs', '$id_usu', " . 
                                        ($id_encuesta ? "'$id_encuesta'" : "NULL") . ", " . 
                                        ($id_informacion ? "'$id_informacion'" : "NULL") . ")";
                                    
                                    if ($mysqli->query($sql_insert)) {
                                        $migrated_count++;
                                    }
                                }
                            }
                        }
                    }
                    
                    echo "<p>✅ Migrados $migrated_count registros exitosamente.</p>";
                } else {
                    echo "<p>⚠️ No se pudieron obtener datos de la tabla anterior.</p>";
                }
            }
        } else {
            echo "<p>2. Omitiendo migración de datos existentes (tabla nueva vacía).</p>";
        }
        
        // 3. Preguntar si renombrar tablas
        if (isset($_POST['finalizar_migracion'])) {
            echo "<p>3. Finalizando migración...</p>";
            
            // Renombrar tabla antigua como respaldo
            $sql_backup = "RENAME TABLE movimientos TO movimientos_backup_" . date('Y_m_d_H_i_s');
            if ($mysqli->query($sql_backup)) {
                echo "<p>✅ Tabla anterior respaldada como movimientos_backup_" . date('Y_m_d_H_i_s') . "</p>";
                
                // Renombrar nueva tabla
                $sql_rename = "RENAME TABLE movimientos_nuevo TO movimientos";
                if ($mysqli->query($sql_rename)) {
                    echo "<p>✅ Nueva tabla activada como 'movimientos'.</p>";
                    echo "<h3>🎉 ¡Migración completada exitosamente!</h3>";
                    echo "<p><strong>Nota:</strong> La tabla anterior se mantiene como respaldo.</p>";
                } else {
                    throw new Exception("Error renombrando tabla nueva: " . $mysqli->error);
                }
            } else {
                throw new Exception("Error respaldando tabla anterior: " . $mysqli->error);
            }
        }
        
    } catch (Exception $e) {
        echo "<div style='color: red;'>";
        echo "<h3>❌ Error durante la migración:</h3>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<p><strong>La migración se ha detenido. Revise el error y vuelva a intentar.</strong></p>";
        echo "</div>";
    }
}

// Verificar estado actual
echo "<h3>📋 Estado Actual</h3>";

// Verificar si existe tabla movimientos
$check_movimientos = "SHOW TABLES LIKE 'movimientos'";
$result_mov = $mysqli->query($check_movimientos);

if ($result_mov && $result_mov->num_rows > 0) {
    // Verificar estructura actual
    $desc_mov = "DESCRIBE movimientos";
    $result_desc = $mysqli->query($desc_mov);
    
    echo "<p><strong>Tabla movimientos encontrada. Estructura actual:</strong></p>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
    
    $has_new_structure = false;
    while ($field = $result_desc->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $field['Field'] . "</td>";
        echo "<td>" . $field['Type'] . "</td>";
        echo "<td>" . $field['Null'] . "</td>";
        echo "<td>" . $field['Key'] . "</td>";
        echo "</tr>";
        
        if ($field['Field'] == 'tipo_movimiento') {
            $has_new_structure = true;
        }
    }
    echo "</table>";
    
    if ($has_new_structure) {
        echo "<p style='color: green;'>✅ <strong>La tabla ya tiene la nueva estructura individual.</strong></p>";
        echo "<p>No es necesario ejecutar la migración.</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ <strong>La tabla tiene la estructura antigua (contadores).</strong></p>";
        echo "<p>Se recomienda ejecutar la migración para actualizar a la nueva estructura.</p>";
    }
} else {
    echo "<p style='color: red;'>❌ <strong>Tabla movimientos no encontrada.</strong></p>";
}

// Verificar si existe tabla movimientos_nuevo
$check_nuevo = "SHOW TABLES LIKE 'movimientos_nuevo'";
$result_nuevo = $mysqli->query($check_nuevo);

if ($result_nuevo && $result_nuevo->num_rows > 0) {
    echo "<p style='color: blue;'>ℹ️ <strong>Tabla movimientos_nuevo ya existe.</strong></p>";
    echo "<p>Elimine la tabla movimientos_nuevo si desea volver a ejecutar la migración.</p>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Migración Tabla Movimientos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
        .warning { background-color: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .success { background-color: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        button { padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-warning { background-color: #ffc107; color: black; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
    </style>
</head>
<body>

<?php if (!isset($_POST['ejecutar_migracion']) && !$has_new_structure): ?>
<div class="form-section">
    <h3>🚀 Ejecutar Migración</h3>
    <div class="warning">
        <strong>⚠️ ADVERTENCIA:</strong> Esta operación modificará la estructura de la base de datos.
        Asegúrese de haber hecho un backup completo antes de continuar.
    </div>
    
    <form method="POST">
        <p>
            <label>
                <input type="checkbox" name="migrar_datos" value="1" checked>
                Migrar datos existentes de la tabla actual
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="finalizar_migracion" value="1">
                Finalizar migración (renombrar tablas)
            </label>
        </p>
        <p>
            <button type="submit" name="ejecutar_migracion" value="1" class="btn-warning" 
                    onclick="return confirm('¿Está seguro de ejecutar la migración? Asegúrese de tener un backup.')">
                Ejecutar Migración
            </button>
        </p>
    </form>
</div>
<?php endif; ?>

<div class="form-section">
    <h3>🔙 Acciones</h3>
    <a href="../../access.php">
        <button class="btn-success">Volver al Sistema</button>
    </a>
    <a href="test_nueva_estructura.php">
        <button class="btn-success">Probar Nueva Estructura</button>
    </a>
</div>

</body>
</html>
