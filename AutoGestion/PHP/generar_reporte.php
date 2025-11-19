
<?php
require 'conexion.php';

// Determinar tipo de reporte
$tipo = $_GET['tipo'] ?? '';

require '../librerias/fpdf/fpdf.php';
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Reporte - AutoGestionMX',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Ln(10);

switch($tipo) {
    case 'usuarios':
        $pdf->Cell(0,10,'Usuarios Registrados',0,1,'L');
        $pdf->Ln(5);
        $sql = "SELECT nombre, correo, creado_en FROM usuarios";
        $res = $conn->query($sql);
        while($row = $res->fetch_assoc()) {
            $pdf->Cell(0,10,"{$row['nombre']} - {$row['correo']} - {$row['creado_en']}",0,1);
        }
        break;

    case 'citas_pendientes':
        $pdf->Cell(0,10,'Citas Pendientes',0,1,'L');
        $pdf->Ln(5);
        $sql = "SELECT u.nombre, c.fecha_cita, c.hora FROM citas c
                JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.estado = 'pendiente'";
        $res = $conn->query($sql);
        while($row = $res->fetch_assoc()) {
            $pdf->Cell(0,10,"{$row['nombre']} - {$row['fecha_cita']} {$row['hora']}",0,1);
        }
        break;

    case 'citas_programadas':
        $pdf->Cell(0,10,'Citas Programadas',0,1,'L');
        $pdf->Ln(5);
        $sql = "SELECT u.nombre, c.fecha_cita, c.hora FROM citas c
                JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.estado = 'confirmada' AND c.fecha_cita > CURDATE()";
        $res = $conn->query($sql);
        while($row = $res->fetch_assoc()) {
            $pdf->Cell(0,10,"{$row['nombre']} - {$row['fecha_cita']} {$row['hora']}",0,1);
        }
        break;

    case 'cotizaciones_pendientes':
        $pdf->Cell(0,10,'Cotizaciones Sin Confirmar',0,1,'L');
        $pdf->Ln(5);
        $sql = "SELECT u.nombre, c.servicio, c.descripcion, c.fecha FROM cotizaciones c
                JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.estado = 'pendiente'";
        $res = $conn->query($sql);
        while($row = $res->fetch_assoc()) {
            $pdf->MultiCell(0,10,"{$row['nombre']} - {$row['servicio']} - {$row['descripcion']} - {$row['fecha']}",0,1);
            $pdf->Ln(2);
        }
        break;

    default:
        $pdf->Cell(0,10,'Tipo de reporte no válido.',0,1);
        break;
}

$pdf->Output('I', 'reporte.pdf');
?>
