<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset=utf-8');
require_once "../models/Persona.php";
require_once "../models/Empresa.php";
    require_once "../models/Cliente.php";
    require_once "../helpers/helper.php";
    $cliente = new Cliente();
    $persona = new Persona();
$empresa = new Empresa();

    if (isset($_SERVER['REQUEST_METHOD'])) {  // Inicio de control de método
        switch ($_SERVER['REQUEST_METHOD']) {
          case "GET":
    // 1) Detalle único (editar)
    if (isset($_GET['task']) && $_GET['task'] === 'getById') {
      $tipo = $_GET['tipo'] ?? '';
      if ($tipo === 'persona') {
        $id = intval($_GET['idpersona'] ?? 0);
        echo $id > 0
          ? json_encode($cliente->getPersonaById($id))
          : json_encode(['status' => false, 'message' => 'ID persona inválido']);
      }
      elseif ($tipo === 'empresa') {
        $id = intval($_GET['idempresa'] ?? 0);
        echo $id > 0
          ? json_encode($cliente->getEmpresaById($id))
          : json_encode(['status' => false, 'message' => 'ID empresa inválido']);
      }
      else {
        echo json_encode(['status' => false, 'message' => 'Tipo no válido']);
      }
      break;
    }

    // 2) Listado para DataTables
    if (isset($_GET['tipo']) && $_GET['tipo'] === 'persona') {
      echo json_encode($cliente->getAllClientesPersona());
      break;
    }
    if (isset($_GET['tipo']) && $_GET['tipo'] === 'empresa') {
      echo json_encode($cliente->getAllClientesEmpresa());
      break;
    }

    // 3) En caso se quiera un solo endpoint getAll
    if (isset($_GET['task']) && $_GET['task'] === 'getAll') {
      // podrías fusionar ambos arrays si lo necesitas
      echo json_encode([
        'personas' => $cliente->getAllClientesPersona(),
        'empresas' => $cliente->getAllClientesEmpresa(),
      ]);
      break;
    }

    // Default
    echo json_encode(['status' => false, 'message' => 'Parámetros GET inválidos']);
    break;
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