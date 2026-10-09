<?php
require_once("../conexion.php");

$id = $_GET["id"];

// Buscar el pedido
$sqlPedido = "SELECT *
              FROM pedidos
              WHERE id_pedido = $id";

$resultadoPedido = $conexion->query($sqlPedido);
$pedido = $resultadoPedido->fetch_assoc();

if (!$pedido) {
    die("El pedido no existe.");
}

// Solo se pueden modificar pedidos pendientes
if ($pedido["estado"] != "Pendiente") {
    die("Este pedido ya no puede modificarse porque fue facturado, entregado o anulado.");
}

// Buscar el detalle del pedido
$sqlDetalle = "SELECT *
               FROM detalle_pedido
               WHERE id_pedido = $id
               LIMIT 1";

$resultadoDetalle = $conexion->query($sqlDetalle);
$detalle = $resultadoDetalle->fetch_assoc();

if (!$detalle) {
    die("El pedido no tiene productos asociados.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $cliente = $_POST["cliente"];
    $producto = $_POST["producto"];
    $cantidad = $_POST["cantidad"];
    $fecha = $_POST["fecha"];

    // Buscar el precio actual del producto
    $consulta = $conexion->query(
        "SELECT precio
         FROM productos
         WHERE id_producto = $producto"
    );

    $prod = $consulta->fetch_assoc();

    if (!$prod) {
        die("El producto seleccionado no existe.");
    }

    $precio = $prod["precio"];
    $subtotal = $precio * $cantidad;

    // Actualizar pedido
    $sqlActualizarPedido = "UPDATE pedidos
                            SET id_cliente = $cliente,
                                fecha = '$fecha',
                                total = $subtotal
                            WHERE id_pedido = $id";

    // Actualizar detalle
    $sqlActualizarDetalle = "UPDATE detalle_pedido
                             SET id_producto = $producto,
                                 cantidad = $cantidad,
                                 precio_unitario = $precio,
                                 subtotal = $subtotal
                             WHERE id_detalle = " . $detalle["id_detalle"];

    if (
        $conexion->query($sqlActualizarPedido) &&
        $conexion->query($sqlActualizarDetalle)
    ) {

        header("Location: ver.php?id=$id");
        exit;

    } else {

        echo "Error al modificar el pedido.";
    }
}

// Obtener clientes
$clientes = $conexion->query(
    "SELECT *
     FROM clientes
     ORDER BY apellido, nombre"
);

// Obtener productos
$productos = $conexion->query(
    "SELECT *
     FROM productos
     ORDER BY nombre"
);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Pedido</title>
</head>

<body>

<h1>Editar Pedido</h1>

<form method="POST">

    <label>Cliente:</label>
    <br>

    <select name="cliente" required>

        <?php while ($c = $clientes->fetch_assoc()) { ?>

            <option
                value="<?= $c["id_cliente"] ?>"
                <?= ($c["id_cliente"] == $pedido["id_cliente"]) ? "selected" : "" ?>
            >
                <?= $c["nombre"] ?> <?= $c["apellido"] ?>
            </option>

        <?php } ?>

    </select>

    <br><br>

    <label>Producto:</label>
    <br>

    <select name="producto" required>

        <?php while ($p = $productos->fetch_assoc()) { ?>

            <option
                value="<?= $p["id_producto"] ?>"
                <?= ($p["id_producto"] == $detalle["id_producto"]) ? "selected" : "" ?>
            >
                <?= $p["nombre"] ?> - $<?= $p["precio"] ?>
            </option>

        <?php } ?>

    </select>

    <br><br>

    <label>Cantidad:</label>
    <br>

    <input
        type="number"
        name="cantidad"
        min="1"
        value="<?= $detalle["cantidad"] ?>"
        required
    >

    <br><br>

    <label>Fecha:</label>
    <br>

    <input
        type="date"
        name="fecha"
        value="<?= $pedido["fecha"] ?>"
        required
    >

    <br><br>

    <input type="submit" value="Guardar Cambios">

</form>

<br>

<a href="ver.php?id=<?= $id ?>">Volver al pedido</a>

</body>
</html>