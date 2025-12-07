<?php
session_start();
require_once '../models/ModeloAdmin.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir datos del formulario
    $codigo = trim($_POST['codEscuela']);
    $nombre = trim($_POST['nombreEscuela']);

    if(empty($codigo) || empty($nombre)){
        $msg = "Todos los campos son obligatorios.";
    } else {
        // Llamar al modelo
        $resultado = ModeloAdmin::crearEscuela($codigo, $nombre);
        $msg = $resultado;
    }

    // Redirigir al dashboard con el mensaje
    header("Location: ../views/dashboards/adminDashboard.php?msg=" . urlencode($msg));
    exit;
} else {
    header("Location: ../views/dashboards/adminDashboard.php");
    exit;
}
?>