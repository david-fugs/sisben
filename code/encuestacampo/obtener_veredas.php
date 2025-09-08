<?php
header('Content-Type: application/json');
require '../../conexion.php';

$response = [];

try {
    // Para esta aplicación, simulamos una lista de veredas rurales de Pereira, Risaralda
    
    $veredas = [
        ['codigo' => '1', 'nombre' => 'Altagracia'],
        ['codigo' => '2', 'nombre' => 'Arabia'],
        ['codigo' => '3', 'nombre' => 'Cerritos'],
        ['codigo' => '4', 'nombre' => 'Combia Baja'],
        ['codigo' => '5', 'nombre' => 'Combia Alta'],
        ['codigo' => '6', 'nombre' => 'El Chocho'],
        ['codigo' => '7', 'nombre' => 'El Tigre'],
        ['codigo' => '8', 'nombre' => 'Estrella del Alto'],
        ['codigo' => '9', 'nombre' => 'Galicia'],
        ['codigo' => '10', 'nombre' => 'La Bella'],
        ['codigo' => '11', 'nombre' => 'La Florida'],
        ['codigo' => '12', 'nombre' => 'La Paloma'],
        ['codigo' => '13', 'nombre' => 'La Platanera'],
        ['codigo' => '14', 'nombre' => 'La Suiza'],
        ['codigo' => '15', 'nombre' => 'Las Delicias'],
        ['codigo' => '16', 'nombre' => 'Los Naranjos'],
        ['codigo' => '17', 'nombre' => 'Mundo Nuevo'],
        ['codigo' => '18', 'nombre' => 'Morelia'],
        ['codigo' => '19', 'nombre' => 'Playa Rica'],
        ['codigo' => '20', 'nombre' => 'Puerto Caldas'],
        ['codigo' => '21', 'nombre' => 'Quimbaya'],
        ['codigo' => '22', 'nombre' => 'Salado de Consota'],
        ['codigo' => '23', 'nombre' => 'Santágueda'],
        ['codigo' => '24', 'nombre' => 'Tribunas'],
        ['codigo' => '25', 'nombre' => 'Yarumal'],
        ['codigo' => '26', 'nombre' => 'Caimalito'],
        ['codigo' => '27', 'nombre' => 'El Manzano'],
        ['codigo' => '28', 'nombre' => 'Condina'],
        ['codigo' => '29', 'nombre' => 'El Estanquillo'],
        ['codigo' => '30', 'nombre' => 'Guacas']
    ];
    
    echo json_encode($veredas);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener las veredas: ' . $e->getMessage()]);
}
?>
