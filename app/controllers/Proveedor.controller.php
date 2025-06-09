<?php
// Proveedor.controller.php
header('Content-Type: application/json; charset=utf-8');
// Limpia buffers anteriores para asegurar solo JSON
if (ob_get_length())
    ob_clean();

require_once "../models/Proveedor.php";
require_once "../models/Empresa.php";
require_once "../helpers/helper.php";

$empresa = new Empresa();
$proveedor = new Proveedores();

// Solo atendemos POST y GET
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    $op = $_POST['operation'] ?? '';
    if ($op !== 'registerEmpresa' && $op !== 'update' && $op !== 'delete') {
        echo json_encode(['status' => false, 'message' => 'Operación no válida']);
        exit;
    }

    switch ($op) {
        case 'registerEmpresa':
            // Mapea explícito desde el formulario
            $ruc = Helper::limpiarCadena($_POST['ruc'] ?? '');
            $nomcomercial = Helper::limpiarCadena($_POST['nomcomercial'] ?? '');
            $razonsocial = Helper::limpiarCadena($_POST['razonsocial'] ?? '');
            $telefono = Helper::limpiarCadena($_POST['telempresa'] ?? '');
            $correo = Helper::limpiarCadena($_POST['correoemp'] ?? '');

            // 1) Registrar empresa
            $resultEmpresa = $empresa->add([
                'ruc' => $ruc,
                'nomcomercial' => $nomcomercial,
                'razonsocial' => $razonsocial,
                'telefono' => $telefono,
                'correo' => $correo
            ]);
            if (!$resultEmpresa['status']) {
                echo json_encode($resultEmpresa);
                exit;
            }

            // 2) Registrar proveedor
            $idempresa = $resultEmpresa['idempresa'];
            $resultProv = $proveedor->add($idempresa);

            // 3) Respuesta final
            echo json_encode([
                'status' => $resultProv['status'],
                'message' => $resultProv['message'],
                'idempresa' => $idempresa,
                'idproveedor' => $resultProv['idproveedor'],
                'nomcomercial' => $nomcomercial
            ]);
            exit;

        case 'update':
            $idprov = Helper::limpiarCadena($_POST['idproveedor'] ?? '');
            $idemp = Helper::limpiarCadena($_POST['idempresa'] ?? '');
            $result = $proveedor->update($idprov, $idemp);
            echo json_encode($result);
            exit;

        case 'delete':
            $idprov = Helper::limpiarCadena($_POST['idproveedor'] ?? '');
            $result = $proveedor->delete($idprov);
            echo json_encode($result);
            exit;
    }

} elseif ($method === 'GET') {
    if (isset($_GET['idproveedor'])) {
        $idprov = Helper::limpiarCadena($_GET['idproveedor']);
        echo json_encode($proveedor->find($idprov));
    } else {
        echo json_encode($proveedor->getAll());
    }
    exit;
} else {
    echo json_encode(['status' => false, 'message' => 'Método no permitido']);
    exit;
}
