<?php

if (isset($_SERVER['REQUEST_METHOD'])) {

    header('Content-Type: application/json; charset=utf-8');

    require_once '../models/Compra.php';
    require_once "../helpers/helper.php";
    $compra = new Compra();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Limpiar y obtener tipo
            $tipo = Helper::limpiarCadena($_GET['type'] ?? "");

            // Caso para proveedores
            if ($tipo == 'proveedor') {
                echo json_encode($compra->getProveedoresCompra());
            } 
            // Caso para buscar productos
            else if (isset($_GET['q']) && !empty($_GET['q'])) {
                $termino = $_GET['q'];
                if ($tipo == 'producto') {
                    // Buscar productos
                    echo json_encode($compra->buscarProductoCompra($termino));
                } else {
                    echo json_encode(["error" => "Tipo no válido para búsqueda"]);
                }
            } 
            // Si no se proporciona 'type' o 'q', obtener todo
            else {
                echo json_encode(["error" => "Falta el parámetro de búsqueda o 'type'"]);
            }
            break;

    }
}
?>
