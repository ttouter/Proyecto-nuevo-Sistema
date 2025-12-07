<?php
session_start();
require_once '../models/ModeloAdmin.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombreEvento']);
    $lugar  = trim($_POST['lugarEvento']);
    $fecha  = $_POST['fechaEvento'];

    if(empty($nombre) || empty($lugar) || empty($fecha)){
        $msg = "Faltan datos del evento.";
    } else {
        // Usamos crearEvento (que corregimos anteriormente)
        $resultado = ModeloAdmin::crearEvento($nombre, $lugar, $fecha);
        $msg = $resultado;
    }

    header("Location: ../views/dashboards/adminDashboard.php?msg=" . urlencode($msg));
    exit;
}
?>