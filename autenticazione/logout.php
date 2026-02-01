<?php
session_start();

// Svuota l'array di sessione
$_SESSION = array();

// Se desideri eliminare anche il cookie di sessione (buona norma come da specifiche)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Distrugge la sessione
session_destroy();

// Ritorna alla home
header("Location: ../home/Home.php");
exit();