<?php
require_once("../conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $materia = $_POST["materia"];
    $tipo = $_POST["tipo"];
    $cantidad = $_POST["cantidad"];
    $fecha = $_POST["fecha"];

    // Buscar stock actual
    $consulta = $conexion->query(
        "SELECT stock
         FROM materias_primas
         WHERE id_materia_prima=$materia"
    );

    $dato = $consulta->fetch_assoc();

    $stockActual = $dato["stock"];

    // Calcular nuevo stock
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
        "UPDATE materias_primas
         SET stock=$nuevoStock
         WHERE id_materia_prima=$materia"
    );

    // Registrar movimiento
    $conexion->query(
        "INSERT INTO movimientos_stock_materias
        (id_materia_prima, tipo, cantidad, fecha)
        VALUES
        ($materia, '$tipo', $cantidad, '$fecha')"
    );

    header("Location: materias.php");
    exit;
}

// Obtener materias primas para el formulario
$materias = $conexion->query(
    "SELECT *
     FROM materias_primas
     ORDER BY nombre"
);

// Obtener historial de movimientos
$movimientos = $conexion->query(
    "SELECT movimientos_stock_materias.id_movimiento,
            materias_primas.nombre,
            materias_primas.unidad,
            movimientos_stock_materias.tipo,
            movimientos_stock_materias.cantidad,
            movimientos_stock_materias.fecha
     FROM movimientos_stock_materias
     INNER JOIN materias_primas
     ON movimientos_stock_materias.id_materia_prima = materias_primas.id_materia_prima
     ORDER BY movimientos_stock_materias.fecha DESC,
              movimientos_stock_materias.id_movimiento DESC"
);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Movimientos de Materias Primas</title>
</head>

<body>

<h1>Movimientos de Materias Primas</h1>

<h2>Registrar Movimiento</h2>

<form method="POST">

    Materia Prima:
    <br>

    <select name="materia" required>

        <?php while($m = $materias->fetch_assoc()) { ?>

            <option value="<?= $m["id_materia_prima"] ?>">
                <?= $m["nombre"] ?>
                (Stock: <?= $m["stock"] ?> <?= $m["unidad"] ?>)
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

    <input type="number"
           name="cantidad"
           step="0.01"
           min="0.01"
           required>

    <br><br>

    Fecha:
    <br>

    <input type="date"
           name="fecha"
           value="<?= date('Y-m-d') ?>"
           required>

    <br><br>

    <input type="submit"
           value="Guardar Movimiento">

</form>

<hr>

<h2>Historial de Movimientos</h2>

<table border="1" cellpadding="8">

    <tr>
        <th>ID</th>
        <th>Materia Prima</th>
        <th>Unidad</th>
        <th>Tipo</th>
        <th>Cantidad</th>
        <th>Fecha</th>
    </tr>

    <?php while($mov = $movimientos->fetch_assoc()) { ?>

    <tr>

        <td><?= $mov["id_movimiento"] ?></td>

        <td><?= $mov["nombre"] ?></td>

        <td><?= $mov["unidad"] ?></td>

        <td><?= $mov["tipo"] ?></td>

        <td><?= $mov["cantidad"] ?></td>

        <td><?= $mov["fecha"] ?></td>

    </tr>

    <?php } ?>

</table>

<br>

<a href="../materias_primas/">Volver a Materias Primas</a>

</body>
</html>