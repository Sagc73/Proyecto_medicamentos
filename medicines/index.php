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
            margin-right: 10px;
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
            <div class="d-flex align-items-center">
                <input type="text" id="buscador" class="form-control search-bar" placeholder="Buscar medicamento...">
            </div>
            <button class="btn btn-primary" onclick="location.href='login.php'">Login</button>
        </div>

        <div class="mt-3" id="tabla-container">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
			<th>Descripción Anterior</th>
                        <th>Familia</th>
                        <th>Vía de Administración</th>
                        <th>Existencia</th>
                        <th>Límite de despacho</th>
                        <th>Comentarios</th>
                    </tr>
                </thead>
                <tbody id="tabla-medicamentos">
                    <tr><td colspan="7" class="text-center">Cargando datos...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const tablaBody = document.getElementById('tabla-medicamentos');
        const buscador = document.getElementById('buscador');
        const tablaContainer = document.getElementById('tabla-container');

        // Función para renderizar la tabla con los datos del servidor
        function renderizarTabla(medicamentos) {
            // Si no hay datos, muestra un mensaje
            if (medicamentos.length === 0) {
                tablaBody.innerHTML = '<tr><td colspan="7" class="text-center">No se encontraron medicamentos.</td></tr>';
                return;
            }

            // Limpiamos el contenido anterior
            tablaBody.innerHTML = '';

            // Creamos las filas nuevas con un bucle
            medicamentos.forEach(med => {
                const statusClass = med.status == 1 ? 'status-in-stock' : 'status-out-of-stock';
                const statusText = med.status == 1 ? 'En stock' : 'Agotado';

                // Creamos la fila HTML usando plantillas de texto
                const filaHTML = `
                    <tr>
                        <td>${med.id_med}</td>
                        <td>${med.description}</td>
			<td>${med.description_old}</td>
                        <td>${med.family}</td>
                        <td>${med.line}</td>
                        <td><span class="${statusClass}">${statusText}</span></td>
                        <td>${med.limite_despacho}</td>
                        <td>${med.comentarios}</td>
                    </tr>
                `;
                // Añadimos la nueva fila al cuerpo de la tabla
                tablaBody.innerHTML += filaHTML;
            });
        }

        // Función principal para obtener datos del API de forma asíncrona
        async function cargarMedicamentos() {
            try {
                const response = await fetch('api_get_medicamentos.php');
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                const resultado = await response.json();
                
                if (resultado.success) {
                    renderizarTabla(resultado.data);
                    // Una vez cargados los datos, volvemos a aplicar el filtro de búsqueda
                    aplicarFiltro();
                } else {
                    tablaBody.innerHTML = `<tr><td colspan="7" class="text-center">Error: ${resultado.message}</td></tr>`;
                }
            } catch (error) {
                console.error('Error al obtener los medicamentos:', error);
                tablaBody.innerHTML = `<tr><td colspan="7" class="text-center">No se pudo conectar con el servidor.</td></tr>`;
            }
        }

        // Función para el buscador
        function aplicarFiltro() {
            let filtro = buscador.value.toLowerCase();
            let filas = tablaBody.querySelectorAll("tr");
            let hayResultados = false;
            
            if (filtro === "") {
                tablaContainer.style.display = "none";
                return;
            }

            filas.forEach(fila => {
                let textoFila = fila.textContent.toLowerCase();
                if (textoFila.includes(filtro)) {
                    fila.style.display = "";
                    hayResultados = true;
                } else {
                    fila.style.display = "none";
                }
            });
            
            tablaContainer.style.display = hayResultados ? "block" : "none";
        }
        
        buscador.addEventListener("keyup", aplicarFiltro);

        // === LÓGICA DE ACTUALIZACIÓN AUTOMÁTICA ===

        // 1. Carga los datos inmediatamente cuando la página está lista
        document.addEventListener('DOMContentLoaded', cargarMedicamentos);

        // 2. Establece un intervalo para refrescar los datos cada 7 segundos (7000 ms)
        setInterval(cargarMedicamentos, 7000);

    </script>
</body>
</html>