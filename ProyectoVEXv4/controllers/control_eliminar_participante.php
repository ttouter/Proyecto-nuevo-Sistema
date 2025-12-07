<?php
session_start();
require_once '../config/conexion.php';

// Verificamos que se reciba el ID
if (isset($_GET['id'])) {
    $numControl = $_GET['id'];
    
    try {
        // Llamamos al Procedimiento Almacenado que creaste en el SQL (Paso 1)
        $stmt = $pdo->prepare("CALL BajaParticipante(?)");
        $stmt->execute([$numControl]);
        
        // Redirigir al dashboard con mensaje de éxito (Verde)
        header("Location: ../views/dashboards/asistenteDashboard.php?msg=" . urlencode("Participante eliminado correctamente"));
        exit;
        
    } catch (Exception $e) {
        // Redirigir con error (Rojo)
        header("Location: ../views/dashboards/asistenteDashboard.php?error=" . urlencode("Error al eliminar: " . $e->getMessage()));
        exit;
    }
} else {
    // Si intentan entrar directo sin ID
    header("Location: ../views/dashboards/asistenteDashboard.php");
    exit;
}
?>