<?php
require 'conexion.php';

// Verifica que se enviaron todos los campos necesarios
if (
    isset($_POST['id']) &&
    isset($_POST['nombre']) &&
    isset($_POST['descripcion']) &&
    isset($_POST['precio']) &&
    isset($_POST['stock'])
) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    // Prepara la sentencia SQL para actualizar el producto
    $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ? WHERE id = ?");
    $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $stock, $id);

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "Error al actualizar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Faltan datos para actualizar el producto.";
}
?>
