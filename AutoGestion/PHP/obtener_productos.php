<?php
require 'conexion.php';

header('Content-Type: application/json');

$result = $conn->query("SELECT * FROM productos");

$productos = [];

while ($row = $result->fetch_assoc()) {
    $productos[] = [
        'id' => $row['id'],
        'nombre' => $row['nombre'],
        'descripcion' => $row['descripcion'],
        'precio' => floatval($row['precio']),
        'stock' => intval($row['stock']),
        'imagen' => $row['imagen'],
        'tipo' => $row['tipo']
    ];
}

echo json_encode($productos);
?>
