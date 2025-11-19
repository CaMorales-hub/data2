<?php
require 'conexion.php';

// Obtener el ID enviado por POST
$producto_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validar
if ($producto_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit;
}

// Paso 1: Eliminar primero de detalle_pedido
$stmt1 = $conn->prepare("DELETE FROM detalle_pedido WHERE producto_id = ?");
$stmt1->bind_param("i", $producto_id);
$stmt1->execute();

// Paso 2: Luego eliminar de productos
$stmt2 = $conn->prepare("DELETE FROM productos WHERE id = ?");
$stmt2->bind_param("i", $producto_id);

if ($stmt2->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'No se pudo eliminar']);
}
?>
