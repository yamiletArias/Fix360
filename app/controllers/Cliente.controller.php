<?php
// Cliente.controller.php

header('Content-Type: application/json; charset=utf-8');

require_once "../models/Conexion.php";
require_once "../models/Persona.php";
require_once "../models/Empresa.php";
require_once "../models/Cliente.php";
require_once "../helpers/helper.php";

$pdo = Conexion::getConexion();
$personaModel = new Persona();
$empresaModel = new Empresa();
$clienteModel = new Cliente();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 1) Detalle único (editar)
    if (isset($_GET['task']) && $_GET['task'] === 'getById') {
        $tipo = $_GET['tipo'] ?? '';
        if ($tipo === 'persona') {
            $id = intval($_GET['idpersona'] ?? 0);
            echo $id > 0
                ? json_encode($clienteModel->getPersonaById($id))
                : json_encode(['status' => false, 'message' => 'ID persona inválido']);
        } elseif ($tipo === 'empresa') {
            $id = intval($_GET['idempresa'] ?? 0);
            echo $id > 0
                ? json_encode($clienteModel->getEmpresaById($id))
                : json_encode(['status' => false, 'message' => 'ID empresa inválido']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Tipo no válido']);
        }
        exit;
    }

    // 2) Listado para DataTables
    if (isset($_GET['tipo']) && $_GET['tipo'] === 'persona') {
        echo json_encode($clienteModel->getAllClientesPersona());
        exit;
    }
    if (isset($_GET['tipo']) && $_GET['tipo'] === 'empresa') {
        echo json_encode($clienteModel->getAllClientesEmpresa());
        exit;
    }

    // 3) En caso se quiera un solo endpoint getAll
    if (isset($_GET['task']) && $_GET['task'] === 'getAll') {
        echo json_encode([
            'personas' => $clienteModel->getAllClientesPersona(),
            'empresas' => $clienteModel->getAllClientesEmpresa(),
        ]);
        exit;
    }

    // 4) Obtener nombre del propietario (persona o empresa) directamente vía SP
    if (isset($_GET['task']) && $_GET['task'] === 'getClienteById' && isset($_GET['idcliente'])) {
        $idcliente = intval($_GET['idcliente']);
        if ($idcliente <= 0) {
            echo json_encode(['status' => false, 'message' => 'ID cliente inválido']);
            exit;
        }

        $stmt = $pdo->prepare("CALL spGetClienteById(:icliente)");
        $stmt->execute([':icliente' => $idcliente]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !isset($row['propietario'])) {
            echo json_encode(['status' => false, 'message' => 'Cliente no encontrado']);
            exit;
        }

        echo json_encode([
            'status'      => true,
            'propietario' => $row['propietario']
        ]);
        exit;
    }

    // Default GET
    echo json_encode(['status' => false, 'message' => 'Parámetros GET inválidos']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $dataJSON = json_decode($input, true);

    $tipo = Helper::limpiarCadena($dataJSON['tipo'] ?? '');

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
            "idcontactabilidad" => intval($dataJSON["idcontactabilidad"] ?? 0)
        ];
        $n = $clienteModel->registerClientePersona($registro);
        echo json_encode(["rows" => $n]);
        exit;
    } elseif ($tipo === "empresa") {
        // Registro de cliente como empresa
        $registro = [
            "ruc"               => Helper::limpiarCadena($dataJSON['ruc'] ?? ""),
            "nomcomercial"      => Helper::limpiarCadena($dataJSON['nomcomercial'] ?? ""),
            "razonsocial"       => Helper::limpiarCadena($dataJSON['razonsocial'] ?? ""),
            "telefono"          => Helper::limpiarCadena($dataJSON['telefono'] ?? ""),
            "correo"            => Helper::limpiarCadena($dataJSON['correo'] ?? ""),
            "idcontactabilidad" => intval($dataJSON["idcontactabilidad"] ?? 0)
        ];
        $n = $clienteModel->registerClienteEmpresa($registro);
        echo json_encode(["rows" => $n]);
        exit;
    } else {
        echo json_encode(["status" => false, "message" => "Tipo de cliente no válido"]);
        exit;
    }
}

echo json_encode(['status' => false, 'message' => 'Método no permitido']);
exit;
