<?php
session_start();
// Asegúrate de que tu archivo de conexión esté bien configurado con __DIR__
require_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Recibir y limpiar datos
    $nombre     = trim($_POST['nombre']);
    $ap_paterno = trim($_POST['ap_paterno']);
    $ap_materno = trim($_POST['ap_materno']);
    $sexo       = $_POST['sexo'];
    $codEscuela = $_POST['codEscuela']; // Recibimos el código de escuela
    $email      = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password   = $_POST['password'];

    // 2. Validaciones básicas
    if (empty($nombre) || empty($ap_paterno) || empty($ap_materno) || empty($email) || empty($password) || empty($codEscuela)) {
        header("Location: ../views/register/registro.php?error=Todos los campos son obligatorios");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../views/register/registro.php?error=Formato de correo inválido");
        exit();
    }

    // 3. Encriptar contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // 4. Llamar al Procedimiento (incluyendo :codEscuela)
        $sql = "CALL AltaAsistente(:nombre, :apPat, :apMat, :sexo, :email, :pass, :codEscuela, @mensaje)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apPat', $ap_paterno);
        $stmt->bindParam(':apMat', $ap_materno);
        $stmt->bindParam(':sexo', $sexo);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':pass', $passwordHash);
        $stmt->bindParam(':codEscuela', $codEscuela); // Bind del nuevo parámetro
        
        $stmt->execute();
        $stmt->closeCursor();

        // 5. Verificar respuesta de la BD
        $row = $pdo->query("SELECT @mensaje AS mensaje")->fetch(PDO::FETCH_ASSOC);
        $mensajeBD = $row['mensaje'];

        if ($mensajeBD === 'Registro exitoso') {
            // Éxito (Verde)
            header("Location: ../views/register/registro.php?success=¡Cuenta creada con éxito! Ya puedes iniciar sesión.");
            exit();
        } else {
            // Error (Rojo) - Ej: Correo duplicado
            header("Location: ../views/register/registro.php?error=" . urlencode($mensajeBD));
            exit();
        }

    } catch (PDOException $e) {
        header("Location: ../views/register/registro.php?error=Error en el servidor: " . $e->getMessage());
        exit();
    }

} else {
    // Si intentan entrar directo
    header("Location: ../views/register/registro.php");
    exit();
}
?>