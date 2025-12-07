<?php
session_start();

// 1. Vaciar la sesión
$_SESSION = [];

// 2. Destruir la sesión completamente
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// 3. REDIRIGIR AL INDEX (Landing Page)
// Salimos de "login" (../) -> entramos a "index" -> archivo "Index.html"
header("Location: ../index/Index.html");
exit;
?>