<?php

require_once "../models/Colaborador.php";

session_start();
header("Content-Type: application/json");

// Instancia del modelo
$colaborador = new Colaborador();

// Inicializar sesión si no existe
if (!isset($_SESSION['login'])) {
    $_SESSION['login'] = [
        "status" => false,
        "idcolaborador" => -1,
        "namuser" => "",
        "nombres" => "",
        "apellidos" => "",
        "rol" => ""
    ];
}

// Verificar si se envió una operación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['operation'])) {
    if ($_POST['operation'] === 'login') {
        $namuser = htmlspecialchars($_POST['namuser']); 
        $passuser = $_POST['passuser']; // No limpiar la contraseña

        $estadoLogin = ["esCorrecto" => false, "mensaje" => ""];

        $registroLogin = $colaborador->login($namuser);

        if (empty($registroLogin)) {
            $estadoLogin["mensaje"] = "Colaborador no existe";
        } else {
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
        }

        echo json_encode($estadoLogin);
        exit;
    }
}

// Si no se recibe una operación válida
echo json_encode(["esCorrecto" => false, "mensaje" => "Operación no válida"]);
exit;
