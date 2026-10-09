<?php
require_once("../conexion.php");

$sql = "SELECT * FROM productos ORDER BY id_producto DESC";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
</head>
<body>

<h1>Listado de Productos</h1>

<a href="agregar.php">Agregar Producto</a>

<br><br>

<table border="1" cellpadding="8">

<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Descripción</th>
    <th>Precio</th>
    <th>Stock</th>
    <th>Acciones</th>
</tr>

<?php while($fila = $resultado->fetch_assoc()) { ?>

<tr>
    <td><?= $fila['id_producto'] ?></td>
    <td><?= $fila['nombre'] ?></td>
    <td><?= $fila['descripcion'] ?></td>
    <td>$<?= $fila['precio'] ?></td>
    <td><?= $fila['stock'] ?></td>

    <td>
        <a href="editar.php?id=<?= $fila['id_producto'] ?>">Editar</a> |
        <a href="eliminar.php?id=<?= $fila['id_producto'] ?>"
           onclick="return confirm('¿Está seguro de eliminar este producto?');">
           Eliminar
        </a>
    </td>
</tr>

<?php } ?>

</table>

</body>
</html>