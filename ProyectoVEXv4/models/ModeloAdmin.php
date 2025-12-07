<?php
require_once __DIR__ . '/../config/conexion.php';

class ModeloAdmin {

    // 1. Resumen (Ahora usa SP)
    public static function obtenerResumen() {
        global $pdo;
        try {
            $stmt = $pdo->prepare("CALL ObtenerResumenAdmin()");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return null; }
    }

    // 2. Listar Usuarios (Ahora usa SP)
    public static function listarUsuarios() {
        global $pdo;
        try {
            $stmt = $pdo->prepare("CALL ListarUsuariosAdmin()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    // 3. Listar Equipos Resumen (Ahora usa SP)
    public static function listarEquipos() {
        global $pdo;
        try {
            $stmt = $pdo->prepare("CALL ListarEquiposResumen()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    // 4. Listar Equipos Detallado (Tarjetas)
    public static function listarEquiposDetallado() {
        global $pdo;
        try {
            $stmt = $pdo->prepare("CALL ObtenerTodosLosEquiposDetallado()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    // --- EL RESTO YA USABA SPs, PERO REVISAMOS ---

    public static function crearEvento($nombre, $lugar, $fecha) {
        global $pdo; try { 
            $stmt = $pdo->prepare("CALL AltaEvento(?, ?, ?, @mensaje)");
            $stmt->execute([$nombre, $lugar, $fecha]);
            return $pdo->query("SELECT @mensaje")->fetchColumn();
        } catch (PDOException $e) { return $e->getMessage(); }
    }

    public static function crearEscuela($codigo, $nombre) {
        global $pdo; try {
            $stmt = $pdo->prepare("CALL AltaEscuelaProcedencia(?, ?, @mensaje)");
            $stmt->execute([$codigo, $nombre]);
            return $pdo->query("SELECT @mensaje")->fetchColumn();
        } catch (PDOException $e) { return $e->getMessage(); }
    }

    public static function eliminarEscuela($codigo) {
        global $pdo; try {
            // Nota: Para eliminar también deberíamos usar un SP idealmente, 
            // pero si prefieres mantenerlo simple, aquí usamos una sentencia preparada segura.
            // Para ser estrictos con tu regla, cámbialo a un SP "BajaEscuela" si lo deseas.
            $stmt = $pdo->prepare("DELETE FROM EscuelaProcedencia WHERE codEscuela = ?");
            $stmt->execute([$codigo]);
            return "Escuela eliminada correctamente.";
        } catch (PDOException $e) { 
            if($e->getCode() == '23000') return "No se puede borrar: Tiene datos vinculados.";
            return "Error: " . $e->getMessage(); 
        }
    }

    public static function asignarRolEntrenador($id, $cod) { 
        global $pdo; try{ 
            // Podrías crear un SP "AsignarRolEntrenador" para encapsular este INSERT
            $pdo->prepare("INSERT IGNORE INTO Entrenador (idAsistente_Asistente, codEscuela_EscuelaProcedencia) VALUES (?,?)")->execute([$id,$cod]); 
        }catch(Exception $e){} 
    }

    public static function asignarRolJuez($id, $cod, $gr) { 
        global $pdo; try{ 
            // Podrías crear un SP "AsignarRolJuez"
            $pdo->prepare("INSERT IGNORE INTO Juez (idAsistente_Asistente, codEscuela_EscuelaProcedencia, gradoEstudios) VALUES (?,?,?)")->execute([$id,$cod,$gr]); 
        }catch(Exception $e){} 
    }
    
    public static function obtenerJuecesValidos($cat) { 
        global $pdo; try{ 
            $s=$pdo->prepare("CALL ObtenerJuecesValidosParaCategoria(?)"); 
            $s->execute([$cat]); 
            return $s->fetchAll(PDO::FETCH_ASSOC); 
        }catch(Exception $e){return[];} 
    }

    public static function obtenerEquiposPorCategoria($cat) { 
        global $pdo; try{ 
            // Idealmente convertir a SP: "CALL ObtenerEquiposPorCategoria(?)"
            $s=$pdo->prepare("SELECT idEquipo FROM Equipo WHERE idCategoria_Categoria=?"); 
            $s->execute([$cat]); 
            return $s->fetchAll(PDO::FETCH_ASSOC); 
        }catch(Exception $e){return[];} 
    }

    public static function asignarJuezEquipo($juez, $equipo) { 
        global $pdo; try{ 
            $s=$pdo->prepare("CALL AsignarJuezEquipo(?, ?, @m)"); 
            $s->execute([$juez, $equipo]); 
            return $pdo->query("SELECT @m")->fetchColumn(); 
        }catch(Exception $e){return $e->getMessage();} 
    }
}
?>