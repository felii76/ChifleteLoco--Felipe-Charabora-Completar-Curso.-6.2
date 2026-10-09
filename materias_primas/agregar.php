<?php
require_once("../conexion.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $nombre = $_POST["nombre"];
    $unidad = $_POST["unidad"];
    $stock = $_POST["stock"];

    $sql = "INSERT INTO materias_primas(nombre, unidad, stock)
            VALUES('$nombre', '$unidad', '$stock')";

    if($conexion->query($sql)){
        header("Location: index.php");
        exit;
    }else{
        echo "Error al guardar la materia prima.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Materia Prima</title>
</head>

<body>

<h1>Agregar Materia Prima</h1>

<form method="POST">

    Nombre:
    <br>
    <input type="text" name="nombre" required>

    <br><br>

    Unidad:
    <br>
    <input type="text" name="unidad" placeholder="kg, unidades, litros..." required>

    <br><br>

    Stock:
    <br>
    <input type="number" name="stock" step="0.01" min="0" value="0" required>

    <br><br>

    <input type="submit" value="Guardar Materia Prima">

</form>

<br>

<a href="index.php">Volver</a>

</body>
</html>