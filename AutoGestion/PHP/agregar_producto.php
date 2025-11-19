<?php
require 'conexion.php';

$nombre = $_POST['nombre'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$precio = floatval($_POST['precio'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);

// Subir imagen
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
    $imgNombre = $_FILES['imagen']['name'];
    $imgTemp = $_FILES['imagen']['tmp_name'];
    $imgRuta = '../IMG/' . $imgNombre;

    if (!move_uploaded_file($imgTemp, $imgRuta)) {
        echo "Error al subir imagen";
        exit;
    }
} else {
    echo "Imagen no válida";
    exit;
}

$stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdss", $nombre, $descripcion, $precio, $stock, $imgNombre);

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "error";
}
?>
