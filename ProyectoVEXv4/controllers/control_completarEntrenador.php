<?php
session_start();
require_once '../models/ModeloProcesos.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAsistente = $_POST['idAsistente'];
    $codEscuela  = $_POST['codEscuela'];

    $mensaje = ModeloProcesos::altaDetallesEntrenador($idAsistente, $codEscuela);

    // Redirigir con alerta (puedes mejorar esto pasando el mensaje por URL)
    echo "<script>
            alert('$mensaje');
            window.location.href = '../views/dashboards/asistenteDashboard.php#completar-registro';
          </script>";
}
?>