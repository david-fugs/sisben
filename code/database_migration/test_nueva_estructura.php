<?php
// Script para probar la nueva estructura de movimientos
include("../../conexion.php");
header("Content-Type: text/html;charset=utf-8");

echo "<h2>🧪 Prueba de Nueva Estructura de Movimientos</h2>";

// Verificar estructura
echo "<h3>📋 Verificando Estructura</h3>";

$desc_query = "DESCRIBE movimientos";
$result_desc = $mysqli->query($desc_query);

if ($result_desc) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
    echo "<tr style='background-color: #f8f9fa;'><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    $has_new_structure = false;
    while ($field = $result_desc->fetch_assoc()) {
        echo "<tr>";
        echo "<td><strong>" . $field['Field'] . "</strong></td>";
        echo "<td>" . $field['Type'] . "</td>";
        echo "<td>" . $field['Null'] . "</td>";
        echo "<td>" . $field['Key'] . "</td>";
        echo "<td>" . ($field['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
        
        if ($field['Field'] == 'tipo_movimiento') {
            $has_new_structure = true;
        }
    }
    echo "</table>";
    
    if ($has_new_structure) {
        echo "<p style='color: green;'>✅ <strong>Estructura correcta: Nueva estructura individual detectada</strong></p>";
    } else {
        echo "<p style='color: red;'>❌ <strong>Estructura antigua: Aún usa sistema de contadores</strong></p>";
        echo "<p><a href='ejecutar_migracion.php'>Ejecutar migración</a></p>";
        exit;
    }
} else {
    echo "<p style='color: red;'>❌ Error al verificar estructura: " . $mysqli->error . "</p>";
    exit;
}

// Verificar datos existentes
echo "<h3>📊 Datos Existentes</h3>";

$count_query = "SELECT COUNT(*) as total FROM movimientos";
$result_count = $mysqli->query($count_query);

if ($result_count) {
    $count_row = $result_count->fetch_assoc();
    echo "<p><strong>Total de registros:</strong> " . $count_row['total'] . "</p>";
    
    if ($count_row['total'] > 0) {
        // Mostrar últimos 10 registros
        $sample_query = "SELECT * FROM movimientos ORDER BY fecha_movimiento DESC LIMIT 10";
        $result_sample = $mysqli->query($sample_query);
        
        if ($result_sample) {
            echo "<h4>📄 Últimos 10 registros:</h4>";
            echo "<table border='1' style='border-collapse: collapse; margin: 10px; width: 100%;'>";
            echo "<tr style='background-color: #f8f9fa;'>
                    <th>ID</th>
                    <th>Documento</th>
                    <th>Tipo Movimiento</th>
                    <th>Fecha</th>
                    <th>Observación</th>
                    <th>Usuario</th>
                  </tr>";
            
            while ($row = $result_sample->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id_movimiento'] . "</td>";
                echo "<td>" . $row['doc_encVenta'] . "</td>";
                echo "<td><strong>" . $row['tipo_movimiento'] . "</strong></td>";
                echo "<td>" . $row['fecha_movimiento'] . "</td>";
                echo "<td>" . substr($row['observacion'], 0, 50) . (strlen($row['observacion']) > 50 ? '...' : '') . "</td>";
                echo "<td>" . $row['id_usu'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Estadísticas por tipo
        echo "<h4>📈 Estadísticas por Tipo de Movimiento:</h4>";
        $stats_query = "SELECT tipo_movimiento, COUNT(*) as cantidad FROM movimientos GROUP BY tipo_movimiento ORDER BY cantidad DESC";
        $result_stats = $mysqli->query($stats_query);
        
        if ($result_stats) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
            echo "<tr style='background-color: #f8f9fa;'><th>Tipo de Movimiento</th><th>Cantidad</th></tr>";
            
            while ($stat = $result_stats->fetch_assoc()) {
                echo "<tr>";
                echo "<td><strong>" . $stat['tipo_movimiento'] . "</strong></td>";
                echo "<td>" . $stat['cantidad'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No hay registros en la tabla movimientos.</p>";
    }
}

// Probar inserción de muestra
if (isset($_POST['test_insert'])) {
    echo "<h3>🧪 Probando Inserción</h3>";
    
    $test_doc = 'TEST' . time();
    $test_tipo = 'inclusion';
    $test_obs = 'Prueba de inserción - ' . date('Y-m-d H:i:s');
    $test_usuario = 1; // Asumir usuario ID 1 existe
    
    $insert_query = "INSERT INTO movimientos (doc_encVenta, tipo_movimiento, observacion, id_usu) 
                     VALUES ('$test_doc', '$test_tipo', '$test_obs', '$test_usuario')";
    
    if ($mysqli->query($insert_query)) {
        echo "<p style='color: green;'>✅ <strong>Inserción exitosa!</strong></p>";
        echo "<p>Documento: $test_doc</p>";
        echo "<p>Tipo: $test_tipo</p>";
        echo "<p>Observación: $test_obs</p>";
        
        // Mostrar el registro insertado
        $verify_query = "SELECT * FROM movimientos WHERE doc_encVenta = '$test_doc' ORDER BY id_movimiento DESC LIMIT 1";
        $result_verify = $mysqli->query($verify_query);
        
        if ($result_verify && $row = $result_verify->fetch_assoc()) {
            echo "<h4>📝 Registro insertado:</h4>";
            echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
            echo "<tr style='background-color: #f8f9fa;'>";
            foreach ($row as $key => $value) {
                echo "<th>$key</th>";
            }
            echo "</tr><tr>";
            foreach ($row as $key => $value) {
                echo "<td>$value</td>";
            }
            echo "</tr></table>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ <strong>Error en inserción:</strong> " . $mysqli->error . "</p>";
    }
}

// Probar consulta tipo exportador
if (isset($_POST['test_export_query'])) {
    echo "<h3>📊 Probando Consulta de Exportador</h3>";
    
    $export_query = "
    SELECT 
        m.doc_encVenta,
        COALESCE(ev.nom_encVenta, i.nom_info, 'N/A') as nombre_persona,
        COALESCE(ev.dir_encVenta, i.dir_info, 'N/A') as direccion,
        m.tipo_movimiento,
        m.fecha_movimiento,
        m.observacion,
        u.nombre AS nombre_usuario,
        CASE 
            WHEN m.id_encuesta IS NOT NULL THEN 'ENCUESTA'
            WHEN m.id_informacion IS NOT NULL THEN 'INFORMACION'
            ELSE 'N/A'
        END as origen
    FROM movimientos m
    LEFT JOIN usuarios u ON m.id_usu = u.id_usu
    LEFT JOIN encventanilla ev ON m.id_encuesta = ev.id_encVenta
    LEFT JOIN informacion i ON m.id_informacion = i.id_informacion
    ORDER BY m.fecha_movimiento DESC
    LIMIT 5
    ";
    
    $result_export = $mysqli->query($export_query);
    
    if ($result_export) {
        echo "<p style='color: green;'>✅ <strong>Consulta de exportador ejecutada exitosamente</strong></p>";
        
        if ($result_export->num_rows > 0) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px; width: 100%;'>";
            echo "<tr style='background-color: #f8f9fa;'>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Tipo Movimiento</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Origen</th>
                  </tr>";
            
            while ($row = $result_export->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['doc_encVenta'] . "</td>";
                echo "<td>" . substr($row['nombre_persona'], 0, 30) . "</td>";
                echo "<td>" . substr($row['direccion'], 0, 30) . "</td>";
                echo "<td><strong>" . $row['tipo_movimiento'] . "</strong></td>";
                echo "<td>" . $row['fecha_movimiento'] . "</td>";
                echo "<td>" . ($row['nombre_usuario'] ?? 'N/A') . "</td>";
                echo "<td>" . $row['origen'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay datos para mostrar.</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ <strong>Error en consulta:</strong> " . $mysqli->error . "</p>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Prueba Nueva Estructura Movimientos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-warning { background-color: #ffc107; color: black; }
        table { font-size: 12px; }
        th, td { padding: 5px; text-align: left; }
    </style>
</head>
<body>

<div class="form-section">
    <h3>🧪 Pruebas Disponibles</h3>
    
    <form method="POST" style="display: inline;">
        <button type="submit" name="test_insert" value="1" class="btn-primary">
            Probar Inserción de Registro
        </button>
    </form>
    
    <form method="POST" style="display: inline;">
        <button type="submit" name="test_export_query" value="1" class="btn-warning">
            Probar Consulta de Exportador
        </button>
    </form>
</div>

<div class="form-section">
    <h3>🔙 Navegación</h3>
    <a href="ejecutar_migracion.php">
        <button class="btn-success">Volver a Migración</button>
    </a>
    <a href="../../access.php">
        <button class="btn-success">Volver al Sistema</button>
    </a>
</div>

</body>
</html>
