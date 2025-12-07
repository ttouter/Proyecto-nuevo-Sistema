<?php
session_start();
require_once '../models/ModeloAdmin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAsistente = $_POST['idAsistente'];
    $tipoRol     = $_POST['tipoRol']; // 'entrenador' o 'juez'
    $codEscuela  = $_POST['codEscuela'];
    
    $mensaje = "";

    if ($tipoRol === 'entrenador') {
        $mensaje = ModeloAdmin::asignarRolEntrenador($idAsistente, $codEscuela);
    } 
    elseif ($tipoRol === 'juez') {
        $grado = $_POST['gradoEstudios'];
        $mensaje = ModeloAdmin::asignarRolJuez($idAsistente, $codEscuela, $grado);
    }

    echo "<script>
            alert('$mensaje');
            window.location.href = '../views/dashboards/adminDashboard.php#usuarios';
          </script>";
}
?>