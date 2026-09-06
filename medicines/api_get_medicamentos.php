<?php
// Establecer la cabecera para indicar que la respuesta es JSON
header('Content-Type: application/json');

require_once 'medicines_db.php'; // archivo de conexión

$medicamentos = []; // Un array para guardar los resultados

try {
    $database = new Database();
    $db = $database->connect();

    // Query para obtener TODOS los medicamentos relevantes.
    // Es importante que obtenga todos los medicamentos para que el frontend
    // siempre reciba la lista completa y pueda mostrar el estado actual de cada uno.
    $query = "SELECT id_med, description, description_old, family, line, status, limite_despacho, comentarios FROM medicamentos WHERE status = 1";
    $stmt = $db->prepare($query);
    $stmt->execute();

    // Guardamos todos los resultados en el array, usando while para recorrer el array
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $medicamentos[] = $row;
    }

    // Convertimos el array a formato JSON y lo imprimimos
    echo json_encode(['success' => true, 'data' => $medicamentos]);

} catch(PDOException $e) {
    // En caso de error, devolvemos un JSON con el mensaje de error
    http_response_code(500); // Código de error del servidor
    echo json_encode(['success' => false, 'message' => 'Error al cargar los datos: ' . $e->getMessage()]);
}
?>