<?php
require 'conexion.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=cotizaciones.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr>
        <th>Usuario</th>
        <th>Archivo</th>
        <th>Estado</th>
        <th>Fecha</th>
      </tr>";

$sql = "SELECT c.*, u.nombre AS usuario
        FROM cotizaciones c
        JOIN usuarios u ON c.usuario_id = u.id
        ORDER BY c.creado_en DESC";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['usuario']) . "</td>";
    echo "<td>" . htmlspecialchars($row['archivo_pdf']) . "</td>";
    echo "<td>" . htmlspecialchars($row['estado']) . "</td>";
    echo "<td>" . htmlspecialchars($row['creado_en']) . "</td>";
    echo "</tr>";
}

echo "</table>";
$conn->close();
?>
