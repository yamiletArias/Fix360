<?php

if (isset($_SERVER['REQUEST_METHOD'])){
    header('Content-type: application/json; charset = utf-8');

    require_once "../models/Marca.php";
    $marca = new Marca();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if($_GET['task'] == 'getAllMarcaVehiculo'){
            echo json_encode($marca->getAllMarcaVehiculo());
            }
            if($_GET['task'] == 'getAllMarcaProducto'){
            echo json_encode($marca->getAllMarcaProducto());
            }
            break;
        
         case 'POST':
            // Leemos JSON del body
            $data = json_decode(file_get_contents('php://input'), true);

            // Registro de marca de vehículo
            if (isset($_GET['task']) && $_GET['task'] === 'registerMarcaVehiculo') {
                if (!empty($data['nombre'])) {
                    $newId = $marca->registerMarcaVehiculo($data);
                    echo json_encode(['success' => $newId > 0, 'idmarca' => $newId]);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Falta el nombre']);
                }
                break;
            }

            // Registro de marca de producto
            if (isset($_GET['task']) && $_GET['task'] === 'registerMarcaProducto') {
         
                if (!empty($data['nombre'])) {
                    $newId = $marca->registerMarcaProducto($data);
                    echo json_encode(['success' => $newId > 0, 'idmarca' => $newId]);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Falta nombre']);
                }
                break;
            }

            // Si no da con ninguna
            http_response_code(400);
            echo json_encode(['error' => 'Tarea inválida']);
            break;
    }
}