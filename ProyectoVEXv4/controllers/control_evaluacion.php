<?php
session_start();
require_once '../models/ModeloEvaluacion.php';

// Verificar sesión de juez (ajusta según tu variable de sesión real)
$idJuez = $_SESSION['juez_id'] ?? 1; // ID 1 temporal si no hay sesión para pruebas

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recolectar todos los datos del formulario
    $datos = [
        'idJuez'   => $idJuez,
        'idEquipo' => $_POST['idEquipo'],
        
        // Diseño
        'registroDeFechas'      => $_POST['registroDeFechas'] ?? 0,
        'justificacionDeCambios'=> $_POST['justificacionDeCambios'] ?? 0,
        'diagramasEImagenes'    => $_POST['diagramasEImagenes'] ?? 0,
        'videoYAnimacion'       => $_POST['videoYAnimacion'] ?? 0,
        'disenoYModelado'       => $_POST['disenoYModelado'] ?? 0,

        // Programación
        'usoFunciones'    => $_POST['usoFunciones'] ?? 0,
        'complejidad'     => $_POST['complejidad'] ?? 0,
        'codigoModular'   => $_POST['codigoModular'] ?? 0,
        'sistemaAutonomo' => $_POST['sistemaAutonomo'] ?? 0,
        'controlDriver'   => $_POST['controlDriver'] ?? 0,

        // Construcción
        'estetica'    => $_POST['estetica'] ?? 0,
        'estabilidad' => $_POST['estabilidad'] ?? 0,
        'cableado'    => $_POST['cableado'] ?? 0,
        'sensores'    => $_POST['sensores'] ?? 0,
        'engranes'    => $_POST['engranes'] ?? 0
    ];

    // Llamar al modelo
    $mensaje = ModeloEvaluacion::guardarEvaluacionCompleta($datos);

    // Redirigir de vuelta al dashboard
    echo "<script>
            alert('$mensaje');
            window.location.href = '../views/dashboards/juezDashboard.php';
          </script>";
}
?>