<?php
require_once("../conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $producto = $_POST["producto"];
    $tipo = $_POST["tipo"];
    $cantidad = $_POST["cantidad"];
    $fecha = $_POST["fecha"];

    // Buscar el stock actual
    $consulta = $conexion->query(
        "SELECT stock
         FROM productos
         WHERE id_producto = $producto"
    );

    $dato = $consulta->fetch_assoc();

    if (!$dato) {
        die("El producto no existe.");
    }

    $stockActual = $dato["stock"];

    // Calcular el nuevo stock
    if ($tipo == "Ingreso") {
        $nuevoStock = $stockActual + $cantidad;
    } else {
        $nuevoStock = $stockActual - $cantidad;
    }

    // Evitar stock negativo
    if ($nuevoStock < 0) {
        die("No hay suficiente stock para realizar este egreso.");
    }

    // Actualizar stock
    $conexion->query(
        "UPDATE productos
         SET stock = $nuevoStock
         WHERE id_producto = $producto"
    );

    // Registrar movimiento
    $conexion->query(
        "INSERT INTO movimientos_stock
        (id_producto, tipo, cantidad, fecha)
        VALUES
        ($producto, '$tipo', $cantidad, '$fecha')"
    );

    header("Location: productos.php");
    exit;
}

// Obtener productos
$productos = $conexion->query(
    "SELECT *
     FROM productos
     ORDER BY nombre"
);

// Obtener historial
$movimientos = $conexion->query(
    "SELECT movimientos_stock.id_movimiento,
            productos.nombre,
            movimientos_stock.tipo,
            movimientos_stock.cantidad,
            movimientos_stock.fecha
     FROM movimientos_stock
     INNER JOIN productos
     ON movimientos_stock.id_producto = productos.id_producto
     ORDER BY movimientos_stock.fecha DESC,
              movimientos_stock.id_movimiento DESC"
);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Movimientos de Productos</title>
</head>

<body>

<h1>Movimientos de Stock de Productos</h1>

<h2>Registrar Movimiento</h2>

<form method="POST">

    Producto:
    <br>

    <select name="producto" required>

        <?php while ($p = $productos->fetch_assoc()) { ?>

            <option value="<?= $p["id_producto"] ?>">
                <?= $p["nombre"] ?> (Stock: <?= $p["stock"] ?>)
            </option>

        <?php } ?>

    </select>

    <br><br>

    Tipo:
    <br>

    <select name="tipo" required>
        <option value="Ingreso">Ingreso</option>
        <option value="Egreso">Egreso</option>
    </select>

    <br><br>

    Cantidad:
    <br>

    <input
        type="number"
        name="cantidad"
        min="1"
        required
    >

    <br><br>

    Fecha:
    <br>

    <input
        type="date"
        name="fecha"
        value="<?= date('Y-m-d') ?>"
        required
    >

    <br><br>

    <input type="submit" value="Guardar Movimiento">

</form>

<hr>

<h2>Historial de Movimientos</h2>

<table border="1" cellpadding="8">

    <tr>
        <th>ID</th>
        <th>Producto</th>
        <th>Tipo</th>
        <th>Cantidad</th>
        <th>Fecha</th>
    </tr>

    <?php while ($mov = $movimientos->fetch_assoc()) { ?>

    <tr>

        <td><?= $mov["id_movimiento"] ?></td>

        <td><?= $mov["nombre"] ?></td>

        <td><?= $mov["tipo"] ?></td>

        <td><?= $mov["cantidad"] ?></td>

        <td><?= $mov["fecha"] ?></td>

    </tr>

    <?php } ?>

</table>

<br>

<a href="../productos/">Volver a Productos</a>

</body>
</html>