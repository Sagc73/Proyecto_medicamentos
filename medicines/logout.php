<?php
session_start();

// Borra todas las variables de sesión
$_SESSION = array();

// Si se usa una cookie de sesión, elimínala
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruye la sesión
session_destroy();

// Redirige a login (o index si así prefieres)
header('Location: index.php');
exit;
?>
