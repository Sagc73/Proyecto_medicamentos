<?php
session_start();
require_once 'medicines_db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$msg = '';

$id_med = $_GET['id_med'] ?? '';

if (empty($id_med)) {
    header('Location: index.php');
    exit;
}

$db = new Database();
$conn = $db->connect();
$stmt = $conn->prepare('SELECT id_med, description, family, line, status, limite_despacho, comentarios FROM medicamentos WHERE id_med = ?');
$stmt->execute([$id_med]);
$medicamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medicamento) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = trim($_POST['description']);
    $family = trim($_POST['family']);
    $line = trim($_POST['line']);
    $limited_exit = trim($_POST['limite_despacho']);
    $comments = trim($_POST['comentarios']);
    $status = isset($_POST['status']) ? 1 : 0;


    if (empty($description) || empty($family) || empty($line)) {
        $msg = "Todos los campos son obligatorios..!";
    } else {
        try {
            $stmt = $conn->prepare("UPDATE medicamentos SET description = ?, family = ?, line = ?, status = ?, limite_despacho = ?, comentarios = ? WHERE id_med = ?");
            $stmt->execute([$description, $family, $line, $status,$limited_exit, $comments, $id_med]);
            header("Location: DashBoard.php?msg=medicamento actualizado con éxito!");
            exit;
        } catch (PDOException $e) {
            $msg = "Error en update: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Medicamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Editar Medicamento</h2>
        <?php if ($msg): ?>
            <div class="alert alert-danger"><?php echo $msg; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="id_med" class="form-label">Código</label>
                <input type="text" class="form-control" id="id_med" name="id_med"
                    value="<?php echo htmlspecialchars($medicamento['id_med']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <input type="text" class="form-control" id="description" name="description"
                    value="<?php echo htmlspecialchars($medicamento['description']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="family" class="form-label">Familia</label>
                <input type="text" class="form-control" id="family" name="family"
                    value="<?php echo htmlspecialchars($medicamento['family']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="line" class="form-label">Vía de Administración</label>
                <input type="text" class="form-control" id="line" name="line"
                    value="<?php echo htmlspecialchars($medicamento['line']); ?>" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="status" name="status" value="1" <?php echo $medicamento['status'] == 1 ? 'checked' : ''; ?>>
                <label class="form-check-label" for="status">En stock</label>
            </div>
            <label for="limite_despacho">Límite de despacho:</label>
            <input type="text" name="limite_despacho"
                value="<?php echo htmlspecialchars($medicamento['limite_despacho']); ?>" >

            <label for="comentarios">Comentarios:</label>
            <input type="text" name="comentarios"
                value="<?php echo htmlspecialchars($medicamento['comentarios']); ?>" >

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="DashBoard.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>

</html>