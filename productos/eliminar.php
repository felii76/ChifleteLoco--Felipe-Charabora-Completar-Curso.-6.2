<?php require_once __DIR__ . "/../auth.php"; ?>
<?php
require_once("../conexion.php");

$id=$_GET["id"];

$sql="DELETE FROM productos WHERE id_producto=$id";

if($conexion->query($sql)){
    header("Location: index.php");
    exit;
}else{
    echo "Error al eliminar.";
}
?>