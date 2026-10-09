<?php
require_once("../conexion.php");

$sql = "SELECT * FROM clientes ORDER BY id_cliente DESC";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
</head>
<body>

<h1>Listado de Clientes</h1>

<a href="agregar.php">Agregar Cliente</a>

<br><br>

<table border="1" cellpadding="8">

<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Apellido</th>
    <th>DNI</th>
    <th>Teléfono</th>
    <th>Email</th>
    <th>Acciones</th>
</tr>

<?php while($fila = $resultado->fetch_assoc()) { ?>

<tr>
    <td><?= $fila['id_cliente'] ?></td>
    <td><?= $fila['nombre'] ?></td>
    <td><?= $fila['apellido'] ?></td>
    <td><?= $fila['dni'] ?></td>
    <td><?= $fila['telefono'] ?></td>
    <td><?= $fila['email'] ?></td>
    <td>
    <a href="editar.php?id=<?= $fila['id_cliente'] ?>">Editar</a>
    <a href="eliminar.php?id=<?= $fila['id_cliente'] ?>" onclick="return confirm('¿Está seguro de eliminar este cliente?');">
        Eliminar
    </a>
</td>
</tr>

<?php } ?>

</table>

</body>
</html>