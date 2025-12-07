<?php
require_once __DIR__ . '/../config/conexion.php';

class ModeloProcesos {
    
    // Listar Escuelas para <select>
    public static function listarEscuelas() {
        global $pdo;
        try {
            $stmt = $pdo->prepare("CALL ListarEscuelas()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    // Listar Eventos para <select>
    public static function listarEventos() {
        global $pdo;
        try {
            $stmt = $pdo->prepare("CALL ListarEventos()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    // Listar Categorías
    public static function listarCategorias() {
        global $pdo;
        try {
            $stmt = $pdo->prepare("CALL ListarCategorias()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    // Obtener info básica de un equipo (Header de evaluación)
    public static function obtenerInfoEquipo($idEquipo) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("CALL ObtenerInfoEquipo(?)");
            $stmt->execute([$idEquipo]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return null; }
    }

    // Registrar un Equipo Nuevo
    public static function registrarEquipo($nombre, $cat, $escuela, $evento, $idAsistente) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("CALL AltaEquipo(?, ?, ?, ?, ?, @mensaje, @idOut)");
            $stmt->execute([$nombre, $cat, $escuela, $evento, $idAsistente]);
            
            $res = $pdo->query("SELECT @mensaje as msg, @idOut as id")->fetch();
            return ['status' => true, 'mensaje' => $res['msg'], 'id' => $res['id']];
        } catch (PDOException $e) { 
            return ['status' => false, 'mensaje' => $e->getMessage()]; 
        }
    }

    // Registrar un Participante
    public static function registrarParticipante($numControl, $nombre, $apat, $amat, $edad, $sexo, $idEquipo) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("CALL AltaParticipante(?, ?, ?, ?, ?, ?, ?, @mensaje)");
            $stmt->execute([$numControl, $nombre, $apat, $amat, $edad, $sexo, $idEquipo]);
            
            $res = $pdo->query("SELECT @mensaje")->fetch();
            return $res['@mensaje'];
        } catch (PDOException $e) { return $e->getMessage(); }
    }
}
?>