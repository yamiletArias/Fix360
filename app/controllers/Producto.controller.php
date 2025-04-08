<?php

if(isset($_SERVER['REQUEST_METHOD'])){
    header('Content-type: application/json; charset=utf-8');

    require_once "../models/Producto.php";
    require_once "../helpers/helper.php";

    $producto = new Producto();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if($_GET['task'] == 'getAll'){echo json_encode($producto->getAll());}
            break;
        
        case 'POST':
            $input = file_get_contents('php://input');

            $dataJSON = json_decode($input, true);

            if($dataJSON === null){
                echo json_encode(["error" => "JSON invalido"]);
                error_log("JSON recibido: " . $input);
                exit;
            }

            $registro = [
                "idsubcategoria"       => Helper::limpiarCadena($dataJSON["idsubcategoria"] ?? ""),
                "idmarca"       => Helper::limpiarCadena($dataJSON["idmarca"] ?? ""),
                "descripcion"       => Helper::limpiarCadena($dataJSON["descripcion"] ?? ""),
                "precio"       => Helper::limpiarCadena($dataJSON["precio"] ?? ""),
                "presentacion"       => Helper::limpiarCadena($dataJSON["presentacion"] ?? ""),
                "undmedida"       => Helper::limpiarCadena($dataJSON["undmedida"] ?? ""),
                "cantidad"       => Helper::limpiarCadena($dataJSON["cantidad"] ?? ""),
                "img"       => Helper::limpiarCadena($dataJSON["img"] ?? "")
            ];

            $n = $producto->add($registro);
            if ($n === 0) {
                echo json_encode(["error" => "No se pudo registrar el vehículo"]);
                error_log("JSON Recibido: " . $input);
            } else {
                echo json_encode(["success" => "Vehículo registrado", "rows" => $n]);
            }
            break;
        default:
            # code...
            break;
    }
}