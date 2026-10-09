<?php
require_once("../conexion.php");

$id = $_GET["id"];

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $nombre=$_POST["nombre"];
    $descripcion=$_POST["descripcion"];
    $precio=$_POST["precio"];
    $stock=$_POST["stock"];

    $sql="UPDATE productos
          SET nombre='$nombre',
              descripcion='$descripcion',
              precio='$precio',
              stock='$stock'
          WHERE id_producto=$id";

    if($conexion->query($sql)){
        header("Location: index.php");
        exit;
    }
}

$sql="SELECT * FROM productos WHERE id_producto=$id";
$resultado=$conexion->query($sql);
$producto=$resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Producto</title>
</head>
<body>

<h1>Editar Producto</h1>

<form method="POST">

Nombre:<br>
<input type="text" name="nombre" value="<?= $producto['nombre'] ?>"><br><br>

Descripción:<br>
<input type="text" name="descripcion" value="<?= $producto['descripcion'] ?>"><br><br>

Precio:<br>
<input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>"><br><br>

Stock:<br>
<input type="number" name="stock" value="<?= $producto['stock'] ?>"><br><br>

<input type="submit" value="Guardar Cambios">

</form>

<br>

<a href="index.php">Volver</a>

</body>
</html>