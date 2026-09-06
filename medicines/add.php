<?php

session_start();

require_once 'medicines_db.php';


if (!isset($_SESSION['user_id'])) {

    header('Location: login.php');

    exit;

}


$msg = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_med = trim($_POST['id_med']);
    $description = trim($_POST['description']);
    $family = trim($_POST['family']);
    $line = trim($_POST['line']);
    $limited_exit = trim($_POST['limite_despacho']);
    $comments = trim($_POST['comentarios']);
    $status = isset($_POST['status']) ? 1 : 0;

    if (empty($id_med) || empty($description) || empty($family) || empty($line) || empty($limited_exit)) {

        $msg = "todos los campos son obligatorios";

    } else {

        try {

            $db = new Database();
            $conn = $db->connect();
            $stmt = $conn->prepare("INSERT INTO medicamentos(id_med, description, family, line, status,limite_despacho,comentarios) VALUES (?, ?, ?, ?, ?,?,?)");
            $stmt->execute([$id_med, $description, $family, $line, $status, $limited_exit, $comments]);
            header('Location: DashBoard.php?msg=medicamentos agregado con éxito.!');
            exit;

        } catch (PDOException $e) {

            $msg = "Error Add: " . $e->getMessage();

        }

    }

}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Medicamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Agregar Medicamento</h2>
        <?php if ($msg): ?>
            <div class="alert alert-danger"><?php echo $msg; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="id_med" class="form-label">Código</label>
                <input type="text" class="form-control" id="id_med" name="id_med" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <input type="text" class="form-control" id="description" name="description" required>
            </div>
            <div class="mb-3">
                <label for="family" class="form-label">Familia</label>
                <input type="text" class="form-control" id="family" name="family" required>
            </div>
            <div class="mb-3">
                <label for="line" class="form-label">Vía de Administración</label>
                <input type="text" class="form-control" id="line" name="line" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="status" name="status" value="1">
                <label class="form-check-label" for="status">En stock</label>
            </div>
            <label for="limite_despacho">Límite de despacho:</label>
            <input type="text" name="limite_despacho"
                value="<?php echo isset($row['limite_despacho']) ? $row['limite_despacho'] : ''; ?>">

            <label for="comentarios">Comentarios:</label>
            <input type="text" name="comentarios"
                value="<?php echo isset($row['comentarios']) ? $row['comentarios'] : ''; ?>">

            <button type="submit" class="btn btn-primary">Agregar</button>
            <a href="DashBoard.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>

</html>