<?php
require 'conexion.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=productos.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Precio</th>
        <th>Stock</th>
      </tr>";

$resultado = $conn->query("SELECT nombre, descripcion, precio, stock FROM productos");

while ($fila = $resultado->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
    echo "<td>" . htmlspecialchars($fila['descripcion']) . "</td>";
    echo "<td>" . number_format($fila['precio'], 2) . "</td>";
    echo "<td>" . $fila['stock'] . "</td>";
    echo "</tr>";
}

echo "</table>";
?>
