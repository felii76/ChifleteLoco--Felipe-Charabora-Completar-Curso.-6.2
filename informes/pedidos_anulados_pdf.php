<?php
require("../fpdf/fpdf.php");
require_once("../conexion.php");

$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial','B',16);
$pdf->Cell(190,10,'Informe de Pedidos Anulados',0,1,'C');

$pdf->Ln(5);

$pdf->SetFont('Arial','B',10);

$pdf->Cell(25,10,'ID',1);
$pdf->Cell(65,10,'Cliente',1);
$pdf->Cell(40,10,'Fecha',1);
$pdf->Cell(60,10,'Total',1);

$pdf->Ln();

$sql = "SELECT
            pedidos.id_pedido,
            clientes.nombre,
            clientes.apellido,
            pedidos.fecha,
            pedidos.total
        FROM pedidos
        INNER JOIN clientes
        ON pedidos.id_cliente = clientes.id_cliente
        WHERE pedidos.estado = 'Anulado'
        ORDER BY pedidos.fecha DESC";

$resultado = $conexion->query($sql);

$pdf->SetFont('Arial','',10);

while($fila = $resultado->fetch_assoc()){

    $pdf->Cell(25,8,$fila["id_pedido"],1);
    $pdf->Cell(65,8,utf8_decode($fila["nombre"]." ".$fila["apellido"]),1);
    $pdf->Cell(40,8,$fila["fecha"],1);
    $pdf->Cell(60,8,"$".number_format($fila["total"],2,",","."),1);

    $pdf->Ln();
}

$pdf->Output();
?>