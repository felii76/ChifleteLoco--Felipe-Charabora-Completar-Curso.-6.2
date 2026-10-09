<?php require_once __DIR__ . "/../auth.php"; ?>
<?php
require_once("../conexion.php");

$sql = "SELECT
            materias_primas.id_materia_prima,
            materias_primas.nombre,
            materias_primas.unidad,
            materias_primas.stock,
            COUNT(movimientos_stock_materias.id_movimiento) AS cantidad_movimientos,
            MAX(movimientos_stock_materias.fecha) AS ultimo_movimiento

        FROM materias_primas

        LEFT JOIN movimientos_stock_materias
        ON materias_primas.id_materia_prima =
           movimientos_stock_materias.id_materia_prima

        GROUP BY materias_primas.id_materia_prima

        ORDER BY materias_primas.nombre";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe de Stock de Materias Primas</title>
</head>

<body>

<h1>Informe de Stock de Materias Primas</h1>

<a href="stock_materias_primas_pdf.php" target="_blank">
    Descargar PDF
</a>

<br><br>

<table border="1" cellpadding="8">

    <tr>
        <th>Materia Prima</th>
        <th>Unidad</th>
        <th>Stock Actual</th>
        <th>Cantidad de Movimientos</th>
        <th>Último Movimiento</th>
    </tr>

    <?php while($fila = $resultado->fetch_assoc()) { ?>

    <tr>

        <td>
            <?= $fila["nombre"] ?>
        </td>

        <td>
            <?= $fila["unidad"] ?>
        </td>

        <td>
            <?= $fila["stock"] ?>
        </td>

        <td>
            <?= $fila["cantidad_movimientos"] ?>
        </td>

        <td>
            <?= $fila["ultimo_movimiento"] ?? "Sin movimientos" ?>
        </td>

    </tr>

    <?php } ?>

</table>

<br>

<a href="../stock/materias.php">
    Ver movimientos de materias primas
</a>

</body>
</html>