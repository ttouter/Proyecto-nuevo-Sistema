<?php
session_start();
require_once '../models/ModeloAdmin.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idUsuario = $_POST['idUsuario'];
    $nuevoRol  = $_POST['nuevoRol'];
    $escuela   = $_POST['escuelaModal'];

    // Lógica simple para asignar roles usando los métodos que ya tienes
    if ($nuevoRol == 'juez' || $nuevoRol == 'ambos') {
        // Asignar como Juez (requiere grado, pondremos uno por defecto o null)
        ModeloAdmin::asignarRolJuez($idUsuario, $escuela, 'Licenciatura');
    }
    
    if ($nuevoRol == 'entrenador' || $nuevoRol == 'ambos') {
        // Asignar como Entrenador
        ModeloAdmin::asignarRolEntrenador($idUsuario, $escuela);
    }

    $mensaje = "Rol actualizado correctamente.";
    header("Location: ../views/dashboards/adminDashboard.php?msg=" . urlencode($mensaje));
    exit;
}
?>