<?php

require_once "../models/Colaborador.php";

session_start();
header("Content-Type: application/json");

// Instancia del modelo
$colaborador = new Colaborador();

// Inicializar sesión si no existe
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

// Verificar si se envió una operación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['operation'])) {
    switch ($_POST['operation']) {
        case "login":
            $namuser = htmlspecialchars($_POST['namuser']); 
            $passuser = $_POST['passuser']; // No limpiar la contraseña

            $estadoLogin = ["esCorrecto" => false, "mensaje" => ""];

            $registroLogin = $colaborador->login($namuser);

<<<<<<< HEAD
        if (!empty($registroLogin) && isset($registroLogin[0])) {
            $claveCifrada = $registroLogin[0]['passuser'];
        
            if (password_verify($passuser, $claveCifrada)) {
                $_SESSION["login"] = [
                    "status"        => true,
                    "idcolaborador" => $registroLogin[0]['idcolaborador'],
                    "namuser"       => $registroLogin[0]['namuser'],
                    "nombres"       => $registroLogin[0]['nombres'],
                    "apellidos"     => $registroLogin[0]['apellidos'],
                    "rol"           => $registroLogin[0]['rol']
                ];
        
                $estadoLogin["esCorrecto"] = true;
                $estadoLogin["mensaje"] = "Bienvenido";
            } else {
                $estadoLogin["mensaje"] = "Contraseña incorrecta";
            }
        } else {
            $estadoLogin["mensaje"] = "Colaborador no existe";
        }
        
=======
            if (empty($registroLogin)) {
                $estadoLogin["mensaje"] = "Colaborador no existe";
            } else {
                $claveCifrada = $registroLogin[0]['passuser'];
>>>>>>> 95ec6a1423848a9f90034c96be2b25e1990f71dd

                if (password_verify($passuser, $claveCifrada)) {
                    $_SESSION["login"] = [
                        "status"        => true,
                        "idcolaborador" => $registroLogin[0]['idcolaborador'],
                        "namuser"       => $registroLogin[0]['namuser'],
                        "nombres"       => $registroLogin[0]['nombres'],
                        "apellidos"     => $registroLogin[0]['apellidos'],
                        "rol"           => $registroLogin[0]['rol']
                    ];

                    $estadoLogin["esCorrecto"] = true;
                    $estadoLogin["mensaje"] = "Bienvenido";
                } else {
                    $estadoLogin["mensaje"] = "Contraseña incorrecta";
                }
            }

            echo json_encode($estadoLogin);
            exit;

        case "register":
            $result = $colaborador->add([
                "idcontrato" => Conexion::limpiarCadena($_POST["idcontrato"]),
                "namuser"    => Conexion::limpiarCadena($_POST["namuser"]),
                "passuser"   => $_POST["passuser"], // Se encripta en el modelo
                "estado"     => Conexion::limpiarCadena($_POST["estado"])
            ]);
            echo json_encode($result);
            exit;

        case "update":
            $result = $colaborador->update([
                "idcolaborador" => Conexion::limpiarCadena($_POST["idcolaborador"]),
                "idcontrato"    => Conexion::limpiarCadena($_POST["idcontrato"]),
                "namuser"       => Conexion::limpiarCadena($_POST["namuser"]),
                "passuser"      => $_POST["passuser"], // Se encripta en el modelo
                "estado"        => Conexion::limpiarCadena($_POST["estado"])
            ]);
            echo json_encode($result);
            exit;

        case "delete":
            $idcolaborador = Conexion::limpiarCadena($_POST["idcolaborador"]);
            echo json_encode($colaborador->delete($idcolaborador));
            exit;

        case "logout":
            $_SESSION["login"] = [
                "status"        => false,
                "idcolaborador" => -1,
                "namuser"       => "",
                "nombres"       => "",
                "apellidos"     => "",
                "rol"           => ""
            ];
            echo json_encode(["status" => true, "message" => "Sesión cerrada"]);
            exit;

        default:
            echo json_encode(["esCorrecto" => false, "mensaje" => "Operación no válida"]);
            exit;
    }
}

// Métodos GET para obtener datos
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["namuser"])) {
        echo json_encode($colaborador->find($_GET["namuser"]));
    } else {
        echo json_encode($colaborador->getAll());
    }
    exit;
}

// Si no se recibe una operación válida
echo json_encode(["esCorrecto" => false, "mensaje" => "Método no permitido"]);
exit;
