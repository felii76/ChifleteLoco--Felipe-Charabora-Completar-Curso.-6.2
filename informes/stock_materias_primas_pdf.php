<?php
require("../fpdf/fpdf.php");
require_once("../conexion.php");

$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(190, 10, 'Informe de Stock de Materias Primas', 0, 1, 'C');

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(45, 10, 'Materia Prima', 1);
$pdf->Cell(30, 10, 'Unidad', 1);
$pdf->Cell(30, 10, 'Stock', 1);
$pdf->Cell(40, 10, 'Movimientos', 1);
$pdf->Cell(45, 10, 'Ultimo Movimiento', 1);

$pdf->Ln();

$sql = "SELECT
            materias_primas.nombre,
            materias_primas.unidad,
            materias_primas.stock,
            COUNT(movimientos_stock_materias.id_movimiento) AS cantidad_movimientos,
            MAX(movimientos_stock_materias.fecha) AS ultimo_movimiento

        FROM materias_primas

        LEFT JOIN movimientos_stock_materias
        ON materias_primas.id_materia_prima =
           movimientos_stock_materias.id_materia_prima

        GROUP BY materias_primas.id_materia_prima

        ORDER BY materias_primas.nombre";

$resultado = $conexion->query($sql);

$pdf->SetFont('Arial', '', 9);

while($fila = $resultado->fetch_assoc()) {

    $ultimoMovimiento = $fila["ultimo_movimiento"];

    if (!$ultimoMovimiento) {
        $ultimoMovimiento = "Sin movimientos";
    }

    $pdf->Cell(
        45,
        8,
        utf8_decode($fila["nombre"]),
        1
    );

    $pdf->Cell(
        30,
        8,
        utf8_decode($fila["unidad"]),
        1
    );

    $pdf->Cell(
        30,
        8,
        $fila["stock"],
        1
    );

    $pdf->Cell(
        40,
        8,
        $fila["cantidad_movimientos"],
        1
    );

    $pdf->Cell(
        45,
        8,
        $ultimoMovimiento,
        1
    );

    $pdf->Ln();
}

$pdf->Output();
?>