<?php
require_once __DIR__ . "/../auth.php";
require_once("../conexion.php");

$idPedido = $_GET["id"];

// Buscar el pedido
$sqlPedido = "SELECT pedidos.*,
                     clientes.nombre,
                     clientes.apellido,
                     clientes.dni,
                     clientes.direccion,
                     clientes.telefono,
                     clientes.email
              FROM pedidos
              INNER JOIN clientes
              ON pedidos.id_cliente = clientes.id_cliente
              WHERE pedidos.id_pedido = $idPedido";

$pedido = $conexion->query($sqlPedido)->fetch_assoc();

if (!$pedido) {
    die("El pedido no existe.");
}

// Verificar si ya existe una factura
$buscarFactura = $conexion->query(
    "SELECT * FROM facturas WHERE id_pedido = $idPedido"
);

$factura = $buscarFactura->fetch_assoc();

// Si no existe, crearla
if (!$factura) {

    $fecha = date("Y-m-d");
    $total = $pedido["total"];

    $conexion->query(
        "INSERT INTO facturas(id_pedido, fecha, total)
         VALUES($idPedido, '$fecha', $total)"
    );

    $idFactura = $conexion->insert_id;

    $factura = [
        "id_factura" => $idFactura,
        "id_pedido" => $idPedido,
        "fecha" => $fecha,
        "total" => $total,
        "enviada" => "No"
    ];
}

// Obtener productos del pedido
$sqlDetalle = "SELECT productos.nombre,
                      detalle_pedido.cantidad,
                      detalle_pedido.precio_unitario,
                      detalle_pedido.subtotal
               FROM detalle_pedido
               INNER JOIN productos
               ON detalle_pedido.id_producto = productos.id_producto
               WHERE detalle_pedido.id_pedido = $idPedido";

$detalle = $conexion->query($sqlDetalle);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura</title>
</head>
<body>

<h1>CHIFLETE LOCO</h1>

<h2>Factura N° <?= $factura["id_factura"] ?></h2>

<p>
    <strong>Fecha:</strong>
    <?= $factura["fecha"] ?>
</p>

<hr>

<h3>Cliente</h3>

<p>
    <strong>Nombre:</strong>
    <?= $pedido["nombre"] ?> <?= $pedido["apellido"] ?>
</p>

<p>
    <strong>DNI:</strong>
    <?= $pedido["dni"] ?>
</p>

<p>
    <strong>Dirección:</strong>
    <?= $pedido["direccion"] ?>
</p>

<p>
    <strong>Teléfono:</strong>
    <?= $pedido["telefono"] ?>
</p>

<hr>

<h3>Detalle</h3>

<table border="1" cellpadding="8">

<tr>
    <th>Producto</th>
    <th>Cantidad</th>
    <th>Precio</th>
    <th>Subtotal</th>
</tr>

<?php while($fila = $detalle->fetch_assoc()) { ?>

<tr>
    <td><?= $fila["nombre"] ?></td>
    <td><?= $fila["cantidad"] ?></td>
    <td>$<?= number_format($fila["precio_unitario"], 2, ",", ".") ?></td>
    <td>$<?= number_format($fila["subtotal"], 2, ",", ".") ?></td>
</tr>

<?php } ?>

</table>

<h2>
    Total:
    $<?= number_format($factura["total"], 2, ",", ".") ?>
</h2>

<p>
    <strong>Factura enviada:</strong>
    <?= $factura["enviada"] ?>
</p>

<?php if ($factura["enviada"] == "No") { ?>

    <a href="enviar.php?id=<?= $factura["id_factura"] ?>">
        <button>Enviar Factura</button>
    </a>

<?php } else { ?>

    <p>
        <strong>La factura ya fue enviada.</strong>
    </p>

<?php } ?>

<br>

<a href="../pedidos/ver.php?id=<?= $idPedido ?>">
    Volver al pedido
</a>

</body>
</html>