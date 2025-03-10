<?php 
require('../../conexion.php');
sleep(1);
if (isset($_POST)) {
    $doc_enc = (string)$_POST['doc_enc'];
    
    $result = $mysqli->query(
        'SELECT * FROM encuestadores WHERE doc_enc = "'.strtolower($doc_enc).'"'
    );
    
    if ($result->num_rows > 0) {
        echo '<div class="alert alert-danger"><strong>VERIFICA EL DOCUMENTO DEL CONTRATISTA!</strong> Ya existe uno igual.</div>';
    } else {
        echo '<div class="alert alert-success"><strong>ES NUEVO REGISTRO!</strong> El contratista no está registrado.</div>';
    }
}