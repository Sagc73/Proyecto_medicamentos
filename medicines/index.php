<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Medicamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #eef0f4;
            font-family: 'Arial', sans-serif;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #87c5a4;
            padding: 15px;
            border-radius: 10px;
        }
        .header h2 {
            color: #0c1b5b;
            font-weight: bold;
        }
        .search-bar {
            max-width: 400px;
        }
        .table th {
            background-color: #0c1b5b;
            color: white;
        }
        #tabla-container {
            display: none;
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
            <h2>LISTADO DE MEDICAMENTOS</h2>
            <input type="text" id="buscador" class="form-control search-bar" placeholder="Buscar medicamento...">
        </div>

        <!-- Tabla de medicamentos -->
        <div class="mt-3" id="tabla-container">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Familia</th>
                        <th>Vía de Administración</th>
                        <th>Existencia</th>
                    </tr>
                </thead>
                <tbody id="tabla-medicamentos">
                    <?php
                    require_once 'medicines_db.php'; // Incluye el archivo con la clase Database

                    try {
                        $database = new Database();
                        $db = $database->connect();

                        $query = "SELECT id_med, description, family, line, status FROM medicamentos";
                        $stmt = $db->prepare($query);
                        $stmt->execute();

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id_med']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['family']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['line']) . "</td>";
                            echo "<td>" . ($row['status'] == 1 ? '<span class="status-in-stock">En stock</span>' : '<span class="status-out-of-stock">Agotado</span>') . "</td>";
                            echo "</tr>";
                        }
                    } catch(PDOException $e) {
                        echo "<tr><td colspan='5'>Error al cargar los datos: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById("buscador").addEventListener("keyup", function() {
            let filtro = this.value.toLowerCase();
            let tablaContainer = document.getElementById("tabla-container");
            let filas = document.querySelectorAll("#tabla-medicamentos tr");

            // Si el campo de búsqueda está vacío, ocultar la tabla
            if (filtro === "") {
                tablaContainer.style.display = "none";
                return;
            }

            let hayResultados = false;

            // Filtrar las filas
            filas.forEach(fila => {
                let textoFila = fila.textContent.toLowerCase();
                if (textoFila.includes(filtro)) {
                    fila.style.display = "";
                    hayResultados = true;
                } else {
                    fila.style.display = "none";
                }
            });

            // Mostrar la tabla solo si hay resultados
            tablaContainer.style.display = hayResultados ? "block" : "none";
        });
    </script>
</body>
</html>
