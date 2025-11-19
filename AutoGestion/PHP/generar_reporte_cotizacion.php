<?php
require '../PHP/conexion.php';
require 'fpdf/fpdf.php';

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'todas';

$sql = "SELECT c.*, u.nombre FROM cotizaciones c 
        JOIN usuarios u ON c.usuario_id = u.id";

if ($tipo === 'pendientes') {
    $sql .= " WHERE c.estado = 'pendiente'";
} elseif ($tipo === 'respondidas') {
    $sql .= " WHERE c.estado = 'confirmada'";
}

$sql .= " ORDER BY c.creado_en DESC";
$result = $conn->query($sql);

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Reporte de Cotizaciones (' . ucfirst($tipo) . ')', 0, 1, 'C');
$pdf->Ln(5);

// Encabezados
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Usuario', 1);
$pdf->Cell(50, 10, 'Estado', 1);
$pdf->Cell(70, 10, 'Fecha', 1);
$pdf->Ln();

// Contenido
$pdf->SetFont('Arial', '', 12);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(60, 10, utf8_decode($row['nombre']), 1);
    $pdf->Cell(50, 10, ucfirst($row['estado']), 1);
    $pdf->Cell(70, 10, $row['creado_en'], 1);
    $pdf->Ln();
}

$pdf->Output("D", "reporte_cotizaciones_$tipo.pdf");
exit;
