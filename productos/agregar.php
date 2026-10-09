<?php
require_once("../conexion.php");

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $nombre=$_POST["nombre"];
    $descripcion=$_POST["descripcion"];
    $precio=$_POST["precio"];
    $stock=$_POST["stock"];

    $sql="INSERT INTO productos(nombre,descripcion,precio,stock)
          VALUES('$nombre','$descripcion','$precio','$stock')";

    if($conexion->query($sql)){
        header("Location: index.php");
        exit;
    }else{
        echo "Error al guardar.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Agregar Producto</title>
</head>
<body>

<h1>Agregar Producto</h1>

<form method="POST">

Nombre:<br>
<input type="text" name="nombre" required><br><br>

Descripción:<br>
<input type="text" name="descripcion"><br><br>

Precio:<br>
<input type="number" step="0.01" name="precio" required><br><br>

Stock:<br>
<input type="number" name="stock" required><br><br>

<input type="submit" value="Guardar Producto">

</form>

<br>

<a href="index.php">Volver</a>

</body>
</html>