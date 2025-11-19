<?php
require 'conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM usuarios WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Usuario eliminado";
    } else {
        echo "Error al eliminar usuario";
    }

    $stmt->close();
}

$conn->close();
?>
