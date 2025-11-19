<?php
require 'conexion.php';

$estadisticas = [
  'productos' => [],
  'masVendido' => null,
  'menosVendido' => null,
  'sinVentas' => null,
  'totalVentas' => 0
];

// Consulta todos los productos
$sql = "SELECT p.id, p.nombre, IFNULL(SUM(dp.cantidad), 0) AS cantidad_vendida
        FROM productos p
        LEFT JOIN detalle_pedido dp ON p.id = dp.producto_id
        GROUP BY p.id";
$resultado = $conn->query($sql);

$maxCantidad = -1;
$minCantidad = PHP_INT_MAX;
$sinVentas = [];
$productos = [];

$totalVentas = 0;
$masVendido = '';
$menosVendido = '';

while ($row = $resultado->fetch_assoc()) {
  $nombre = $row['nombre'];
  $cantidad = intval($row['cantidad_vendida']);
  $productos[] = ['nombre' => $nombre, 'cantidad' => $cantidad];

  if ($cantidad > $maxCantidad) {
    $maxCantidad = $cantidad;
    $masVendido = $nombre;
  }

  if ($cantidad < $minCantidad) {
    $minCantidad = $cantidad;
    $menosVendido = $nombre;
  }

  if ($cantidad == 0) {
    $sinVentas[] = $nombre;
  }
}

// Calcular total en pesos desde los productos vendidos
$sqlTotal = "SELECT SUM(dp.cantidad * p.precio) AS total
             FROM detalle_pedido dp
             JOIN productos p ON dp.producto_id = p.id";
$resTotal = $conn->query($sqlTotal);
$totalVentas = $resTotal->fetch_assoc()['total'] ?? 0;

$estadisticas['productos'] = $productos;
$estadisticas['masVendido'] = $masVendido;
$estadisticas['menosVendido'] = $menosVendido;
$estadisticas['sinVentas'] = implode(', ', $sinVentas);
$estadisticas['totalVentas'] = floatval($totalVentas);

header('Content-Type: application/json');
echo json_encode($estadisticas);
