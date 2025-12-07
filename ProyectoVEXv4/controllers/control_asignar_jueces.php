<?php
session_start();
require_once '../models/ModeloAdmin.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idCategoria = $_POST['idCategoria'];
    // Recibimos el array de checkboxes seleccionados
    $jueces = isset($_POST['jueces']) ? $_POST['jueces'] : [];

    // Validación básica: Máximo 3 jueces
    if (count($jueces) > 3) {
        $msg = "Error: Maximo 3 jueces permitidos por categoria";
        header("Location: ../views/dashboards/adminDashboard.php?msg=" . urlencode($msg));
        exit;
    }

    if (empty($jueces)) {
        $msg = "Error: Debes seleccionar al menos un juez";
        header("Location: ../views/dashboards/adminDashboard.php?msg=" . urlencode($msg));
        exit;
    }

    // 1. Obtener todos los equipos de esa categoría
    // (Esta función debe existir en ModeloAdmin, la agregamos en el PASO 1 anterior)
    $equipos = ModeloAdmin::obtenerEquiposPorCategoria($idCategoria);

    if (empty($equipos)) {
        $msg = "Aviso: No hay equipos registrados en esta categoría para asignar.";
        header("Location: ../views/dashboards/adminDashboard.php?msg=" . urlencode($msg));
        exit;
    }

    // 2. Asignar cada juez seleccionado a CADA equipo de esa categoría
    $contador = 0;
    foreach ($equipos as $equipo) {
        foreach ($jueces as $idJuez) {
            // Llamamos a la función que ejecuta el SP AsignarJuezEquipo
            ModeloAdmin::asignarJuezEquipo($idJuez, $equipo['idEquipo']);
            $contador++;
        }
    }

    $msg = "Asignación exitosa. Se asignaron jueces a " . count($equipos) . " equipos.";
    header("Location: ../views/dashboards/adminDashboard.php?msg=" . urlencode($msg));
    exit;
} else {
    // Si intentan entrar directo sin POST
    header("Location: ../views/dashboards/adminDashboard.php");
    exit;
}
?>