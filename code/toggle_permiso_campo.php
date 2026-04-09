<?php
session_start();

// Solo el administrador (tipo_usu == 1) puede cambiar esta configuración
if (!isset($_SESSION['id_usu']) || (int)$_SESSION['tipo_usu'] !== 1) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

include('../conexion.php');
header('Content-Type: application/json');

// Crear tabla de configuración si no existe
$mysqli->query("CREATE TABLE IF NOT EXISTS configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TINYINT(1) NOT NULL DEFAULT 0
)");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_valor = (isset($_POST['valor']) && intval($_POST['valor']) === 1) ? 1 : 0;

    $stmt = $mysqli->prepare(
        "INSERT INTO configuracion (clave, valor) VALUES ('bloqueo_campo_jue_vie', ?)
         ON DUPLICATE KEY UPDATE valor = ?"
    );
    if ($stmt) {
        $stmt->bind_param('ii', $nuevo_valor, $nuevo_valor);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true, 'valor' => $nuevo_valor]);
    } else {
        echo json_encode(['error' => 'Error al guardar la configuración']);
    }
} else {
    // GET: retornar valor actual
    $result = $mysqli->query("SELECT valor FROM configuracion WHERE clave = 'bloqueo_campo_jue_vie'");
    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode(['valor' => (int)$row['valor']]);
    } else {
        echo json_encode(['valor' => 0]);
    }
}
