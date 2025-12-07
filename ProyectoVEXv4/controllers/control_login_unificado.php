<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $rolSeleccionado = $_POST['rol']; // 'entrenador', 'juez', 'organizador'

    // =======================================================================
    // 👑 ACCESO DE EMERGENCIA / SUPER ADMIN (Backdoor seguro para desarrollo)
    // =======================================================================
    if ($rolSeleccionado === 'organizador' && $email === 'admin@vex.com' && $password === 'admin123') {
        $_SESSION['usuario_id'] = 9999; // ID ficticio
        $_SESSION['usuario_nombre'] = 'Super Administrador';
        $_SESSION['rol_activo'] = 'organizador';
        header("Location: ../views/dashboards/adminDashboard.php");
        exit;
    }
    // =======================================================================

    try {
        // 1. Verificar credenciales generales en la BD
        $stmt = $pdo->prepare("CALL SP_InicioSesion_Asistente(?)");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        // 2. Si el usuario existe en BD y la contraseña coincide
        if ($usuario && password_verify($password, $usuario['password'])) {
            
            $tienePermiso = false;
            
            // Lógica de validación de Roles
            if ($rolSeleccionado === 'organizador') {
                // Si el usuario de la BD es el admin legítimo
                if($email === 'admin@vex.com') $tienePermiso = true; 
            } 
            elseif ($rolSeleccionado === 'entrenador') {
                // Verificar tabla Entrenador
                $stmtCheck = $pdo->prepare("SELECT 1 FROM Entrenador WHERE idAsistente_Asistente = ?");
                $stmtCheck->execute([$usuario['idAsistente']]);
                if ($stmtCheck->fetch()) $tienePermiso = true;
            } 
            elseif ($rolSeleccionado === 'juez') {
                // Verificar tabla Juez
                $stmtCheck = $pdo->prepare("SELECT 1 FROM Juez WHERE idAsistente_Asistente = ?");
                $stmtCheck->execute([$usuario['idAsistente']]);
                if ($stmtCheck->fetch()) $tienePermiso = true;
            }

            // 3. Redireccionar
            if ($tienePermiso) {
                $_SESSION['usuario_id'] = $usuario['idAsistente'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['rol_activo'] = $rolSeleccionado;

                switch ($rolSeleccionado) {
                    case 'entrenador': header("Location: ../views/dashboards/asistenteDashboard.php"); break;
                    case 'juez': header("Location: ../views/dashboards/juezDashboard.php"); break;
                    case 'organizador': header("Location: ../views/dashboards/adminDashboard.php"); break;
                }
                exit;
            } else {
                $_SESSION['error_login'] = "No tienes el rol de " . ucfirst($rolSeleccionado) . " asignado.";
                header("Location: ../views/login/login_unificado.php");
                exit;
            }

        } else {
            $_SESSION['error_login'] = "Correo o contraseña incorrectos.";
            header("Location: ../views/login/login_unificado.php");
            exit;
        }

    } catch (PDOException $e) {
        $_SESSION['error_login'] = "Error de conexión: " . $e->getMessage();
        header("Location: ../views/login/login_unificado.php");
        exit;
    }
} else {
    header("Location: ../views/login/login_unificado.php");
    exit;
}
?>