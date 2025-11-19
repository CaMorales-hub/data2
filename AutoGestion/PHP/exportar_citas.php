<?php
require 'conexion.php';
require 'fpdf/fpdf.php';

$filtro = $_GET['filtro'] ?? 'todas';

// Aplicar filtro
$where = "";
if ($filtro === 'confirmadas') {
    $where = "WHERE c.estado = 'confirmada'";
} elseif ($filtro === 'pendientes') {
    $where = "WHERE c.estado = 'pendiente'";
}

$query = "SELECT c.id, u.nombre AS usuario, c.fecha, c.hora, c.estado 
          FROM citas c 
          JOIN usuarios u ON c.usuario_id = u.id
          $where 
          ORDER BY c.fecha DESC";
$result = $conn->query($query);

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Reporte de Citas (' . ucfirst($filtro) . ')', 0, 1, 'C');
$pdf->Ln(5);

// Encabezados
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Usuario', 1);
$pdf->Cell(40, 10, 'Fecha', 1);
$pdf->Cell(30, 10, 'Hora', 1);
$pdf->Cell(40, 10, 'Estado', 1);
$pdf->Ln();

// Contenido
$pdf->SetFont('Arial', '', 11);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(60, 10, utf8_decode($row['usuario']), 1);
    $pdf->Cell(40, 10, $row['fecha'], 1);
    $pdf->Cell(30, 10, $row['hora'], 1);
    $pdf->Cell(40, 10, ucfirst($row['estado']), 1);
    $pdf->Ln();
}

// Nombre del archivo
$nombreArchivo = "citas_" . $filtro . "_" . date("Ymd_His") . ".pdf";
$pdf->Output('D', $nombreArchivo);
?>
