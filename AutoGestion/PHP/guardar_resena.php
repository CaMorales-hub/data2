<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['cliente_id'])) {
    http_response_code(401);
    echo "No autenticado";
    exit;
}

$cliente_id = $_SESSION['cliente_id'];

// Validar texto
if (!isset($_POST['texto']) || trim($_POST['texto']) === "") {
    http_response_code(400);
    echo "Texto vacío";
    exit;
}

$texto = trim($_POST['texto']);

// Obtener el nombre del cliente
$stmt = $conn->prepare("SELECT nombre FROM usuarios WHERE id = ?");
if (!$stmt) {
    echo "Error al preparar SELECT: " . $conn->error;
    exit;
}

$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$stmt->bind_result($nombre_cliente);
$stmt->fetch();
$stmt->close();

if (empty($nombre_cliente)) {
    echo "No se encontró el nombre del cliente";
    exit;
}

// Insertar la reseña
$stmt = $conn->prepare("INSERT INTO reseñas (nombre_cliente, texto, fecha) VALUES (?, ?, NOW())");
if (!$stmt) {
    echo "Error al preparar INSERT: " . $conn->error;
    exit;
}

$stmt->bind_param("ss", $nombre_cliente, $texto);
if ($stmt->execute()) {
    echo "ok";
} else {
    echo "Error al insertar: " . $stmt->error;
}
$stmt->close();
