<?php
require_once("../conexion.php");

$sql = "SELECT movimientos_stock.id_movimiento,
               productos.nombre,
               movimientos_stock.tipo,
               movimientos_stock.cantidad,
               movimientos_stock.fecha
        FROM movimientos_stock
        INNER JOIN productos
        ON movimientos_stock.id_producto = productos.id_producto
        ORDER BY movimientos_stock.fecha DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Movimientos de Stock</title>
</head>
<body>

<h1>Movimientos de Stock</h1>

<table border="1" cellpadding="8">

<tr>
    <th>ID</th>
    <th>Producto</th>
    <th>Tipo</th>
    <th>Cantidad</th>
    <th>Fecha</th>
</tr>

<?php while($fila = $resultado->fetch_assoc()){ ?>

<tr>

    <td><?= $fila["id_movimiento"] ?></td>

    <td><?= $fila["nombre"] ?></td>

    <td><?= $fila["tipo"] ?></td>

    <td><?= $fila["cantidad"] ?></td>

    <td><?= $fila["fecha"] ?></td>

</tr>

<?php } ?>

</table>

</body>
</html>