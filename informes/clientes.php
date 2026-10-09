<?php
require_once("../conexion.php");
require_once __DIR__ . "/../auth.php";

$sql = "SELECT
            clientes.id_cliente,
            clientes.nombre,
            clientes.apellido,
            clientes.telefono,
            clientes.email,
            COUNT(pedidos.id_pedido) AS cantidad_pedidos,
            IFNULL(SUM(pedidos.total),0) AS total_compras,
            IFNULL(SUM(
                CASE
                    WHEN pedidos.estado = 'Pendiente'
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Informe de Clientes</title>
</head>
<body>

<h1>Informe de Clientes</h1>
<a href="clientes_pdf.php" target="_blank">Descargar PDF</a>

<br><br>

<table border="1" cellpadding="8">

<tr>
    <th>Cliente</th>
    <th>Teléfono</th>
    <th>Email</th>
    <th>Pedidos</th>
    <th>Total Comprado</th>
    <th>Deuda</th>
</tr>

<?php while($fila = $resultado->fetch_assoc()){ ?>

<tr>

    <td><?= $fila["nombre"] ?> <?= $fila["apellido"] ?></td>

    <td><?= $fila["telefono"] ?></td>

    <td><?= $fila["email"] ?></td>

    <td><?= $fila["cantidad_pedidos"] ?></td>

    <td>$<?= number_format($fila["total_compras"],2,",",".") ?></td>

    <td>$<?= number_format($fila["deuda"],2,",",".") ?></td>

</tr>

<?php } ?>

</table>

</body>
</html>