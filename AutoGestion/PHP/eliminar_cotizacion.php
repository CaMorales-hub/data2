<?php
require 'conexion.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo 'ID no proporcionado';
    exit;
}

$id = intval($_GET['id']);

// Primero obtenemos el archivo para eliminarlo del servidor
$sql = "SELECT archivo_pdf FROM cotizaciones WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($archivo_pdf);
$stmt->fetch();
$stmt->close();

if ($archivo_pdf && file_exists("../archivos/$archivo_pdf")) {
    unlink("../archivos/$archivo_pdf");
}

// Eliminar de la base de datos
$sql = "DELETE FROM cotizaciones WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "ok";
} else {
    http_response_code(500);
    echo "Error al eliminar";
}

$conn->close();
?>
