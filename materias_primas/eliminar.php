<?php require_once __DIR__ . "/../auth.php"; ?>
<?php
require_once("../conexion.php");

$id = $_GET["id"];

$sql = "DELETE FROM materias_primas
        WHERE id_materia_prima=$id";

if ($conexion->query($sql)) {
    header("Location: index.php");
    exit;
} else {
    echo "Error al eliminar la materia prima.";
}
?>