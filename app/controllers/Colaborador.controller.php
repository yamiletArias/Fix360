<?php
require_once "../models/Colaborador.php";
require_once "../helpers/helper.php";

session_start();
header("Content-Type: application/json");

$colaborador = new Colaborador();

// Inicializar sesión
if (!isset($_SESSION['login'])) {
    $_SESSION['login'] = [
        "status"        => false,
        "idcolaborador" => -1,
        "namuser"       => "",
        "nombres"       => "",
        "apellidos"     => "",
        "rol"           => ""
    ];
}

// 1) Listar colaboradores activos vigentes
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['action']) && $_GET['action'] === 'list') {
    try {
        $lista = $colaborador->getAll();
        echo json_encode([ 'status' => 'success', 'data' => $lista ]);
    } catch (Exception $e) {
        echo json_encode([ 'status' => 'error', 'message' => $e->getMessage() ]);
    }
    exit;
}

// 2) POST: login, register, logout
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['operation'])) {
    switch ($_POST['operation']) {
        case 'login':
            $namuser  = Helper::limpiarCadena($_POST['namuser']);
            $passuser = $_POST['passuser'];

            $res = $colaborador->login($namuser, $passuser);
            if ($res['status'] === 'SUCCESS') {
                // Asignar datos de sesión (suponiendo el SP devuelve estos campos)
                $_SESSION['login'] = [
                    'status'        => true,
                    'idcolaborador' => $res['idcolaborador'],
                    'namuser'       => $res['namuser']   ?? $namuser,
                    'nombres'       => $res['nombres']   ?? '',
                    'apellidos'     => $res['apellidos'] ?? '',
                    'rol'           => $res['rol']       ?? ''
                ];
                echo json_encode([ 'status' => true, 'message' => 'Bienvenido' ]);
            } else {
                echo json_encode([ 'status' => false, 'message' => 'Credenciales inválidas o contrato no vigente' ]);
            }
            break;

        case 'register':
            $params = [
                'idcontrato' => Helper::limpiarCadena($_POST['idcontrato']),
                'namuser'    => Helper::limpiarCadena($_POST['namuser']),
                'passuser'   => $_POST['passuser']
            ];
            $result = $colaborador->add($params);
            echo json_encode($result);
            break;

        case 'logout':
            $_SESSION['login'] = [
                "status"        => false,
                "idcolaborador" => -1,
                "namuser"       => "",
                "nombres"       => "",
                "apellidos"     => "",
                "rol"           => ""
            ];
            echo json_encode([ 'status' => true, 'message' => 'Sesión cerrada' ]);
            break;

        default:
            echo json_encode([ 'status' => false, 'message' => 'Operación no válida' ]);
            break;
    }
    exit;
}

// Si no coincide ningún endpoint
echo json_encode([ 'status' => 'error', 'message' => 'Método u operación no soportada' ]);
exit;