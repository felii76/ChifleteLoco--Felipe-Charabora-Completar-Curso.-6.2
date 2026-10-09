<?php require_once __DIR__ . "/../auth.php"; ?>
<?php
require_once("../conexion.php");

$sql = "SELECT pedidos.id_pedido,
               clientes.nombre,
               clientes.apellido,
               pedidos.fecha,
               pedidos.total
        FROM pedidos
        INNER JOIN clientes
        ON pedidos.id_cliente = clientes.id_cliente
        WHERE pedidos.estado = 'Pendiente'
        ORDER BY pedidos.fecha DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Informe de Pedidos Pendientes</title>
</head>
<body>

<h1>Informe de Pedidos Pendientes</h1>
<a href="pedidos_pendientes_pdf.php" target="_blank">Descargar PDF</a>

<br><br>

<table border="1" cellpadding="8">

<tr>
    <th>ID Pedido</th>
    <th>Cliente</th>
    <th>Fecha</th>
    <th>Total</th>
</tr>

<?php while($fila = $resultado->fetch_assoc()){ ?>

<tr>

<td><?= $fila["id_pedido"] ?></td>

<td><?= $fila["nombre"] ?> <?= $fila["apellido"] ?></td>

<td><?= $fila["fecha"] ?></td>

<td>$<?= number_format($fila["total"],2,",",".") ?></td>

</tr>

<?php } ?>

</table>

</body>
</html>