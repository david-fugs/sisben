<?php
/**
 * VERIFICAR ESTADO DE INDEPENDENCIA
 * 
 * Este script verifica si la migración de independencia ya se ejecutó
 * y muestra el estado actual de las estructuras de datos.
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
    <title>Verificar Estado Independencia - SISBEN</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <style>
        .status-ok { color: #28a745; font-weight: bold; }
        .status-pending { color: #ffc107; font-weight: bold; }
        .status-error { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>🔍 Verificación del Estado de Independencia</h2>
        <hr>

        <?php
        try {
            echo "<h4>📊 Estado de las Estructuras de Datos</h4>";
            
            // 1. Verificar estructura de tabla movimientos
            echo "<h5>1. Tabla 'movimientos'</h5>";
            $sql_describe = "DESCRIBE movimientos";
            $result_describe = $mysqli->query($sql_describe);
            
            $columnas_necesarias = [
                'nom_encVenta', 'fec_reg_encVenta', 'tipo_documento', 'departamento_expedicion',
                'ciudad_expedicion', 'fecha_expedicion', 'dir_encVenta', 'zona_encVenta',
                'id_com', 'id_bar', 'otro_bar_ver_encVenta', 'integra_encVenta',
                'num_ficha_encVenta', 'sisben_nocturno', 'estado_ficha'
            ];
            
            $columnas_existentes = [];
            while ($col = $result_describe->fetch_assoc()) {
                $columnas_existentes[] = $col['Field'];
            }
            
            $columnas_faltantes = array_diff($columnas_necesarias, $columnas_existentes);
            
            if (empty($columnas_faltantes)) {
                echo "<p class='status-ok'>✅ Estructura de movimientos: COMPLETA</p>";
                $estructura_movimientos_ok = true;
            } else {
                echo "<p class='status-pending'>⚠️ Estructura de movimientos: INCOMPLETA</p>";
                echo "<p>Columnas faltantes: " . implode(', ', $columnas_faltantes) . "</p>";
                $estructura_movimientos_ok = false;
            }
            
            // 2. Verificar tabla integmovimientos_independiente
            echo "<h5>2. Tabla 'integmovimientos_independiente'</h5>";
            $sql_check_table = "SHOW TABLES LIKE 'integmovimientos_independiente'";
            $result_check = $mysqli->query($sql_check_table);
            
            if ($result_check->num_rows > 0) {
                echo "<p class='status-ok'>✅ Tabla integmovimientos_independiente: EXISTE</p>";
                $tabla_integrantes_ok = true;
            } else {
                echo "<p class='status-pending'>⚠️ Tabla integmovimientos_independiente: NO EXISTE</p>";
                $tabla_integrantes_ok = false;
            }
            
            // 3. Verificar datos migrados
            echo "<h5>3. Datos Migrados</h5>";
            
            // Contar registros en encventanilla
            $sql_count_enc = "SELECT COUNT(*) as total FROM encventanilla";
            $result_count_enc = $mysqli->query($sql_count_enc);
            $count_enc = $result_count_enc->fetch_assoc()['total'];
            
            // Contar registros en movimientos
            $sql_count_mov = "SELECT COUNT(*) as total FROM movimientos";
            $result_count_mov = $mysqli->query($sql_count_mov);
            $count_mov = $result_count_mov->fetch_assoc()['total'];
            
            echo "<p><strong>Registros en encventanilla:</strong> $count_enc</p>";
            echo "<p><strong>Registros en movimientos:</strong> $count_mov</p>";
            
            if ($count_mov > 0) {
                echo "<p class='status-ok'>✅ Datos en movimientos: PRESENTES</p>";
                
                // Verificar si hay datos independientes
                if ($estructura_movimientos_ok) {
                    $sql_check_independent = "SELECT COUNT(*) as total FROM movimientos WHERE nom_encVenta IS NOT NULL";
                    $result_independent = $mysqli->query($sql_check_independent);
                    $count_independent = $result_independent->fetch_assoc()['total'];
                    
                    if ($count_independent > 0) {
                        echo "<p class='status-ok'>✅ Datos independientes: $count_independent registros</p>";
                        $datos_independientes_ok = true;
                    } else {
                        echo "<p class='status-pending'>⚠️ Datos independientes: NO MIGRADOS</p>";
                        $datos_independientes_ok = false;
                    }
                } else {
                    $datos_independientes_ok = false;
                }
            } else {
                echo "<p class='status-pending'>⚠️ Datos en movimientos: NO HAY DATOS</p>";
                $datos_independientes_ok = false;
            }
            
            // 4. Resumen final
            echo "<hr><h4>📋 Resumen del Estado</h4>";
            
            $migracion_completa = $estructura_movimientos_ok && $tabla_integrantes_ok && $datos_independientes_ok;
            
            if ($migracion_completa) {
                echo "<div class='alert alert-success'>";
                echo "<h5>🎉 ¡INDEPENDENCIA COMPLETA!</h5>";
                echo "<p>El sistema está completamente independizado y listo para funcionar sin encventanilla.</p>";
                echo "<ul>";
                echo "<li>✅ Estructura de movimientos completa</li>";
                echo "<li>✅ Tabla de integrantes independiente existe</li>";
                echo "<li>✅ Datos migrados correctamente</li>";
                echo "</ul>";
                echo "</div>";
                
                echo "<h5>🚀 Acciones Disponibles</h5>";
                echo "<a href='../eventan/movimientosEncuesta.php' class='btn btn-success'>Probar Sistema Independiente</a>";
                echo "<a href='../../access.php' class='btn btn-primary'>Ir al Menú Principal</a>";
                
            } else {
                echo "<div class='alert alert-warning'>";
                echo "<h5>⚠️ MIGRACIÓN PENDIENTE</h5>";
                echo "<p>La independencia no está completa. Se requiere ejecutar la migración.</p>";
                echo "<ul>";
                echo "<li>" . ($estructura_movimientos_ok ? "✅" : "❌") . " Estructura de movimientos</li>";
                echo "<li>" . ($tabla_integrantes_ok ? "✅" : "❌") . " Tabla de integrantes independiente</li>";
                echo "<li>" . ($datos_independientes_ok ? "✅" : "❌") . " Datos migrados</li>";
                echo "</ul>";
                echo "</div>";
                
                echo "<h5>🛠️ Acciones Requeridas</h5>";
                echo "<a href='migracion_independencia_completa.php' class='btn btn-warning'>Ejecutar Migración Completa</a>";
                echo "<a href='../../access.php' class='btn btn-secondary'>Volver al Menú</a>";
            }
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>";
            echo "<h5>❌ Error de Verificación</h5>";
            echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
            echo "</div>";
        }
        ?>
        
        <hr>
        <p><small><em>Fecha de verificación: <?php echo date('Y-m-d H:i:s'); ?></em></small></p>
    </div>
</body>
</html>
