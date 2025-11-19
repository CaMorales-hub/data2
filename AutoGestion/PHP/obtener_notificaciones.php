<?php
session_start();
require 'conexion.php';

$tipo = $_GET['receptor_tipo'];

if ($tipo === 'cliente' && isset($_SESSION['cliente_id'])) {
    $id = $_SESSION['cliente_id'];
} elseif ($tipo === 'personal' && isset($_SESSION['personal_id'])) {
    $id = $_SESSION['personal_id'];
} else {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT id, mensaje FROM notificaciones WHERE receptor_id = ? AND receptor_tipo = ? AND leido = 0 ORDER BY creado_en DESC");
$stmt->bind_param("is", $id, $tipo);
$stmt->execute();
$result = $stmt->get_result();

$notificaciones = [];
while ($row = $result->fetch_assoc()) {
    $notificaciones[] = $row;
}

echo json_encode($notificaciones);
?>
