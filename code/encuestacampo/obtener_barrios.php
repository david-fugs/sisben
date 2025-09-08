<?php
header('Content-Type: application/json');
require '../../conexion.php';

$response = [];

try {
    // Para esta aplicación, simulamos una lista de barrios de Pereira, Risaralda
    // En un caso real, estos datos vendrían de una tabla de barrios en la base de datos
    
    $barrios = [
        ['codigo' => '1', 'nombre' => 'Centro'],
        ['codigo' => '2', 'nombre' => 'San Jorge'],
        ['codigo' => '3', 'nombre' => 'Poblado I'],
        ['codigo' => '4', 'nombre' => 'Poblado II'],
        ['codigo' => '5', 'nombre' => 'Villa Santana'],
        ['codigo' => '6', 'nombre' => 'Boston'],
        ['codigo' => '7', 'nombre' => 'Jardín'],
        ['codigo' => '8', 'nombre' => 'El Rocío'],
        ['codigo' => '9', 'nombre' => 'Ferrocarril'],
        ['codigo' => '10', 'nombre' => 'Universidad'],
        ['codigo' => '11', 'nombre' => 'Cuba'],
        ['codigo' => '12', 'nombre' => 'Los Álamos'],
        ['codigo' => '13', 'nombre' => 'Nacederos'],
        ['codigo' => '14', 'nombre' => 'El Plumón'],
        ['codigo' => '15', 'nombre' => 'Kennedy'],
        ['codigo' => '16', 'nombre' => 'Olímpica'],
        ['codigo' => '17', 'nombre' => 'San Fernando'],
        ['codigo' => '18', 'nombre' => 'El Cardal'],
        ['codigo' => '19', 'nombre' => 'Maracaibo'],
        ['codigo' => '20', 'nombre' => 'El Dorado'],
        ['codigo' => '21', 'nombre' => 'Leningrado'],
        ['codigo' => '22', 'nombre' => 'Villa Verde'],
        ['codigo' => '23', 'nombre' => 'El Progreso'],
        ['codigo' => '24', 'nombre' => 'La Dulcera'],
        ['codigo' => '25', 'nombre' => 'Perla del Otún'],
        ['codigo' => '26', 'nombre' => 'El Jordán'],
        ['codigo' => '27', 'nombre' => 'Portal de Comfamiliar'],
        ['codigo' => '28', 'nombre' => 'Tokio'],
        ['codigo' => '29', 'nombre' => 'El Remanso'],
        ['codigo' => '30', 'nombre' => 'Villa Consota']
    ];
    
    echo json_encode($barrios);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener los barrios: ' . $e->getMessage()]);
}
?>
