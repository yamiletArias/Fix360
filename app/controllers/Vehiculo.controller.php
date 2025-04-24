<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset=utf-8');

    require_once "../models/Vehiculo.php";
    require_once "../helpers/helper.php";
    
    $vehiculo = new Vehiculo();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if($_GET['task'] == 'getAll') {echo json_encode($vehiculo->getAll());}

            if($_GET['task'] == 'getVehiculoByCliente'){
                echo json_encode($vehiculo->getVehiculoByCliente($_GET['idcliente']));
            }
            break;

            case 'POST':
                $input = file_get_contents('php://input');
                
                $dataJSON = json_decode($input, true);
            
                if ($dataJSON === null) {
                    echo json_encode(["error" => "JSON inválido"]);
                    error_log("JSON Recibido: " . $input);
                    exit;
                }
            
                $registro = [
                    "idmodelo"          => Helper::limpiarCadena($dataJSON["idmodelo"] ?? ""),
                    "idtcombustible"   => Helper::limpiarCadena($dataJSON["tipocombustible"] ?? ""),
                    "placa"             => Helper::limpiarCadena($dataJSON["placa"] ?? ""),
                    "anio"              => Helper::limpiarCadena($dataJSON["anio"] ?? ""),
                    "numserie"          => Helper::limpiarCadena($dataJSON["numserie"] ?? ""),
                    "color"             => Helper::limpiarCadena($dataJSON["color"] ?? ""),
                    "vin"               => Helper::limpiarCadena($dataJSON["vin"] ?? ""),
                    "numchasis"         => Helper::limpiarCadena($dataJSON["numchasis"] ?? ""),
                    "idcliente"         => Helper::limpiarCadena($dataJSON["idcliente"] ?? ""),
                ];
            
                $n = $vehiculo->registerVehiculo($registro);
            
                if ($n === 0) {
                    echo json_encode(["error" => "No se pudo registrar el vehículo"]);
                    error_log("JSON Recibido: " . $input);
                } else {
                    echo json_encode(["success" => "Vehículo registrado", "rows" => $n]);
                }
                break;
            
    }
}
?>
