<?php
session_start();

// Deshabilitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

require_once 'medicines_db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$msg = '';

try {
    $database = new Database();
    $db = $database->connect();

    // Procesar la actualización del estado
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
        $id_med = $_POST['id_med'];
        $status = $_POST['status'];

        $stmt = $db->prepare("UPDATE medicamentos SET status = ? WHERE id_med = ?");
        $stmt->execute([$status, $id_med]);
        $msg = "Estado actualizado con éxito.";
    }

    // Obtener todos los medicamentos
    $query = "SELECT id_med, description, family, line, status, limite_despacho, comentarios FROM medicamentos";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $msg = "Error: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #eef0f4;
            font-family: 'Arial', sans-serif;
        }

        .header {
            background-color: #87c5a4;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            color: #0c1b5b;
            font-weight: bold;
        }

        .card {
            margin-bottom: 20px;
        }

        .table th {
            background-color: #0c1b5b;
            color: white;
        }

        .status-in-stock {
            color: green;
            font-weight: bold;
        }

        .status-out-of-stock {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="header">
            <h2>Dashboard de Administración</h2>
            <div>
                <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
            </div>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Agregar Medicamento</h5>
                        <a href="add.php" class="btn btn-success btn-lg">Agregar</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h3>Listado de Medicamentos</h3>
            <input type="text" id="buscador" class="form-control mb-3" placeholder="Buscar medicamento..."
                style="max-width: 400px;">
            <div id="tabla-container">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Familia</th>
                            <th>Vía de Administración</th>
                            <th>Existencia</th>
                            <th>Limite de despacho</th>
                            <th>Comentarios</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-medicamentos">
                        <?php foreach ($medicamentos as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id_med']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo htmlspecialchars($row['family']); ?></td>
                                <td><?php echo htmlspecialchars($row['line']); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id_med" value="<?php echo $row['id_med']; ?>">
                                        <select name="status" onchange="this.form.submit()">
                                            <option value="0" <?php echo $row['status'] == 0 ? 'selected' : ''; ?>>Agotado
                                            </option>
                                            <option value="1" <?php echo $row['status'] == 1 ? 'selected' : ''; ?>>En stock
                                            </option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td><?php echo htmlspecialchars($row['limite_despacho']); ?></td>
                                <td><?php echo htmlspecialchars($row['comentarios']); ?></td>
                                <td>
                                    <a href="edit.php?id_med=<?php echo $row['id_med']; ?>"
                                        class="btn btn-warning btn-sm">Editar</a>
                                    <a href="delete.php?id_med=<?php echo $row['id_med']; ?>" class="btn btn -danger btn-sm"
                                        onclick="return confirm('¿Seguro que deseas eliminar?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("buscador").addEventListener("keyup", function () {
            let filtro = this.value.toLowerCase();
            let filas = document.querySelectorAll("#tabla-medicamentos tr");

            filas.forEach(fila => {
                let textoFila = fila.textContent.toLowerCase();
                fila.style.display = textoFila.includes(filtro) ? "" : "none";
            });
        });
    </script>
</body>

</html>