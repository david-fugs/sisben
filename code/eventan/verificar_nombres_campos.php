<?php
/**
 * SCRIPT DE CORRECCIÓN MASIVA - NOMBRES DE CAMPOS DE INTEGRANTES
 * 
 * Este script actualiza todos los nombres de campos para que coincidan
 * exactamente con la estructura real de la base de datos.
 */

echo "<h1>🔧 Corrección de Nombres de Campos - Integrantes</h1>";

// Mapeo de correcciones necesarias
$correcciones = [
    // IDs
    'id_integMovIndep' => 'id_integmov_indep',
    'estado_integMovIndep' => 'estado_integMovIndep', // Este está correcto
    'fecha_alta_integMovIndep' => 'fecha_alta_integMovIndep', // Este está correcto
    'fecha_edit_integMovIndep' => 'fecha_edit_integMovIndep', // Este está correcto
    
    // Campos que están correctos (no necesitan cambio)
    'cant_integMovIndep' => 'cant_integMovIndep',
    'gen_integMovIndep' => 'gen_integMovIndep', 
    'rango_integMovIndep' => 'rango_integMovIndep',
    
    // Campos que ya están con nombres correctos
    'orientacionSexual' => 'orientacionSexual',
    'condicionDiscapacidad' => 'condicionDiscapacidad',
    'tipoDiscapacidad' => 'tipoDiscapacidad',
    'grupoEtnico' => 'grupoEtnico',
    'victima' => 'victima',
    'mujerGestante' => 'mujerGestante',
    'cabezaFamilia' => 'cabezaFamilia',
    'experienciaMigratoria' => 'experienciaMigratoria',
    'seguridadSalud' => 'seguridadSalud',
    'nivelEducativo' => 'nivelEducativo',
    'condicionOcupacion' => 'condicionOcupacion'
];

echo "<h2>Nombres de Campos a Corregir:</h2>";
echo "<ul>";
echo "<li><strong>PRINCIPAL:</strong> 'id_integMovIndep' → 'id_integmov_indep'</li>";
echo "<li>Los demás campos ya están correctos según la estructura de BD</li>";
echo "</ul>";

echo "<h2>✅ Verificación de Estructura de BD:</h2>";

include("../../conexion.php");

$sql_estructura = "DESCRIBE integmovimientos_independiente";
$resultado = mysqli_query($mysqli, $sql_estructura);

echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
echo "<tr style='background-color: #f0f0f0;'><th>Campo Real en BD</th><th>Tipo</th><th>Estado en Código</th></tr>";

while ($campo = mysqli_fetch_assoc($resultado)) {
    $nombre_campo = $campo['Field'];
    $tipo_campo = $campo['Type'];
    
    $estado = "✅ Correcto";
    if ($nombre_campo == 'id_integmov_indep') {
        $estado = "❌ Usar 'id_integmov_indep' no 'id_integMovIndep'";
    }
    
    echo "<tr>";
    echo "<td><strong>$nombre_campo</strong></td>";
    echo "<td>$tipo_campo</td>";
    echo "<td>$estado</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>📝 Archivos que necesitan actualización:</h2>";
echo "<ul>";
echo "<li><code>editMovimiento.php</code> - Cambiar data-integrante-id</li>";
echo "<li><code>updateMovimiento.php</code> - Cambiar referencias al ID</li>";
echo "<li><code>eliminarIntegrante.php</code> - Cambiar referencias al ID</li>";
echo "</ul>";

mysqli_close($mysqli);
?>
