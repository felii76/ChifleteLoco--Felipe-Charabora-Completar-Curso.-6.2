<?php
require("../fpdf/fpdf.php");
require_once("../conexion.php");

$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial','B',16);
$pdf->Cell(190,10,'Informe de Clientes',0,1,'C');

$pdf->Ln(5);

$pdf->SetFont('Arial','B',10);

$pdf->Cell(55,10,'Cliente',1);
$pdf->Cell(30,10,'Telefono',1);
$pdf->Cell(55,10,'Email',1);
$pdf->Cell(20,10,'Pedidos',1);
$pdf->Cell(30,10,'Deuda',1);

$pdf->Ln();

$sql = "SELECT
            clientes.nombre,
            clientes.apellido,
            clientes.telefono,
            clientes.email,
            COUNT(pedidos.id_pedido) AS cantidad_pedidos,

            IFNULL(SUM(
                CASE
                    WHEN pedidos.estado='Pendiente'
                    THEN pedidos.total
                    ELSE 0
                END
            ),0) AS deuda

        FROM clientes

        LEFT JOIN pedidos
        ON clientes.id_cliente = pedidos.id_cliente

        GROUP BY clientes.id_cliente

        ORDER BY clientes.apellido, clientes.nombre";

$resultado = $conexion->query($sql);

$pdf->SetFont('Arial','',9);

while($fila = $resultado->fetch_assoc()){

    $pdf->Cell(55,8,utf8_decode($fila["nombre"]." ".$fila["apellido"]),1);
    $pdf->Cell(30,8,$fila["telefono"],1);
    $pdf->Cell(55,8,utf8_decode($fila["email"]),1);
    $pdf->Cell(20,8,$fila["cantidad_pedidos"],1);
    $pdf->Cell(30,8,"$".number_format($fila["deuda"],2,",","."),1);

    $pdf->Ln();
}

$pdf->Output();
?>