<?php
require_once("../conexion.php");

$sql = "SELECT * FROM materias_primas
        ORDER BY id_materia_prima DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Materias Primas</title>
</head>

<body>

<h1>Listado de Materias Primas</h1>

<a href="agregar.php">Agregar Materia Prima</a>

<br><br>

<table border="1" cellpadding="8">

<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Unidad</th>
    <th>Stock</th>
    <th>Acciones</th>
</tr>

<?php while($fila = $resultado->fetch_assoc()){ ?>

<tr>

    <td><?= $fila["id_materia_prima"] ?></td>

    <td><?= $fila["nombre"] ?></td>

    <td><?= $fila["unidad"] ?></td>

    <td><?= $fila["stock"] ?></td>

    <td>
        <a href="editar.php?id=<?= $fila["id_materia_prima"] ?>">
            Editar
        </a>

        |

        <a href="eliminar.php?id=<?= $fila["id_materia_prima"] ?>"
           onclick="return confirm('¿Está seguro de eliminar esta materia prima?');">
            Eliminar
        </a>
    </td>

</tr>

<?php } ?>

</table>

</body>
</html>