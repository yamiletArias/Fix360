<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset=utf-8');

    require_once "../models/Cliente.php";
    require_once "../helpers/helper.php";
    $cliente = new Cliente();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // echo json_encode($cliente->getAll());
            break;

        case 'POST':
            $input = file_get_contents('php://input');
            $dataJSON = json_decode($input, true);

            $tipo = Helper::limpiarCadena($dataJSON['tipo'] ?? "");

            if ($tipo === "persona") {
                // Registro de cliente como persona
                $registro = [
                    "nombres"           => Helper::limpiarCadena($dataJSON['nombres'] ?? ""),
                    "apellidos"         => Helper::limpiarCadena($dataJSON['apellidos'] ?? ""),
                    "tipodoc"           => Helper::limpiarCadena($dataJSON['tipodoc'] ?? ""),
                    "numdoc"            => Helper::limpiarCadena($dataJSON['numdoc'] ?? ""),
                    "direccion"         => Helper::limpiarCadena($dataJSON['direccion'] ?? ""),
                    "correo"            => Helper::limpiarCadena($dataJSON['correo'] ?? ""),
                    "telprincipal"      => Helper::limpiarCadena($dataJSON['telprincipal'] ?? ""),
                    "telalternativo"    => Helper::limpiarCadena($dataJSON['telalternativo'] ?? ""),
                    "idcontactabilidad" => $dataJSON["idcontactabilidad"]
                ];
                $n = $cliente->registerClientePersona($registro);
            } 
            elseif ($tipo === "empresa") {
                // Registro de cliente como empresa
                $registro = [
                    "ruc"               => Helper::limpiarCadena($dataJSON['ruc'] ?? ""),
                    "nomcomercial"      => Helper::limpiarCadena($dataJSON['nomcomercial'] ?? ""),
                    "razonsocial"       => Helper::limpiarCadena($dataJSON['razonsocial'] ?? ""),
                    "telefono"          => Helper::limpiarCadena($dataJSON['telefono'] ?? ""),
                    "correo"            => Helper::limpiarCadena($dataJSON['correo'] ?? ""),
                    "idcontactabilidad" => $dataJSON["idcontactabilidad"]
                ];
                $n = $cliente->registerClienteEmpresa($registro);
            } 
            else {
                echo json_encode(["status" => false, "message" => "Tipo de cliente no válido"]);
                exit;
            }

            echo json_encode(["rows" => $n]);
            break;
    }
}
