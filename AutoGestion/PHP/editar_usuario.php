<?php
require 'conexion.php';

$id = $_POST['id'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$correo = $_POST['correo'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

$sql = "UPDATE usuarios SET nombre=?, correo=?, contrasena=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $nombre, $correo, $contrasena, $id);

if ($stmt->execute()) {
    echo "Usuario editado";
} else {
    echo "Error al editar usuario";
}

$stmt->close();
$conn->close();
?>
