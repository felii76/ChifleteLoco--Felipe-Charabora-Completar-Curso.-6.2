<?php
require_once("../conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $dni = $_POST["dni"];
    $direccion = $_POST["direccion"];
    $telefono = $_POST["telefono"];
    $email = $_POST["email"];

    $sql = "INSERT INTO clientes(nombre, apellido, dni, direccion, telefono, email)
            VALUES('$nombre','$apellido','$dni','$direccion','$telefono','$email')";

    if($conexion->query($sql)){
        header("Location: index.php");
        exit;
    }else{
        echo "Error al guardar el cliente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Cliente</title>
</head>
<body>

<h1>Agregar Cliente</h1>

<form method="POST">

    <label>Nombre:</label><br>
    <input type="text" name="nombre" required><br><br>

    <label>Apellido:</label><br>
    <input type="text" name="apellido" required><br><br>

    <label>DNI:</label><br>
    <input type="text" name="dni" required><br><br>

    <label>Dirección:</label><br>
    <input type="text" name="direccion"><br><br>

    <label>Teléfono:</label><br>
    <input type="text" name="telefono"><br><br>

    <label>Email:</label><br>
    <input type="email" name="email"><br><br>

    <input type="submit" value="Guardar Cliente">

</form>

<br>

<a href="index.php">Volver</a>

</body>
</html>