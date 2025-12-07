<?php
session_start();
require_once '../models/ModeloEntrenador.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_id'])) {
    
    $nombreEquipo = trim($_POST['nombreEquipo']);
    $idCategoria  = $_POST['idCategoria'];
    $nombreEvento = $_POST['nombreEvento'];
    $codEscuela   = $_POST['codEscuela']; 
    $idAsistente  = $_SESSION['usuario_id'];

    if(empty($nombreEquipo) || empty($idCategoria) || empty($nombreEvento)){
         header("Location: ../views/dashboards/asistenteDashboard.php?error=CamposVacios");
         exit;
    }

    // Registrar usando el Modelo
    $mensaje = ModeloEntrenador::registrarEquipo($nombreEquipo, $idCategoria, $codEscuela, $nombreEvento, $idAsistente);

    // LOGICA NUEVA PARA DETECTAR EL ERROR DE NOMBRE
    if (strpos($mensaje, 'Error:') !== false) {
        // Si el mensaje empieza con "Error:", redirigimos como error (rojo)
        header("Location: ../views/dashboards/asistenteDashboard.php?error=" . urlencode($mensaje));
    } else {
        // Si no, es éxito (verde)
        header("Location: ../views/dashboards/asistenteDashboard.php?msg=" . urlencode($mensaje));
    }
    exit;

} else {
    header("Location: ../views/login/login_unificado.php");
    exit;
}
?>