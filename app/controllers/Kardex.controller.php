<?php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/Kardex.php';

$method = $_SERVER['REQUEST_METHOD'];
$kardex = new Kardex();

switch ($method) {
    case 'GET':
        // Espera ?task=getStock&idproducto=123
        if (isset($_GET['task']) && $_GET['task'] === 'getStock') {
            $id = intval($_GET['idproducto'] ?? 0);
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'idproducto inválido']);
                exit;
            }
            $stock = $kardex->getStockByProduct($id);
            if ($stock === null) {
                echo json_encode(['stock_actual' => null, 'stockmin' => null, 'stockmax' => null]);
            } else {
                echo json_encode($stock);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Parámetros incorrectos']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
