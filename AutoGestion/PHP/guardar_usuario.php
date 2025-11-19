<?php
require 'conexion.php';

$nombre = $_POST['nombre'] ?? '';
$correo = $_POST['correo'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

$sql = "INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nombre, $correo, $contrasena);

if ($stmt->execute()) {
    echo "Usuario guardado";
} else {
    echo "Error al guardar usuario";
}

$stmt->close();
$conn->close();
?>
