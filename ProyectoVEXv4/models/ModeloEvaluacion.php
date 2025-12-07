<?php
require_once __DIR__ . '/../config/conexion.php';

class ModeloEvaluacion {

    // 1. Obtener los equipos que este Juez debe evaluar
    public static function obtenerEquiposAsignados($idJuez) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("CALL ObtenerEquiposPorJuez(?)");
            $stmt->execute([$idJuez]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // 2. Guardar Evaluación de DISEÑO
    public static function guardarDiseno($idJuez, $idEquipo, $datos) {
        global $pdo;
        try {
            $sql = "CALL AltaEvaluacionDiseno(?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $idJuez, $idEquipo, 
                $datos['fechas'], $datos['justificacion'], $datos['diagramas'], 
                $datos['video'], $datos['modelado']
            ]);
            return $pdo->query("SELECT @mensaje")->fetchColumn();
        } catch (PDOException $e) { return "Error: " . $e->getMessage(); }
    }

    // 3. Guardar Evaluación de PROGRAMACIÓN
    public static function guardarProgramacion($idJuez, $idEquipo, $datos) {
        global $pdo;
        try {
            $sql = "CALL AltaEvaluacionProgramacion(?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $idJuez, $idEquipo,
                $datos['funciones'], $datos['complejidad'], $datos['modular'], 
                $datos['autonomo'], $datos['joystick']
            ]);
            return $pdo->query("SELECT @mensaje")->fetchColumn();
        } catch (PDOException $e) { return "Error: " . $e->getMessage(); }
    }

    // 4. Guardar Evaluación de CONSTRUCCIÓN
    public static function guardarConstruccion($idJuez, $idEquipo, $datos) {
        global $pdo;
        try {
            $sql = "CALL AltaEvaluacionConstruccion(?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $idJuez, $idEquipo,
                $datos['estetica'], $datos['estabilidad'], $datos['cableado'], 
                $datos['sensores'], $datos['engranes']
            ]);
            return $pdo->query("SELECT @mensaje")->fetchColumn();
        } catch (PDOException $e) { return "Error: " . $e->getMessage(); }
    }
}
?>