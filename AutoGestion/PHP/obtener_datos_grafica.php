<?php
require 'conexion.php';

// Más vendido
$mas = $conn->query("
  SELECT p.id, p.nombre, SUM(dp.cantidad) AS cantidad 
  FROM productos p
  JOIN detalle_pedido dp ON p.id = dp.producto_id
  GROUP BY p.id
  ORDER BY cantidad DESC
  LIMIT 1
")->fetch_assoc();

// Menos vendido
$menos = $conn->query("
  SELECT p.id, p.nombre, SUM(dp.cantidad) AS cantidad 
  FROM productos p
  JOIN detalle_pedido dp ON p.id = dp.producto_id
  GROUP BY p.id
  ORDER BY cantidad ASC
  LIMIT 1
")->fetch_assoc();

// Sin ventas
$sinVentas = $conn->query("
  SELECT id, nombre, stock FROM productos 
  WHERE id NOT IN (SELECT DISTINCT producto_id FROM detalle_pedido)
");

$sinVentasArray = [];
while ($row = $sinVentas->fetch_assoc()) {
  $sinVentasArray[] = $row;
}

// Todos los productos con sus ventas
$todos = $conn->query("
  SELECT p.id, p.nombre, IFNULL(SUM(dp.cantidad), 0) AS cantidad
  FROM productos p
  LEFT JOIN detalle_pedido dp ON p.id = dp.producto_id
  GROUP BY p.id
");

$todosArray = [];
$totalVentas = 0;
$totalVentasDinero = 0;
while ($row = $todos->fetch_assoc()) {
  $todosArray[] = $row;
  $totalVentas += intval($row['cantidad']);

  // Obtener precio individual del producto
  $precio = $conn->query("SELECT precio FROM productos WHERE id = " . intval($row['id']))->fetch_assoc();
  $totalVentasDinero += $row['cantidad'] * floatval($precio['precio']);
}

// Historial simple (puedes personalizar)
$historial = $todosArray;

echo json_encode([
  "masVendido" => $mas,
  "menosVendido" => $menos,
  "sinVentas" => $sinVentasArray,
  "todos" => $todosArray,
  "totalVentas" => $totalVentas,
  "totalVentasDinero" => $totalVentasDinero,
  "historial" => $historial
]);
?>
