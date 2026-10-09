<?php
require_once("../conexion.php");

$id = $_GET["id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST["nombre"];
    $unidad = $_POST["unidad"];

    $sql = "UPDATE materias_primas
            SET nombre='$nombre',
                unidad='$unidad'
            WHERE id_materia_prima=$id";

    if ($conexion->query($sql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error al modificar la materia prima.";
    }
}

$sql = "SELECT * FROM materias_primas
        WHERE id_materia_prima=$id";

$resultado = $conexion->query($sql);
$materia = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Materia Prima</title>
</head>

<body>

<h1>Editar Materia Prima</h1>

<form method="POST">

    Nombre:
    <br>
    <input type="text"
           name="nombre"
           value="<?= $materia["nombre"] ?>"
           required>

    <br><br>

    Unidad:
    <br>
    <input type="text"
           name="unidad"
           value="<?= $materia["unidad"] ?>"
           required>

    <br><br>

    <input type="submit" value="Guardar Cambios">

</form>

<br>

<a href="index.php">Volver</a>

</body>
</html>