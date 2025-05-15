
<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

session_start();
require_once "../models/Colaborador.php";
require_once "../helpers/helper.php";


$colaborador = new Colaborador();

// Inicializar sesión
if ($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['operation']) && $_GET['operation']==='logout') {
    // Limpio sesión
    $_SESSION['login'] = [
        "status"        => false,
        "idcolaborador" => -1,
        "namuser"       => "",
        "nombres"       => "",
        "apellidos"     => "",
        "rol"           => ""
    ];
    // Redirijo al login
    header('Location: ' . SERVERURL);
    exit;
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
    if ($res['status'] === true) {
        $_SESSION['login'] = [
            'status'        => true,
            'idcolaborador' => $res['idcolaborador'],
            'namuser'       => $namuser,
            // si quieres traer más campos del SP, tendrás que ajustarlo
        ];
        echo json_encode([
            'status'  => true,
            'message' => '¡Bienvenido!',
            'idcolaborador' => $res['idcolaborador']
        ]);
    } else {
        echo json_encode([
            'status'  => false,
            'message' => $res['message'] 
                ?? 'Credenciales inválidas o contrato no vigente'
        ]);
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