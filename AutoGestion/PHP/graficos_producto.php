<?php
require 'conexion.php';

$sql = "SELECT p.nombre, SUM(dp.cantidad) AS total_vendidos
        FROM productos p
        LEFT JOIN detalle_pedido dp ON p.id = dp.producto_id
        GROUP BY p.id";

$resultado = $conn->query($sql);

$datos = [];
$total = 0;
$mayor = ['nombre' => '', 'valor' => 0];
$menor = ['nombre' => '', 'valor' => PHP_INT_MAX];

while ($fila = $resultado->fetch_assoc()) {
  $nombre = $fila['nombre'];
  $vendidos = $fila['total_vendidos'] ?? 0;
  $datos[] = ['nombre' => $nombre, 'vendidos' => $vendidos];
  $total += $vendidos;

  if ($vendidos > $mayor['valor']) {
    $mayor = ['nombre' => $nombre, 'valor' => $vendidos];
  }

  if ($vendidos < $menor['valor']) {
    $menor = ['nombre' => $nombre, 'valor' => $vendidos];
  }
}

echo json_encode([
  'datos' => $datos,
  'total' => $total,
  'mas_vendido' => $mayor,
  'menos_vendido' => $menor
]);
?>
