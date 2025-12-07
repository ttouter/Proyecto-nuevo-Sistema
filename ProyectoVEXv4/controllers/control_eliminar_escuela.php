<?php
session_start();
require_once '../models/ModeloAdmin.php';

if (isset($_GET['id'])) {
    $codigo = $_GET['id'];
    
    // Llamamos a la función de eliminar
    $resultado = ModeloAdmin::eliminarEscuela($codigo);
    
    // Redirigimos con el mensaje
    header("Location: ../views/dashboards/adminDashboard.php?msg=" . urlencode($resultado));
    exit;
} else {
    header("Location: ../views/dashboards/adminDashboard.php");
    exit;
}
?>