<?php
session_start();
require 'conexion.php';

// Detectar la sesión activa
if (isset($_SESSION['personal_id'])) {
    $id = $_SESSION['personal_id'];
    $tipo = 'personal';
} elseif (isset($_SESSION['cliente_id'])) {
    $id = $_SESSION['cliente_id'];
    $tipo = 'cliente';
} else {
    http_response_code(403);
    echo "Sesión no válida";
    exit;
}

// DEBUG opcional
// file_put_contents("debug_personal.txt", "ID: $id | Tipo: $tipo\n", FILE_APPEND);

// Ejecutar el UPDATE
$stmt = $conn->prepare("UPDATE notificaciones SET leido = 1 WHERE receptor_id = ? AND receptor_tipo = ? AND leido = 0");
$stmt->bind_param("is", $id, $tipo);

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "error al marcar";
}
?>
