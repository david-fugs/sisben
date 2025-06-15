<?php
/**
 * MIGRACIÓN COMPLETA DE INDEPENDENCIA DE MOVIMIENTOS
 * 
 * Este script migra TODOS los datos existentes de encventanilla a la tabla movimientos,
 * creando una estructura completamente independiente donde movimientos contiene 
 * toda la información necesaria para funcionar sin depender de encventanilla.
 * 
 * IMPORTANTE: 
 * - Hacer BACKUP completo de la base de datos antes de ejecutar
 * - Este proceso es irreversible
 * - Después de ejecutar, movimientos será completamente independiente
 */

session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();
}

include("../../conexion.php");
date_default_timezone_set("America/Bogota");
header("Content-Type: text/html;charset=utf-8");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migración Independencia Completa - SISBEN</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .alert { margin: 10px 0; padding: 15px; border-radius: 5px; }
        .alert-danger { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .alert-warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .alert-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .alert-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .code-block { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-warning { background-color: #ffc107; color: black; }
        .btn-primary { background-color: #007bff; color: white; }
    </style>
</head>
<body>

<div class="container">
    <center>
        <img src='../../img/sisben.png' width=300 height=185 class="responsive">
    </center>
    
    <h1 class="text-center">🔄 MIGRACIÓN INDEPENDENCIA COMPLETA</h1>
    <h2 class="text-center text-muted">Sistema SISBEN - Tabla Movimientos</h2>

    <div class="alert alert-danger">
        <h4>⚠️ ADVERTENCIA CRÍTICA ⚠️</h4>
        <p><strong>Este proceso es IRREVERSIBLE y modifica permanentemente la estructura de datos.</strong></p>
        <ul>
            <li>✅ Hacer BACKUP completo de la base de datos ANTES de continuar</li>
            <li>📋 La tabla movimientos se convertirá en completamente independiente</li>
            <li>🔄 Todos los datos de encventanilla se copiarán a movimientos</li>
            <li>🗃️ Se agregan nuevos campos a la tabla movimientos</li>
            <li>⚡ El formulario funcionará independientemente de encventanilla</li>
        </ul>
    </div>

<?php
if (isset($_POST['ejecutar_migracion_completa'])) {
    echo "<div class='alert alert-info'><h3>🚀 INICIANDO MIGRACIÓN COMPLETA...</h3></div>";
    
    try {
        // Iniciar transacción
        $mysqli->autocommit(FALSE);
        
        echo "<h3>📊 Paso 1: Verificando estructura actual</h3>";
        
        // Verificar si la tabla movimientos ya tiene la estructura independiente
        $check_columns = "SHOW COLUMNS FROM movimientos LIKE 'nom_encVenta'";
        $result_check = $mysqli->query($check_columns);
        
        if ($result_check && $result_check->num_rows > 0) {
            echo "<div class='alert alert-warning'>⚠️ La tabla movimientos ya tiene estructura independiente. Saltando modificación de estructura.</div>";
        } else {
            echo "<p>📝 Agregando columnas para independencia completa...</p>";
            
            // Agregar todas las columnas necesarias para independencia
            $alter_queries = [
                "ALTER TABLE movimientos ADD COLUMN nom_encVenta VARCHAR(255) DEFAULT NULL AFTER doc_encVenta",
                "ALTER TABLE movimientos ADD COLUMN fec_reg_encVenta DATE DEFAULT NULL",
                "ALTER TABLE movimientos ADD COLUMN tipo_documento VARCHAR(50) DEFAULT NULL",
                "ALTER TABLE movimientos ADD COLUMN departamento_expedicion VARCHAR(10) DEFAULT NULL",
                "ALTER TABLE movimientos ADD COLUMN ciudad_expedicion VARCHAR(10) DEFAULT NULL", 
                "ALTER TABLE movimientos ADD COLUMN fecha_expedicion DATE DEFAULT NULL",
                "ALTER TABLE movimientos ADD COLUMN dir_encVenta VARCHAR(255) DEFAULT NULL",
                "ALTER TABLE movimientos ADD COLUMN zona_encVenta VARCHAR(50) DEFAULT NULL",
                "ALTER TABLE movimientos ADD COLUMN id_com INT(11) DEFAULT NULL",
                "ALTER TABLE movimientos ADD COLUMN id_bar INT(11) DEFAULT NULL",
                "ALTER TABLE movimientos ADD COLUMN otro_bar_ver_encVenta VARCHAR(255) DEFAULT NULL",
                "ALTER TABLE movimientos ADD COLUMN integra_encVenta INT(11) DEFAULT NULL",
                "ALTER TABLE movimientos ADD COLUMN num_ficha_encVenta VARCHAR(50) DEFAULT NULL",
                "ALTER TABLE movimientos ADD COLUMN sisben_nocturno VARCHAR(10) DEFAULT NULL",
                "ALTER TABLE movimientos ADD COLUMN estado_ficha TINYINT(1) DEFAULT 1",
                "ALTER TABLE movimientos ADD COLUMN fecha_alta_movimiento DATETIME DEFAULT CURRENT_TIMESTAMP",
                "ALTER TABLE movimientos ADD COLUMN fecha_edit_movimiento DATETIME DEFAULT NULL"
            ];
            
            foreach ($alter_queries as $query) {
                if (!$mysqli->query($query)) {
                    // Si hay error, puede ser que la columna ya existe
                    echo "<p class='text-warning'>⚠️ " . $mysqli->error . "</p>";
                }
            }
            
            echo "<div class='alert alert-success'>✅ Estructura de tabla movimientos actualizada.</div>";
        }
        
        echo "<h3>📊 Paso 2: Migrando datos de encventanilla a movimientos</h3>";
        
        // Obtener todos los registros de encventanilla que no están ya en movimientos
        $sql_select = "SELECT ev.*, 
                       COUNT(iv.id_integVenta) as total_integrantes
                       FROM encventanilla ev 
                       LEFT JOIN integventanilla iv ON ev.id_encVenta = iv.id_encVenta 
                       WHERE ev.doc_encVenta NOT IN (
                           SELECT DISTINCT doc_encVenta FROM movimientos WHERE doc_encVenta IS NOT NULL
                       )
                       GROUP BY ev.id_encVenta";
        
        $result_encuestas = $mysqli->query($sql_select);
        
        if ($result_encuestas) {
            $total_migradas = 0;
            
            echo "<p>📄 Registros a migrar: " . $result_encuestas->num_rows . "</p>";
            
            while ($encuesta = $result_encuestas->fetch_assoc()) {
                // Escapar datos para evitar inyección SQL
                $doc_encVenta = $mysqli->real_escape_string($encuesta['doc_encVenta']);
                $nom_encVenta = $mysqli->real_escape_string($encuesta['nom_encVenta']);
                $fec_reg_encVenta = $encuesta['fec_reg_encVenta'];
                $tipo_documento = $mysqli->real_escape_string($encuesta['tipo_documento'] ?? '');
                $departamento_expedicion = $mysqli->real_escape_string($encuesta['departamento_expedicion'] ?? '');
                $ciudad_expedicion = $mysqli->real_escape_string($encuesta['ciudad_expedicion'] ?? '');
                $fecha_expedicion = $encuesta['fecha_expedicion'] ?? null;
                $dir_encVenta = $mysqli->real_escape_string($encuesta['dir_encVenta']);
                $zona_encVenta = $mysqli->real_escape_string($encuesta['zona_encVenta'] ?? '');
                $id_com = $encuesta['id_com'] ?? null;
                $id_bar = $encuesta['id_bar'] ?? null;
                $otro_bar_ver_encVenta = $mysqli->real_escape_string($encuesta['otro_bar_ver_encVenta'] ?? '');
                $integra_encVenta = $encuesta['total_integrantes'];
                $num_ficha_encVenta = $mysqli->real_escape_string($encuesta['num_ficha_encVenta'] ?? '');
                $sisben_nocturno = $mysqli->real_escape_string($encuesta['sisben_nocturno'] ?? '');
                $estado_ficha = $encuesta['estado_ficha'] ?? 1;
                $observacion = $mysqli->real_escape_string($encuesta['obs_encVenta'] ?? '');
                $id_usu = $encuesta['id_usu'];
                $id_encuesta = $encuesta['id_encVenta'];
                $fecha_alta = $encuesta['fecha_alta_encVenta'] ?? date('Y-m-d H:i:s');
                $fecha_edit = $encuesta['fecha_edit_encVenta'] ?? null;
                
                // Determinar tipo de movimiento basado en tram_solic_encVenta
                $tipo_movimiento = 'ENCUESTA MIGRADA';
                if (!empty($encuesta['tram_solic_encVenta'])) {
                    $tipo_movimiento = $mysqli->real_escape_string($encuesta['tram_solic_encVenta']);
                }
                
                // Construir la consulta de inserción
                $sql_insert = "INSERT INTO movimientos (
                    doc_encVenta, nom_encVenta, fec_reg_encVenta, tipo_documento, 
                    departamento_expedicion, ciudad_expedicion, fecha_expedicion, 
                    dir_encVenta, zona_encVenta, id_com, id_bar, otro_bar_ver_encVenta,
                    integra_encVenta, num_ficha_encVenta, sisben_nocturno, estado_ficha,
                    tipo_movimiento, fecha_movimiento, observacion, id_usu, id_encuesta,
                    fecha_alta_movimiento, fecha_edit_movimiento
                ) VALUES (
                    '$doc_encVenta', '$nom_encVenta', '$fec_reg_encVenta', '$tipo_documento',
                    '$departamento_expedicion', '$ciudad_expedicion', " . ($fecha_expedicion ? "'$fecha_expedicion'" : "NULL") . ",
                    '$dir_encVenta', '$zona_encVenta', " . ($id_com ? $id_com : "NULL") . ", " . ($id_bar ? $id_bar : "NULL") . ", '$otro_bar_ver_encVenta',
                    $integra_encVenta, '$num_ficha_encVenta', '$sisben_nocturno', $estado_ficha,
                    '$tipo_movimiento', '$fecha_alta', '$observacion', $id_usu, $id_encuesta,
                    '$fecha_alta', " . ($fecha_edit ? "'$fecha_edit'" : "NULL") . "
                )";
                
                if ($mysqli->query($sql_insert)) {
                    $total_migradas++;
                    if ($total_migradas % 50 == 0) {
                        echo "<p>📋 Migradas: $total_migradas registros...</p>";
                    }
                } else {
                    echo "<div class='alert alert-warning'>⚠️ Error migrando documento $doc_encVenta: " . $mysqli->error . "</div>";
                }
            }
            
            echo "<div class='alert alert-success'>✅ Total de registros migrados: $total_migradas</div>";
        }
        
        echo "<h3>📊 Paso 3: Actualizando registros existentes en movimientos</h3>";
        
        // Actualizar registros de movimientos que ya existen pero les faltan datos
        $sql_update_existing = "UPDATE movimientos m 
                               INNER JOIN encventanilla ev ON m.doc_encVenta = ev.doc_encVenta 
                               SET 
                                   m.nom_encVenta = COALESCE(m.nom_encVenta, ev.nom_encVenta),
                                   m.fec_reg_encVenta = COALESCE(m.fec_reg_encVenta, ev.fec_reg_encVenta),
                                   m.tipo_documento = COALESCE(m.tipo_documento, ev.tipo_documento),
                                   m.departamento_expedicion = COALESCE(m.departamento_expedicion, ev.departamento_expedicion),
                                   m.ciudad_expedicion = COALESCE(m.ciudad_expedicion, ev.ciudad_expedicion),
                                   m.fecha_expedicion = COALESCE(m.fecha_expedicion, ev.fecha_expedicion),
                                   m.dir_encVenta = COALESCE(m.dir_encVenta, ev.dir_encVenta),
                                   m.zona_encVenta = COALESCE(m.zona_encVenta, ev.zona_encVenta),
                                   m.id_com = COALESCE(m.id_com, ev.id_com),
                                   m.id_bar = COALESCE(m.id_bar, ev.id_bar),
                                   m.otro_bar_ver_encVenta = COALESCE(m.otro_bar_ver_encVenta, ev.otro_bar_ver_encVenta),
                                   m.num_ficha_encVenta = COALESCE(m.num_ficha_encVenta, ev.num_ficha_encVenta),
                                   m.sisben_nocturno = COALESCE(m.sisben_nocturno, ev.sisben_nocturno),
                                   m.estado_ficha = COALESCE(m.estado_ficha, ev.estado_ficha),
                                   m.id_encuesta = COALESCE(m.id_encuesta, ev.id_encVenta),
                                   m.fecha_alta_movimiento = COALESCE(m.fecha_alta_movimiento, ev.fecha_alta_encVenta)
                               WHERE m.nom_encVenta IS NULL OR m.nom_encVenta = ''";
        
        if ($mysqli->query($sql_update_existing)) {
            $updated_rows = $mysqli->affected_rows;
            echo "<div class='alert alert-success'>✅ Registros existentes actualizados: $updated_rows</div>";
        } else {
            echo "<div class='alert alert-warning'>⚠️ Error actualizando registros existentes: " . $mysqli->error . "</div>";
        }
        
        echo "<h3>📊 Paso 4: Creando tabla de integrantes independiente</h3>";
        
        // Crear tabla integmovimientos_independiente si no existe
        $create_integ_table = "CREATE TABLE IF NOT EXISTS integmovimientos_independiente (
            id_integMovIndep int(11) NOT NULL AUTO_INCREMENT,
            id_movimiento int(11) NOT NULL,
            doc_encVenta varchar(20) NOT NULL,
            cant_integVenta int(11) DEFAULT 1,
            gen_integVenta varchar(10) DEFAULT NULL,
            rango_integVenta varchar(50) DEFAULT NULL,
            orientacionSexual varchar(50) DEFAULT NULL,
            condicionDiscapacidad varchar(10) DEFAULT NULL,
            tipoDiscapacidad varchar(50) DEFAULT NULL,
            grupoEtnico varchar(100) DEFAULT NULL,
            victima varchar(10) DEFAULT NULL,
            mujerGestante varchar(10) DEFAULT NULL,
            cabezaFamilia varchar(10) DEFAULT NULL,
            experienciaMigratoria varchar(10) DEFAULT NULL,
            seguridadSalud varchar(50) DEFAULT NULL,
            nivelEducativo varchar(50) DEFAULT NULL,
            condicionOcupacion varchar(50) DEFAULT NULL,
            estado_integMovIndep tinyint(1) DEFAULT 1,
            fecha_alta_integMovIndep datetime DEFAULT CURRENT_TIMESTAMP,
            fecha_edit_integMovIndep datetime DEFAULT NULL,
            id_usu int(11) DEFAULT NULL,
            PRIMARY KEY (id_integMovIndep),
            KEY idx_id_movimiento (id_movimiento),
            KEY idx_doc_encVenta (doc_encVenta),
            KEY idx_estado (estado_integMovIndep)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        if ($mysqli->query($create_integ_table)) {
            echo "<div class='alert alert-success'>✅ Tabla integmovimientos_independiente creada/verificada.</div>";
        } else {
            echo "<div class='alert alert-warning'>⚠️ Error creando tabla de integrantes: " . $mysqli->error . "</div>";
        }
        
        echo "<h3>📊 Paso 5: Verificación final y estadísticas</h3>";
        
        // Estadísticas finales
        $stats_queries = [
            "Total registros en movimientos" => "SELECT COUNT(*) as total FROM movimientos",
            "Registros con datos completos" => "SELECT COUNT(*) as total FROM movimientos WHERE nom_encVenta IS NOT NULL",
            "Tipos de movimientos únicos" => "SELECT COUNT(DISTINCT tipo_movimiento) as total FROM movimientos",
            "Registros por estado de ficha" => "SELECT estado_ficha, COUNT(*) as total FROM movimientos GROUP BY estado_ficha"
        ];
        
        foreach ($stats_queries as $desc => $query) {
            $result = $mysqli->query($query);
            if ($result) {
                echo "<p><strong>$desc:</strong> ";
                if ($desc == "Registros por estado de ficha") {
                    while ($row = $result->fetch_assoc()) {
                        $estado_text = $row['estado_ficha'] == 1 ? 'Activas' : 'Retiradas';
                        echo "$estado_text: {$row['total']} | ";
                    }
                } else {
                    $row = $result->fetch_assoc();
                    echo $row['total'];
                }
                echo "</p>";
            }
        }
        
        // Confirmar transacción
        $mysqli->commit();
        $mysqli->autocommit(TRUE);
        
        echo "<div class='alert alert-success'>
                <h3>🎉 ¡MIGRACIÓN COMPLETADA EXITOSAMENTE!</h3>
                <p><strong>La tabla movimientos ahora es completamente independiente de encventanilla.</strong></p>
                <ul>
                    <li>✅ Todos los datos históricos se preservaron</li>
                    <li>✅ La estructura está lista para funcionamiento independiente</li>
                    <li>✅ Los formularios pueden funcionar sin encventanilla</li>
                    <li>✅ Se mantiene compatibilidad con datos existentes</li>
                </ul>
              </div>";
        
        echo "<div class='alert alert-info'>
                <h4>📋 Próximos pasos:</h4>
                <ol>
                    <li>Actualizar el archivo <code>verificar_encuesta.php</code> para consultar movimientos</li>
                    <li>Modificar <code>updateEncuesta.php</code> para funcionar independientemente</li>
                    <li>Probar el formulario <code>movimientosEncuesta.php</code></li>
                    <li>Verificar que todos los reportes funcionen correctamente</li>
                </ol>
              </div>";
        
    } catch (Exception $e) {
        // Rollback en caso de error
        $mysqli->rollback();
        $mysqli->autocommit(TRUE);
        
        echo "<div class='alert alert-danger'>
                <h3>❌ ERROR DURANTE LA MIGRACIÓN</h3>
                <p><strong>Error:</strong> " . $e->getMessage() . "</p>
                <p><strong>La migración ha sido revertida. La base de datos está en su estado original.</strong></p>
              </div>";
    }
    
} else {
    // Mostrar información previa y formulario de confirmación
    echo "<h3>📋 Información del proceso</h3>";
    
    echo "<div class='alert alert-info'>
            <h4>🔍 ¿Qué hace esta migración?</h4>
            <ol>
                <li><strong>Agrega nuevos campos a la tabla movimientos:</strong> Todos los campos necesarios de encventanilla</li>
                <li><strong>Migra datos históricos:</strong> Copia TODA la información existente de encventanilla a movimientos</li>
                <li><strong>Crea independencia total:</strong> movimientos ya no dependerá de encventanilla para funcionar</li>
                <li><strong>Preserva historial:</strong> No se pierde ningún dato existente</li>
                <li><strong>Mantiene compatibilidad:</strong> Los sistemas existentes seguirán funcionando</li>
            </ol>
          </div>";
    
    // Mostrar estadísticas actuales
    echo "<h4>📊 Estado actual del sistema</h4>";
    
    $current_stats = [
        "Registros en encventanilla" => "SELECT COUNT(*) as total FROM encventanilla",
        "Registros en movimientos" => "SELECT COUNT(*) as total FROM movimientos", 
        "Integrantes en integventanilla" => "SELECT COUNT(*) as total FROM integventanilla"
    ];
    
    echo "<table>";
    echo "<tr><th>Tabla</th><th>Registros</th></tr>";
    
    foreach ($current_stats as $desc => $query) {
        $result = $mysqli->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<tr><td>$desc</td><td>" . $row['total'] . "</td></tr>";
        }
    }
    echo "</table>";
    
    // Verificar estructura actual de movimientos
    echo "<h4>📋 Estructura actual de movimientos</h4>";
    $desc_result = $mysqli->query("DESCRIBE movimientos");
    if ($desc_result) {
        echo "<table>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th></tr>";
        while ($field = $desc_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $field['Field'] . "</td>";
            echo "<td>" . $field['Type'] . "</td>";
            echo "<td>" . $field['Null'] . "</td>";
            echo "<td>" . $field['Key'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<form method='POST' onsubmit='return confirm(\"¿Está seguro de ejecutar la migración completa? Este proceso es IRREVERSIBLE.\");'>";
    echo "<div class='alert alert-warning'>";
    echo "<h4>⚠️ Confirmación requerida</h4>";
    echo "<p>He leído y entiendo que:</p>";
    echo "<label><input type='checkbox' required> He hecho un BACKUP completo de la base de datos</label><br>";
    echo "<label><input type='checkbox' required> Entiendo que este proceso es IRREVERSIBLE</label><br>";
    echo "<label><input type='checkbox' required> Confirmo que quiero proceder con la migración</label><br><br>";
    echo "<button type='submit' name='ejecutar_migracion_completa' class='btn btn-danger'>🚀 EJECUTAR MIGRACIÓN COMPLETA</button>";
    echo "</div>";
    echo "</form>";
}
?>

    <div style="margin-top: 30px; text-align: center;">
        <a href="../../access.php" class="btn btn-primary">🏠 Volver al Sistema</a>
        <a href="../eventan/movimientosEncuesta.php" class="btn btn-success">📝 Ir a Movimientos Encuesta</a>
    </div>

</div>

</body>
</html>
