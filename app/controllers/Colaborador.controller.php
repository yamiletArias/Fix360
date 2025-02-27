<?php

require_once "../models/Colaborador.php";

//Colaborador
$colaborador = new Colaborador();

if(!isset($_SESSION['login']) || $_SESSION['login']['status'] == false){
  $_SESSION['login'] = [
    "status"    => false,
    "idcolaborador"     => -1,
    "namuser"           => "",
    "passuser"          => "",
    "nombres"           => "",
    "apellidos"         => "",
    "rol"               => ""
  ];
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if($_POST['operation'] == 'login'){

        $namuser = $colaborador->limpiarCadena($_POST['namuser']);
        $passuser = $colaborador->limpiarCadena($_POST['passuser']);
        $claveCifrada = "";

        $estadoLogin = [
            "esCorrecto" =>false,
            "mensaje" => ""
        ];

        $registroLogin = $colaborador->login(['namuser' => $namuser]);

        if (count($registroLogin) == 0){
            $statusLogin["mensaje"] = "Colaborador no existe";
        }else{
            $claveCifrada = $registro[0]['passuser'];

            if(password_verify($passuser, $claveCifrada)){
                
                $statusLogin["esCorrecto"] = true;
                $statusLogin["mensaje"] = "Bienvenido";

                //Actualizacion de datos
                $_SESSION["login"]["status"] = True;
                $_SESSION["login"]["idcolaborador"] = $registroLogin[0]['idcolaborador'];
                $_SESSION["login"]["namuser"] = $registroLogin[0]['namuser'];
                $_SESSION["login"]["passuser"] = $registroLogin[0]['passuser'];
                $_SESSION["login"]["nombres"] = $registroLogin[0]['nombres'];
                $_SESSION["login"]["apellidos"] = $registroLogin[0]['apellidos'];
            }
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "GET"){
    if($_GET['operation'] == "destroy"){
      session_destroy();
      session_unset();
      header("Location: ../../");
    }
  }