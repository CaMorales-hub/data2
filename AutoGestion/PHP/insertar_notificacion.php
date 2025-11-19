<?php
require 'conexion.php';

$receptor_id = $_POST['receptor_id'];
$receptor_tipo = $_POST['receptor_tipo']; // 'cliente' o 'personal'
$mensaje = $_POST['mensaje'];
$usuario_id = $_POST['usuario_id']; // quien genera la notificación (cliente o personal)

$stmt = $conn->prepare("INSERT INTO notificaciones (receptor_id, receptor_tipo, mensaje, leido, usuario_id, creado_en) VALUES (?, ?, ?, 0, ?, NOW())");
$stmt->bind_param("issi", $receptor_id, $receptor_tipo, $mensaje, $usuario_id);
$stmt->execute();

echo "ok";
?>
