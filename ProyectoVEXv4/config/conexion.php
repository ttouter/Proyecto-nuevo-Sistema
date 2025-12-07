<?php
$host = "localhost";
$port = "3306"; 
$dbname = "BaseDatosVex";
$user = "root";
$pass = ""; 

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si falla, detenemos todo y mostramos el error claro
    die("❌ Error Crítico de Conexión: " . $e->getMessage());
}
?>
