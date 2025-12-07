<?php
session_start();
require_once '../models/ModeloProcesos.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_id'])) {
    
    $idEquipo   = $_POST['idEquipo'];
    $numControl = $_POST['numControl'];
    $nombre     = trim($_POST['nombre']);
    $apPat      = trim($_POST['apPat']);
    $apMat      = trim($_POST['apMat']);
    $edad       = $_POST['edad'];
    $sexo       = $_POST['sexo'];

    // Validaciones básicas
    if(empty($nombre) || empty($idEquipo) || empty($numControl)){
         header("Location: ../views/dashboards/asistenteDashboard.php?error=DatosIncompletos");
         exit;
    }

    // Llamar al modelo general de procesos
    $resultado = ModeloProcesos::registrarParticipante($numControl, $nombre, $apPat, $apMat, $edad, $sexo, $idEquipo);

    // Redirigir con mensaje
    header("Location: ../views/dashboards/asistenteDashboard.php?msg=" . urlencode($resultado));
    exit;

} else {
    header("Location: ../views/login/login_unificado.php");
    exit;
}
?>