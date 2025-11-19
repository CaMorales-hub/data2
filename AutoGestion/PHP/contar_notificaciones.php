<?php
session_start();
require 'conexion.php';

$tipo = $_GET['receptor_tipo'];

if ($tipo === 'cliente' && isset($_SESSION['cliente_id'])) {
    $id = $_SESSION['cliente_id'];
} elseif ($tipo === 'personal' && isset($_SESSION['personal_id'])) {
    $id = $_SESSION['personal_id'];
} else {
    echo 0;
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM notificaciones WHERE receptor_id = ? AND receptor_tipo = ? AND leido = 0");
$stmt->bind_param("is", $id, $tipo);
$stmt->execute();
$result = $stmt->get_result();
$total = $result->fetch_assoc()['total'];

echo $total;
?>
