<?php
session_start();

// Deshabilitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

require_once 'medicines_db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $msg = "Todos los campos son obligatorios.";
    } else {
        $db = new Database();
        $conn = $db->connect();

        $stmt = $conn->prepare('SELECT id, username, password, role FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificación de contraseña sin hash
        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: DashBoard.php');
            exit;
        } else {
            $msg = "Usuario o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Iniciar Sesión</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($msg): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($msg); ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                        </form>
                        <a href="index.php" class="btn btn-secondary mt-3">Regresar al Inicio</a> <!-- Botón de regreso -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
