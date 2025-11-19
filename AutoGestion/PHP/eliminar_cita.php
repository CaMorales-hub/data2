<?php
require 'conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Preparar sentencia
    $stmt = $conn->prepare("DELETE FROM citas WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "Error al eliminar";
    }

    $stmt->close();
} else {
    echo "ID inválido";
}

$conn->close();
?>
