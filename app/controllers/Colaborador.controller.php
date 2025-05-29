<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once "../models/Colaborador.php";
require_once "../helpers/helper.php";

$col = new Colaborador();

// 1) Logout explícito
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION['login'] = [
        'status'        => false,
        'idcolaborador' => -1,
        'namuser'       => '',
        'nombreCompleto'=> '',
        'permisos'      => []
    ];
    echo json_encode([ 'status' => true, 'message' => 'Sesión cerrada' ]);
    exit;
}

// 2) Listar todos los colaboradores
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'list') {
    try {
        $data = $col->getAll();
        echo json_encode([ 'status' => 'success', 'data' => $data ]);
    } catch (Exception $e) {
        echo json_encode([ 'status' => 'error', 'message' => $e->getMessage() ]);
    }
    exit;
}

// 3) Obtener uno por ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get') {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        echo json_encode([ 'status' => 'error', 'message' => 'ID inválido' ]);
    } else {
        $colData = $col->getColaboradorById($id);
        if ($colData) {
            echo json_encode([ 'status' => 'success', 'data' => $colData ]);
        } else {
            echo json_encode([ 'status' => 'error', 'message' => 'No se encontró colaborador' ]);
        }
    }
    exit;
}

// 4) Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $nam    = Helper::limpiarCadena($_POST['namuser'] ?? '');
    $pwd    = $_POST['passuser'] ?? '';
    $res    = $col->login($nam, $pwd);
    if ($res['status'] === true) {
        $_SESSION['login'] = [
            'status'         => true,
            'idcolaborador'  => $res['idcolaborador'],
            'nombreCompleto' => $res['nombreCompleto'],
            'permisos'       => $res['permisos']
        ];
        echo json_encode([
            'status'         => true,
            'message'        => '¡Bienvenido!',
            'idcolaborador'  => $res['idcolaborador'],
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

// 5) Crear un colaborador completo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    // Limpiar y preparar params
    $p = Helper::limpiarCadena($_POST, [
        'namuser','passuser','idrol','fechainicio','fechafin',
        'nombres','apellidos','tipodoc','numdoc',
        'numruc','direccion','correo','telprincipal','telalternativo'
    ]);
    $result = $col->add($p);
    echo json_encode($result);
    exit;
}

// 6) Actualizar un colaborador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $p = Helper::limpiarCadena($_POST, [
        'idcolaborador','nombres','apellidos','tipodoc','numdoc',
        'numruc','direccion','correo','telprincipal','telalternativo',
        'idrol','fechainicio','fechafin',
        'namuser','passuser','estado'
    ]);
    $result = $col->update($p);
    echo json_encode($result);
    exit;
}

// 7) Dar de baja (desactivar) un colaborador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deactivate') {
    $id  = isset($_POST['idcolaborador']) ? (int) $_POST['idcolaborador'] : 0;
    $fin = $_POST['fechafin'] ?? null;
    $result = $col->deactivate($id, $fin);
    echo json_encode($result);
    exit;
}

// Si ningún endpoint coincide
echo json_encode([ 'status' => 'error', 'message' => 'Operación no soportada' ]);
exit;
