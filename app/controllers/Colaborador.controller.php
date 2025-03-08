<?php

require_once "../models/Colaborador.php";

session_start();
header("Content-Type: application/json");

$colaborador = new Colaborador();

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
            $passuser = $_POST['passuser'];

            $estadoLogin = ["esCorrecto" => false, "mensaje" => ""];

            $registroLogin = $colaborador->login($namuser);

            if ($registroLogin) {
                $claveCifrada = $registroLogin['passuser'];

                if (password_verify($passuser, $claveCifrada)) {
                    $_SESSION["login"] = [
                        "status"        => true,
                        "idcolaborador" => $registroLogin['idcolaborador'],
                        "namuser"       => $registroLogin['namuser'],
                        "nombres"       => $registroLogin['nombres'],
                        "apellidos"     => $registroLogin['apellidos'],
                        "rol"           => $registroLogin['rol']
                    ];

                    $estadoLogin["esCorrecto"] = true;
                    $estadoLogin["mensaje"] = "Bienvenido";
                } else {
                    $estadoLogin["mensaje"] = "Contraseña incorrecta";
                }
            } else {
                $estadoLogin["mensaje"] = "Colaborador no existe";
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
                "passuser"      => $_POST["passuser"], 
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
            echo json_encode(["status" => true, "mensaje" => "Sesión cerrada correctamente"]);
            exit;

        default:
            echo json_encode(["esCorrecto" => false, "mensaje" => "Operación no válida"]);
            exit;
    }
}
