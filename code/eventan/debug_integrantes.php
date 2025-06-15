<?php
/**
 * Script de Diagnóstico Mejorado - Verificar Carga de Integrantes
 */

session_start();
if (!isset($_SESSION['id_usu'])) {
    die("Error: No hay sesión activa");
}

include("../../conexion.php");

// Verificar si se proporcionó un ID de movimiento
$id_movimiento = $_GET['id_movimiento'] ?? null;
if (!$id_movimiento) {
    die("Error: No se proporcionó ID de movimiento. Uso: debug_integrantes.php?id_movimiento=X");
}

echo "<h2>🔍 Diagnóstico Mejorado de Integrantes - Movimiento ID: $id_movimiento</h2>";

// 1. Verificar que el movimiento existe
echo "<h3>1. Verificando Movimiento</h3>";
$sql_movimiento = "SELECT * FROM movimientos WHERE id_movimiento = '$id_movimiento'";
$resultado_movimiento = mysqli_query($mysqli, $sql_movimiento);

if (!$resultado_movimiento || mysqli_num_rows($resultado_movimiento) == 0) {
    die("<p style='color:red'>❌ Movimiento no encontrado con ID: $id_movimiento</p>");
}

$movimiento = mysqli_fetch_assoc($resultado_movimiento);
echo "<p style='color:green'>✅ Movimiento encontrado:</p>";
echo "<ul>";
echo "<li><strong>Documento:</strong> " . $movimiento['doc_encVenta'] . "</li>";
echo "<li><strong>Nombre:</strong> " . $movimiento['nom_encVenta'] . "</li>";
echo "<li><strong>Integrantes esperados:</strong> " . $movimiento['integra_encVenta'] . "</li>";
echo "</ul>";

// 2. Verificar estructura de tabla
echo "<h3>2. Estructura de Tabla integmovimientos_independiente</h3>";
$sql_estructura = "DESCRIBE integmovimientos_independiente";
$resultado_estructura = mysqli_query($mysqli, $sql_estructura);

if ($resultado_estructura) {
    echo "<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size: 12px;'>";
    echo "<tr style='background-color: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Permite NULL</th><th>Default</th></tr>";
    
    $campos_disponibles = [];
    while ($campo = mysqli_fetch_assoc($resultado_estructura)) {
        $campos_disponibles[] = $campo['Field'];
        echo "<tr>";
        echo "<td><strong>" . $campo['Field'] . "</strong></td>";
        echo "<td>" . $campo['Type'] . "</td>";
        echo "<td>" . $campo['Null'] . "</td>";
        echo "<td>" . ($campo['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar campos esperados
    echo "<h4>Verificación de Campos Esperados:</h4>";
    $campos_esperados = [
        'id_integmov_indep', 'cant_integMovIndep', 'gen_integMovIndep', 'rango_integMovIndep',
        'orientacionSexual', 'condicionDiscapacidad', 'tipoDiscapacidad', 'grupoEtnico',
        'victima', 'mujerGestante', 'cabezaFamilia', 'nivelEducativo', 'doc_encVenta',
        'estado_integMovIndep', 'fecha_alta_integMovIndep'
    ];
    
    foreach ($campos_esperados as $campo_esperado) {
        if (in_array($campo_esperado, $campos_disponibles)) {
            echo "<p style='color:green'>✅ $campo_esperado</p>";
        } else {
            echo "<p style='color:red'>❌ $campo_esperado (FALTANTE)</p>";
        }
    }
}

// 3. Verificar integrantes en la base de datos
echo "<h3>3. Verificando Integrantes en BD</h3>";
$doc_encVenta = $movimiento['doc_encVenta'];

// Consulta con mapeo de rango
$sql_integrantes = "SELECT *, 
    CASE 
        WHEN rango_integMovIndep = 1 THEN '0 - 6'
        WHEN rango_integMovIndep = 2 THEN '7 - 12'
        WHEN rango_integMovIndep = 3 THEN '13 - 17'
        WHEN rango_integMovIndep = 4 THEN '18 - 28'
        WHEN rango_integMovIndep = 5 THEN '29 - 45'
        WHEN rango_integMovIndep = 6 THEN '46 - 64'
        WHEN rango_integMovIndep = 7 THEN 'Mayor o igual a 65'
        ELSE 'Rango desconocido'
    END as rango_texto
    FROM integmovimientos_independiente 
    WHERE doc_encVenta = '$doc_encVenta' 
    AND estado_integMovIndep = 1
    ORDER BY fecha_alta_integMovIndep DESC";

echo "<p><strong>Consulta SQL:</strong></p>";
echo "<pre style='background-color: #f0f0f0; padding: 10px;'>$sql_integrantes</pre>";

$resultado_integrantes = mysqli_query($mysqli, $sql_integrantes);

if (!$resultado_integrantes) {
    echo "<p style='color:red'>❌ Error en consulta: " . mysqli_error($mysqli) . "</p>";
} else {
    $num_integrantes = mysqli_num_rows($resultado_integrantes);
    echo "<p style='color:green'>✅ Consulta exitosa. Integrantes encontrados: $num_integrantes</p>";
    
    if ($num_integrantes > 0) {
        echo "<h3>4. Detalles de Integrantes</h3>";
        echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>ID</th><th>Cantidad</th><th>Género</th><th>Rango (Num)</th><th>Rango (Texto)</th>";
        echo "<th>Orientación</th><th>Discapacidad</th><th>Estado</th><th>Fecha Alta</th>";
        echo "</tr>";
        
        while ($integrante = mysqli_fetch_assoc($resultado_integrantes)) {
            echo "<tr>";
            echo "<td>" . $integrante['id_integmov_indep'] . "</td>";
            echo "<td><strong>" . $integrante['cant_integMovIndep'] . "</strong></td>";
            echo "<td>" . $integrante['gen_integMovIndep'] . "</td>";
            echo "<td>" . $integrante['rango_integMovIndep'] . "</td>";
            echo "<td><strong>" . $integrante['rango_texto'] . "</strong></td>";
            echo "<td>" . ($integrante['orientacionSexual'] ?? 'N/A') . "</td>";
            echo "<td>" . ($integrante['condicionDiscapacidad'] ?? 'N/A') . "</td>";
            echo "<td>" . $integrante['estado_integMovIndep'] . "</td>";
            echo "<td>" . $integrante['fecha_alta_integMovIndep'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:orange'>⚠️ No se encontraron integrantes para este documento.</p>";
    }
}

echo "<hr>";
echo "<p><a href='editMovimiento.php?id_movimiento=$id_movimiento'>🔙 Volver al Editor</a></p>";
echo "<p><a href='verificar_estructura_integrantes.php'>🔍 Ver Estructura Completa</a></p>";
echo "<p><a href='showMovimientos.php'>📋 Ver Todos los Movimientos</a></p>";

mysqli_close($mysqli);
?>
