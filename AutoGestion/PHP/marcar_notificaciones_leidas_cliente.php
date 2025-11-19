<?php
session_start();
require 'conexion.php';

if (isset($_SESSION['cliente_id'])) {
    $id = $_SESSION['cliente_id'];
    $tipo = 'cliente';
} elseif (isset($_SESSION['personal_id'])) {
    $id = $_SESSION['personal_id'];
    $tipo = 'personal';
} else {
    http_response_code(403);
    echo "Sesión no válida";
    exit;
}

$stmt = $conn->prepare("UPDATE notificaciones SET leido = 1 WHERE receptor_id = ? AND receptor_tipo = ? AND leido = 0");
$stmt->bind_param("is", $id, $tipo);

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "error";
}
?>
