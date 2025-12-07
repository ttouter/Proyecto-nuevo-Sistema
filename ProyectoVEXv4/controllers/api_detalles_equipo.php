<?php
require_once '../models/ModeloEntrenador.php';
require_once '../config/conexion.php'; // Aseguramos conexión por si acaso

if(isset($_GET['id'])) {
    $idEquipo = $_GET['id'];
    
    // 1. Obtener Integrantes
    // Nota: ModeloEntrenador ya tiene este método preparado en tu código actual.
    // Asegúrate de haber corrido el SQL del "Paso 1" para que el SP exista.
    $integrantes = ModeloEntrenador::obtenerIntegrantes($idEquipo);
    
    // 2. Obtener Estado de Evaluación
    // Hacemos una llamada directa rápida para ver si ya calificaron los jueces
    global $pdo;
    try {
        $stmt = $pdo->prepare("CALL ObtenerEstadoEvaluacion(?)");
        $stmt->execute([$idEquipo]);
        $evaluacion = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Si falla (por ejemplo si no has corrido el SQL), devolvemos ceros para no romper el JSON
        $evaluacion = ['eval_diseno' => 0, 'eval_prog' => 0, 'eval_const' => 0];
    }
    
    // Devolver todo en JSON para que Javascript lo pinte
    header('Content-Type: application/json');
    echo json_encode([
        'integrantes' => $integrantes,
        'evaluacion' => $evaluacion
    ]);
}
?>