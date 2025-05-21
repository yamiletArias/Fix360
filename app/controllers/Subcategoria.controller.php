<?php
if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset = utf-8');

    require_once "../models/Subcategoria.php";
    $subcategoria = new Subcategoria();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($_GET['task'] == 'getSubcategoriaByCategoria') {
                echo json_encode($subcategoria->getSubcategoriaByCategoria($_GET['idcategoria']));
            }
            if ($_GET['task'] == 'getServicioSubcategoria') {
                echo json_encode($subcategoria->getServicioSubcategoria());
            }

            break;
        case 'POST':
            if (isset($_GET['task']) && $_GET['task'] === 'add') {
                $data = json_decode(file_get_contents('php://input'), true);
                if (!empty($data['idcategoria']) && !empty($data['subcategoria'])) {
                    $newId = $subcategoria->add($data);
                    echo json_encode([
                        'success'        => $newId > 0,
                        'idsubcategoria' => $newId
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Falta idcategoria o subcategoria'
                    ]);
                }
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Tarea inválida'
                ]);
            }
            break;

        default:
            break;
    }
}
