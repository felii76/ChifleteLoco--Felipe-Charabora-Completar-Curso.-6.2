
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["usuario_id"])) {
    $rutaLogin = "/chiflete-loco/login.php";

    if (!empty($_SERVER["HTTP_HOST"]) &&
        $_SERVER["HTTP_HOST"] !== "localhost") {
        $rutaLogin = "/login.php";
    }

    header("Location: " . $rutaLogin);
    exit;
}