<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset = utf-8');

    require_once "../models/Tcombustible.php";
    $tcombustible = new Tcombustible();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            echo json_encode($tcombustible->getAll());
            break;

        case 'POST':

            $data = json_decode(file_get_contents('php://input'), true);
            if (!empty($data['tcombustible'])) {
                $newId = $tcombustible->registerTcombustible($data);
                echo json_encode([
                    'success' => $newId > 0, 
                    'idtcombustible' => $newId
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Falta el tipo de combustible']);
            }
            break;

        default:
            # code...
            break;
    }
}
