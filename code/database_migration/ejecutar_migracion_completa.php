<?php
// Script para migrar a la nueva estructura COMPLETA de movimientos
// IMPORTANTE: Hacer backup antes de ejecutar

include("../../conexion.php");
header("Content-Type: text/html;charset=utf-8");

echo "<h2>🔄 Migración COMPLETA de Tabla Movimientos</h2>";
echo "<p><strong>⚠️ IMPORTANTE:</strong> Esta migración hará que movimientos sea completamente independiente de encventanilla.</p>";

if (isset($_POST['ejecutar_migracion_completa'])) {
    
    echo "<h3>📊 Iniciando migración completa...</h3>";
    
    try {
        // 1. Crear nueva tabla con estructura completa
        echo "<p>1. Creando nueva estructura completa de tabla...</p>";
        $sql_create = "
        CREATE TABLE movimientos_completo (
            id_movimiento int(11) NOT NULL AUTO_INCREMENT,
            
            -- Campos básicos de movimiento
            doc_encVenta varchar(20) NOT NULL,
            tipo_movimiento varchar(100) NOT NULL,
            fecha_movimiento datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            observacion text,
            id_usu int(11) NOT NULL,
            
            -- Todos los campos de encventanilla
            fec_reg_encVenta date DEFAULT NULL,
            nom_encVenta varchar(100) DEFAULT NULL,
            dir_encVenta varchar(200) DEFAULT NULL,
            zona_encVenta varchar(50) DEFAULT NULL,
            id_com int(11) DEFAULT NULL,
            id_bar int(11) DEFAULT NULL,
            otro_bar_ver_encVenta varchar(100) DEFAULT NULL,
            tram_solic_encVenta varchar(100) DEFAULT NULL,
            integra_encVenta int(11) DEFAULT NULL,
            num_ficha_encVenta varchar(50) DEFAULT NULL,
            obs_encVenta text,
            tipo_documento varchar(20) DEFAULT NULL,
            fecha_expedicion date DEFAULT NULL,
            departamento_expedicion varchar(10) DEFAULT NULL,
            ciudad_expedicion varchar(10) DEFAULT NULL,
            sisben_nocturno varchar(10) DEFAULT NULL,
            estado_ficha int(1) DEFAULT 1,
            
            -- Campos de control
            fecha_alta_movimiento datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            fecha_edit_movimiento datetime DEFAULT NULL,
            
            -- Relaciones opcionales
            id_encuesta int(11) DEFAULT NULL,
            id_informacion int(11) DEFAULT NULL,
            
            PRIMARY KEY (id_movimiento),
            KEY idx_doc_encVenta (doc_encVenta),
            KEY idx_fecha_movimiento (fecha_movimiento),
            KEY idx_tipo_movimiento (tipo_movimiento),
            KEY idx_id_usu (id_usu),
            KEY idx_fec_reg_encVenta (fec_reg_encVenta),
            KEY idx_num_ficha_encVenta (num_ficha_encVenta)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";
        
        if ($mysqli->query($sql_create)) {
            echo "<p>✅ Tabla movimientos_completo creada exitosamente.</p>";
        } else {
            throw new Exception("Error creando tabla: " . $mysqli->error);
        }
        
        // 2. Migrar datos de encventanilla
        echo "<p>2. Migrando datos de encventanilla...</p>";
        
        $sql_migrate = "
        INSERT INTO movimientos_completo 
        (doc_encVenta, tipo_movimiento, fecha_movimiento, observacion, id_usu,
         fec_reg_encVenta, nom_encVenta, dir_encVenta, zona_encVenta, id_com, id_bar,
         otro_bar_ver_encVenta, tram_solic_encVenta, integra_encVenta, num_ficha_encVenta,
         obs_encVenta, tipo_documento, fecha_expedicion, departamento_expedicion,
         ciudad_expedicion, sisben_nocturno, estado_ficha, fecha_alta_movimiento,
         fecha_edit_movimiento, id_encuesta)
        SELECT 
            doc_encVenta,
            COALESCE(tram_solic_encVenta, 'ENCUESTA INICIAL') as tipo_movimiento,
            COALESCE(fecha_alta_encVenta, NOW()) as fecha_movimiento,
            obs_encVenta as observacion,
            id_usu,
            fec_reg_encVenta,
            nom_encVenta,
            dir_encVenta,
            zona_encVenta,
            id_com,
            id_bar,
            otro_bar_ver_encVenta,
            tram_solic_encVenta,
            integra_encVenta,
            num_ficha_encVenta,
            obs_encVenta,
            tipo_documento,
            fecha_expedicion,
            departamento_expedicion,
            ciudad_expedicion,
            sisben_nocturno,
            COALESCE(estado_ficha, 1),
            COALESCE(fecha_alta_encVenta, NOW()),
            fecha_edit_encVenta,
            id_encVenta
        FROM encventanilla";
        
        if ($mysqli->query($sql_migrate)) {
            $migrated_count = $mysqli->affected_rows;
            echo "<p>✅ Migrados $migrated_count registros de encventanilla.</p>";
        } else {
            throw new Exception("Error migrando datos: " . $mysqli->error);
        }
        
        // 3. Finalizar migración si se solicita
        if (isset($_POST['finalizar_migracion_completa'])) {
            echo "<p>3. Finalizando migración...</p>";
            
            // Renombrar tabla antigua como respaldo
            $sql_backup = "RENAME TABLE movimientos TO movimientos_backup_" . date('Y_m_d_H_i_s');
            if ($mysqli->query($sql_backup)) {
                echo "<p>✅ Tabla anterior respaldada.</p>";
                
                // Renombrar nueva tabla
                $sql_rename = "RENAME TABLE movimientos_completo TO movimientos";
                if ($mysqli->query($sql_rename)) {
                    echo "<p>✅ Nueva tabla activada como 'movimientos'.</p>";
                    echo "<h3>🎉 ¡Migración COMPLETA exitosa!</h3>";
                    echo "<p><strong>Nota:</strong> Ahora movimientos es completamente independiente de encventanilla.</p>";
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
        echo "</div>";
    }
}

// Verificar estado actual
echo "<h3>📋 Estado Actual</h3>";

// Verificar si existe tabla movimientos
$check_movimientos = "SHOW TABLES LIKE 'movimientos'";
$result_mov = $mysqli->query($check_movimientos);

if ($result_mov && $result_mov->num_rows > 0) {
    $desc_mov = "DESCRIBE movimientos";
    $result_desc = $mysqli->query($desc_mov);
    
    echo "<p><strong>Tabla movimientos encontrada. Estructura actual:</strong></p>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
    echo "<tr><th>Campo</th><th>Tipo</th></tr>";
    
    $has_complete_structure = false;
    while ($field = $result_desc->fetch_assoc()) {
        echo "<tr><td>" . $field['Field'] . "</td><td>" . $field['Type'] . "</td></tr>";
        
        if ($field['Field'] == 'nom_encVenta') {
            $has_complete_structure = true;
        }
    }
    echo "</table>";
    
    if ($has_complete_structure) {
        echo "<p style='color: green;'>✅ <strong>La tabla ya tiene la estructura completa independiente.</strong></p>";
    } else {
        echo "<p style='color: orange;'>⚠️ <strong>La tabla necesita migración a estructura completa.</strong></p>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Migración COMPLETA Tabla Movimientos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
        .warning { background-color: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
        button { padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-warning { background-color: #ffc107; color: black; }
        .btn-success { background-color: #28a745; color: white; }
    </style>
</head>
<body>

<?php if (!isset($_POST['ejecutar_migracion_completa'])): ?>
<div class="form-section">
    <h3>🚀 Ejecutar Migración COMPLETA</h3>
    <div class="warning">
        <strong>⚠️ ADVERTENCIA:</strong> Esta migración hará movimientos completamente independiente de encventanilla.
        Asegúrese de haber hecho un backup completo antes de continuar.
    </div>
    
    <form method="POST">
        <p>
            <label>
                <input type="checkbox" name="finalizar_migracion_completa" value="1">
                Finalizar migración (activar nueva estructura)
            </label>
        </p>
        <p>
            <button type="submit" name="ejecutar_migracion_completa" value="1" class="btn-warning" 
                    onclick="return confirm('¿Está seguro de ejecutar la migración COMPLETA? Asegúrese de tener un backup.')">
                Ejecutar Migración COMPLETA
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
</div>

</body>
</html>
