<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once "../models/Colaborador.php";
require_once "../helpers/helper.php";

$col = new Colaborador();

// 1) Logout
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION['login'] = [
        'status'        => false,
        'idcolaborador' => -1,
        'namuser'       => '',
        'nombreCompleto' => '',
        'permisos'      => []
    ];
    echo json_encode(['status' => true, 'message' => 'Sesión cerrada']);
    exit;
}

// 2) Listar todos
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'list') {
    try {
        $data = $col->getAll();
        echo json_encode(['status' => 'success', 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// 3) Detalle por ID (modal)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'detail') {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
    } else {
        $row = $col->getColaboradorById($id);
        if ($row) {
            echo json_encode(['status' => 'success', 'data' => $row]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontró colaborador']);
        }
    }
    exit;
}

// 4) Obtener datos para editar
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get') {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
    } else {
        $colData = $col->getById($id);
        if ($colData) {
            echo json_encode(['status' => 'success', 'data' => $colData]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontró colaborador']);
        }
    }
    exit;
}

// 5) Crear colaborador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $p = [
        'namuser'      => Helper::limpiarCadena($_POST['namuser']      ?? ''),
        'passuser'     => Helper::limpiarCadena($_POST['passuser']     ?? ''),
        'idrol'        => intval($_POST['idrol']                       ?? 0),
        'fechainicio'  => Helper::limpiarCadena($_POST['fechainicio']  ?? ''),
        'fechafin'     => Helper::limpiarCadena($_POST['fechafin']     ?? ''),
        'nombres'      => Helper::limpiarCadena($_POST['nombres']      ?? ''),
        'apellidos'    => Helper::limpiarCadena($_POST['apellidos']    ?? ''),
        'tipodoc'      => Helper::limpiarCadena($_POST['tipodoc']      ?? ''),
        'numdoc'       => Helper::limpiarCadena($_POST['numdoc']       ?? ''),
        'direccion'    => Helper::limpiarCadena($_POST['direccion']    ?? ''),
        'correo'       => Helper::limpiarCadena($_POST['correo']       ?? ''),
        'telprincipal' => Helper::limpiarCadena($_POST['telprincipal'] ?? '')
    ];
    $inserted = $col->add($p);
    if ($inserted > 0) {
        echo json_encode(['status' => true, 'message' => 'Registrado correctamente.']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Error al registrar.']);
    }
    exit;
}

// 6) Actualizar colaborador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $p = [
        'idcolaborador' => intval(Helper::limpiarCadena($_POST['idcolaborador'] ?? '')),
        'nombres'      => Helper::limpiarCadena($_POST['nombres']      ?? ''),
        'apellidos'    => Helper::limpiarCadena($_POST['apellidos']    ?? ''),
        'direccion'    => Helper::limpiarCadena($_POST['direccion']    ?? ''),
        'correo'       => Helper::limpiarCadena($_POST['correo']       ?? ''),
        'telprincipal' => Helper::limpiarCadena($_POST['telprincipal'] ?? ''),
        'idrol'        => intval(Helper::limpiarCadena($_POST['idrol'] ?? '')),
        'fechainicio'  => Helper::limpiarCadena($_POST['fechainicio']  ?? ''), // Sigue llegando ''
        'fechafin'     => empty($_POST['fechafin']) ? null : Helper::limpiarCadena($_POST['fechafin']), // Cambio aquí
        'namuser'      => Helper::limpiarCadena($_POST['namuser']      ?? ''),
        'passuser'     => Helper::limpiarCadena($_POST['passuser']     ?? '')
    ];

    error_log("Parámetros para actualizar colaborador: " . print_r($p, true)); // Añade esto
    $updated = $col->update($p);
    if ($updated > 0) {
        echo json_encode(['status' => true, 'message' => 'Actualizado correctamente.']);
    } else {
        echo json_encode(['status' => false, 'message' => 'No se realizaron cambios o hubo un error.']);
    }
    exit;
}

// 7) Desactivar colaborador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deactivate') {
    $id  = isset($_POST['idcolaborador']) ? (int) $_POST['idcolaborador'] : 0;
    $fin = $_POST['fechafin'] ?? null;
    $result = $col->deactivate($id, $fin);
    echo json_encode($result);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $nam    = Helper::limpiarCadena($_POST['namuser'] ?? '');
    $pwd    = $_POST['passuser'] ?? '';
    $res    = $col->login($nam, $pwd);
    if ($res['status'] === true) {
        $_SESSION['login'] = [
            'status'         => true,
            'idcolaborador'  => $res['idcolaborador'],
            'idrol'          => $res['idrol'],
            'nombreCompleto' => $res['nombreCompleto'],
            'permisos'       => $res['permisos']
        ];
        echo json_encode([
            'status'         => true,
            'message'        => '¡Bienvenido!',
            'idcolaborador'  => $res['idcolaborador'],
            'idrol'          => $res['idrol'],
            'nombreCompleto' => $res['nombreCompleto'],
            'permisos'       => $res['permisos']
        ]);
    } else {
        echo json_encode([
            'status'  => false,
            'message' => $res['message'] ?? 'Credenciales inválidas o contrato no vigente'
        ]);
    }
    exit;
}

// Operación no soportada
echo json_encode(['status' => 'error', 'message' => 'Operación no soportada']);
exit;
