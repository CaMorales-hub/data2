<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $nombre = $_POST['nombre'];
  $correo = $_POST['correo'];
  $contrasena = $_POST['contrasena'];

  $sql = "UPDATE usuarios SET nombre = ?, correo = ?, contrasena = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssi", $nombre, $correo, $contrasena, $id);

  if ($stmt->execute()) {
    header("Location: ../HTML/administrador.php?editado=ok");
  } else {
    echo "Error al actualizar";
  }

  $stmt->close();
  $conn->close();
}
?>
