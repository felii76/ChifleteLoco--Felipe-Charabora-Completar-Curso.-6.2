<?php
require_once("../conexion.php");

$id = $_GET["id"];

$sqlPedido = "SELECT pedidos.*, 
                     clientes.nombre, 
                     clientes.apellido
              FROM pedidos
              INNER JOIN clientes
              ON pedidos.id_cliente = clientes.id_cliente
              WHERE pedidos.id_pedido = $id";

$pedido = $conexion->query($sqlPedido)->fetch_assoc();

if (!$pedido) {
    die("El pedido no existe.");
}

$sqlDetalle = "SELECT productos.nombre,
                      detalle_pedido.cantidad,
                      detalle_pedido.precio_unitario,
                      detalle_pedido.subtotal
               FROM detalle_pedido
               INNER JOIN productos
               ON detalle_pedido.id_producto = productos.id_producto
               WHERE detalle_pedido.id_pedido = $id";

$detalle = $conexion->query($sqlDetalle);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle del Pedido</title>
</head>

<body>

<h1>Detalle del Pedido</h1>

<p>
    <strong>Cliente:</strong>
    <?= $pedido["nombre"] ?> <?= $pedido["apellido"] ?>
</p>

<p>
    <strong>Fecha:</strong>
    <?= $pedido["fecha"] ?>
</p>

<p>
    <strong>Estado:</strong>
    <?= $pedido["estado"] ?>
</p>

<?php

if ($pedido["estado"] == "Pendiente") {

    echo '
    <a href="cambiar_estado.php?id='.$id.'&estado=Facturado">
        <button>Facturar Pedido</button>
    </a>
    ';

}

elseif ($pedido["estado"] == "Facturado") {

    echo '
    <a href="cambiar_estado.php?id='.$id.'&estado=Entregado">
        <button>Marcar como Entregado</button>
    </a>
    ';

    echo '
    <a href="../facturas/ver.php?id='.$id.'" target="_blank">
        <button>Ver Factura</button>
    </a>
    ';

}

elseif ($pedido["estado"] == "Entregado") {

    echo "<p><strong>Pedido entregado.</strong></p>";

    echo '
    <a href="../facturas/ver.php?id='.$id.'" target="_blank">
        <button>Ver Factura</button>
    </a>
    ';

}

elseif ($pedido["estado"] == "Anulado") {

    echo "<p><strong>Pedido anulado.</strong></p>";

}

?>

<hr>

<h2>Productos del Pedido</h2>

<table border="1" cellpadding="8">

    <tr>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Subtotal</th>
    </tr>

    <?php while ($fila = $detalle->fetch_assoc()) { ?>

    <tr>

        <td>
            <?= $fila["nombre"] ?>
        </td>

        <td>
            <?= $fila["cantidad"] ?>
        </td>

        <td>
            $<?= number_format($fila["precio_unitario"], 2, ",", ".") ?>
        </td>

        <td>
            $<?= number_format($fila["subtotal"], 2, ",", ".") ?>
        </td>

    </tr>

    <?php } ?>

</table>

<br>

<h3>
    Total:
    $<?= number_format($pedido["total"], 2, ",", ".") ?>
</h3>

<br>

<a href="index.php">Volver al listado</a>

</body>

</html>