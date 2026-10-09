<?php
require_once __DIR__ . "/../auth.php";
require_once("../conexion.php");

$id = $_GET["id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $dni = $_POST["dni"];
    $direccion = $_POST["direccion"];
    $telefono = $_POST["telefono"];
    $email = $_POST["email"];

    $sql = "UPDATE clientes
            SET nombre='$nombre',
                apellido='$apellido',
                dni='$dni',
                direccion='$direccion',
                telefono='$telefono',
                email='$email'
            WHERE id_cliente=$id";

    if($conexion->query($sql)){
        header("Location: index.php");
        exit;
    }
}

$sql = "SELECT * FROM clientes WHERE id_cliente=$id";
$resultado = $conexion->query($sql);
$cliente = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
</head>
<body>

<h1>Editar Cliente</h1>

<form method="POST">

Nombre:<br>
<input type="text" name="nombre" value="<?= $cliente['nombre'] ?>"><br><br>

Apellido:<br>
<input type="text" name="apellido" value="<?= $cliente['apellido'] ?>"><br><br>

DNI:<br>
<input type="text" name="dni" value="<?= $cliente['dni'] ?>"><br><br>

Dirección:<br>
<input type="text" name="direccion" value="<?= $cliente['direccion'] ?>"><br><br>

Teléfono:<br>
<input type="text" name="telefono" value="<?= $cliente['telefono'] ?>"><br><br>

Email:<br>
<input type="email" name="email" value="<?= $cliente['email'] ?>"><br><br>

<input type="submit" value="Guardar Cambios">

</form>

<br>

<a href="index.php">Volver al listado</a>

</body>
</html>