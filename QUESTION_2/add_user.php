<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_completo'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre_completo, correo, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $correo, $password, $rol);
// Después de insertar el usuario
$desc = "El usuario {$_SESSION['usuario']['nombre']} creó el usuario $nombre_completo";
$uid = $_SESSION['usuario']['id']; // o el que corresponda
$conn->query("INSERT INTO actividad (usuario_id, tipo, descripcion) VALUES ($uid, 'usuario', '$desc')");

    if ($stmt->execute()) {
        header("Location: dashboard.php?user_added=1");
    } else {
        header("Location: dashboard.php?user_added=0");
    }
    exit;
}
?>
