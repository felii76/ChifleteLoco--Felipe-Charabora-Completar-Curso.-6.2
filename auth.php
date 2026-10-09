
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["usuario_id"])) {
    $rutaLogin = "/login.php";

    if (
        isset($_SERVER["HTTP_HOST"]) &&
        strpos($_SERVER["HTTP_HOST"], "localhost") === 0
    ) {
        $rutaLogin = "/chiflete-loco/login.php";
    }

    header("Location: " . $rutaLogin);
    exit;
}