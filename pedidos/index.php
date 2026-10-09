<?php
require_once("../conexion.php");

$sql = "SELECT pedidos.id_pedido,
               clientes.nombre,
               clientes.apellido,
               pedidos.fecha,
               pedidos.estado,
               pedidos.total
        FROM pedidos
        INNER JOIN clientes
        ON pedidos.id_cliente = clientes.id_cliente
        ORDER BY pedidos.id_pedido DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Pedidos</title>
</head>
<body>

<h1>Listado de Pedidos</h1>

<a href="agregar.php">Nuevo Pedido</a>

<br><br>

<table border="1" cellpadding="8">

    <tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>Fecha</th>
        <th>Estado</th>
        <th>Total</th>
        <th>Acciones</th>
    </tr>

    <?php while($fila = $resultado->fetch_assoc()) { ?>

    <tr>

        <td><?= $fila["id_pedido"] ?></td>

        <td><?= $fila["nombre"] ?> <?= $fila["apellido"] ?></td>

        <td><?= $fila["fecha"] ?></td>

        <td><?= $fila["estado"] ?></td>

        <td>$<?= number_format($fila["total"], 2, ',', '.') ?></td>

        <td>

    <a href="ver.php?id=<?= $fila["id_pedido"] ?>">
        Ver
    </a>

    <?php if ($fila["estado"] == "Pendiente") { ?>

        |
        
        <a href="editar.php?id=<?= $fila["id_pedido"] ?>">
            Editar
        </a>

    <?php } ?>

</td>

    </tr>

    <?php } ?>

</table>

</body>
</html>