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
    // recibes un JSON { "nombre": "Fiat" }
    $data = json_decode(file_get_contents('php://input'), true);
    if (!empty($data['nombre'])) {
        $newId = $marca->registerMarcaVehiculo($data);
        echo json_encode(['success' => $newId > 0, 'idmarca' => $newId]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Falta nombre']);
    }
    break;

    }
}