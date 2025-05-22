<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset=utf-8');

    require_once "../models/Marca.php";
    $marca = new Marca();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $task = $_GET['task'] ?? null;

            if ($task === 'getAllMarcaVehiculo') {
                echo json_encode($marca->getAllMarcaVehiculo());
                break;
            }

            if ($task === 'getAllMarcaProducto') {
                echo json_encode($marca->getAllMarcaProducto());
                break;
            }

            http_response_code(400);
            echo json_encode(['error' => 'Tarea inválida']);
            break;

        case 'POST':
            $task = $_GET['task'] ?? null;
            $data = json_decode(file_get_contents('php://input'), true);

            if ($task === 'registerMarcaVehiculo') {
                if (!empty($data['nombre'])) {
                    $newId = $marca->registerMarcaVehiculo($data);
                    echo json_encode(['success' => $newId > 0, 'idmarca' => $newId]);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Falta el nombre']);
                }
                break;
            }

            if ($task === 'registerMarcaProducto') {
                if (!empty($data['nombre'])) {
                    $newId = $marca->registerMarcaProducto($data);
                    echo json_encode(['success' => $newId > 0, 'idmarca' => $newId]);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Falta nombre']);
                }
                break;
            }

            http_response_code(400);
            echo json_encode(['error' => 'Tarea inválida']);
            break;
    }
}
