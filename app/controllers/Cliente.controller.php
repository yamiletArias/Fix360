<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset=utf-8');

    require_once "../models/Cliente.php";
    require_once "../helpers/helper.php";
    $cliente = new Cliente();

    if (isset($_SERVER['REQUEST_METHOD'])) {  // Inicio de control de método
        switch ($_SERVER['REQUEST_METHOD']) {
          case 'GET':  // -- GET: listado de clientes o detalle por ID
            // 1) Obtener cliente por ID (task=getClienteById)
            if (isset($_GET['task']) && $_GET['task'] === 'getClienteById') {
              $idcliente = intval($_GET['idcliente'] ?? 0);
              $data = $cliente->getClienteById($idcliente);
              echo json_encode($data);
              exit;  // <---- Salimos para no procesar más JSON
            }  // cierra if getClienteById
      
            // 2) Listar todos los clientes según tipo (persona o empresa)
            $tipo = Helper::limpiarCadena($_GET['tipo'] ?? 'persona');
            if ($tipo === 'persona') {
              $data = $cliente->getAllClientesPersona();
              echo json_encode($data);
            } elseif ($tipo === 'empresa') {
              $data = $cliente->getAllClientesEmpresa();
              echo json_encode($data);
            } else {
              http_response_code(400);  // Bad Request
              echo json_encode(['error' => 'Tipo de cliente no válido']);
            }  // cierra if tipo
      
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
                    "numruc"            => Helper::limpiarCadena($dataJSON['numruc'] ?? ""),
                    "direccion"         => Helper::limpiarCadena($dataJSON['direccion'] ?? ""),
                    "correo"            => Helper::limpiarCadena($dataJSON['correo'] ?? ""),
                    "telprincipal"      => Helper::limpiarCadena($dataJSON['telprincipal'] ?? ""),
                    "telalternativo"    => Helper::limpiarCadena($dataJSON['telalternativo'] ?? ""),
                    "idcontactabilidad" => $dataJSON["idcontactabilidad"]
                ];
                $n = $cliente->registerClientePersona($registro);
            } elseif ($tipo === "empresa") {
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
            } else {
                echo json_encode(["status" => false, "message" => "Tipo de cliente no válido"]);
                exit;
            }

            echo json_encode(["rows" => $n]);
            break;
    }
    }
}