<?php
if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset = utf-8');

    require_once "../models/Categoria.php";
    $categoria = new Categoria();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($_GET['task'] == 'getAll') {
                echo json_encode($categoria->getAll());
            }
            break;

        case 'POST':
            if (isset($_GET['task']) && $_GET['task'] === 'add') {
                $data = json_decode(file_get_contents('php://input'), true);

                if (!empty($data['categoria'])) {
                    // Invocamos el modelo y capturamos el nuevo ID
                    $newId = $categoria->add($data);

                    // Devolvemos un JSON uniforme
                    echo json_encode([
                        'success'     => $newId > 0,
                        'idcategoria' => $newId
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Falta el nombre de la categoría.'
                    ]);
                }
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Tarea inválida.'
                ]);
            }
            break;


        default:
            http_response_code(405); // Método no permitido
            echo json_encode([
                'status'  => false,
                'message' => 'Método HTTP no soportado.'
            ]);
            break;
    }
}
