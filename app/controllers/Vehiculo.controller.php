<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset=utf-8');

    require_once "../models/Vehiculo.php";
    require_once "../helpers/helper.php";
    
    $vehiculo = new Vehiculo();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Puedes agregar funcionalidad para listar vehículos si lo requieres.
            break;

        case 'POST':
            $input = file_get_contents('php://input');
            $dataJSON = json_decode($input, true);
            
            // Limpiar y extraer los datos enviados
            $registro = [
                "idmodelo"       => Helper::limpiarCadena($dataJSON["idmodelo"] ?? ""),
                "placa"          => Helper::limpiarCadena($dataJSON["placa"] ?? ""),
                "anio"           => Helper::limpiarCadena($dataJSON["anio"] ?? ""),
                "kilometraje"    => $dataJSON["kilometraje"] ?? 0,
                "numserie"       => Helper::limpiarCadena($dataJSON["numserie"] ?? ""),
                "color"          => Helper::limpiarCadena($dataJSON["color"] ?? ""),
                "tipocombustible"=> Helper::limpiarCadena($dataJSON["tipocombustible"] ?? ""),
                "criterio"       => Helper::limpiarCadena($dataJSON["criterio"] ?? ""),
                "tipoBusqueda"   => Helper::limpiarCadena($dataJSON["tipoBusqueda"] ?? "")
            ];
            
            $n = $vehiculo->registerVehiculoYPropietario($registro);
            
            echo json_encode(["rows" => $n]);
            break;
    }
}
?>
