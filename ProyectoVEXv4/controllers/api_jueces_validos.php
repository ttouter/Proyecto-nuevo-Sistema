<?php
require_once '../models/ModeloAdmin.php';

if(isset($_GET['cat'])) {
    $idCategoria = $_GET['cat'];
    // Usamos la nueva función del modelo (Anti-Conflicto)
    $jueces = ModeloAdmin::obtenerJuecesValidos($idCategoria);
    
    // Devolvemos JSON para que Javascript lo lea
    header('Content-Type: application/json');
    echo json_encode($jueces);
}
?>