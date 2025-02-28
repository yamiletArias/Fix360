<?php
session_start();
require_once "../models/Roles.php";

$rol = new RolModel();

// Verificar la sesión de usuario
if (!isset($_SESSION['login']) || $_SESSION['login']['status'] == false) {
    $_SESSION['login'] = [
        "status"      => false,
        "idusuario"   => -1,
        "dni"         => "",
        "apellidos"   => "",
        "nombres"     => "",
        "nombreRol"   => "",
        "nombreCorto" => "",
        "nomUser"     => "",
        "permisos"    => []
    ];
}

header('Content-Type: application/json');

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        switch ($_POST["operation"]) {
            case "registerRol":
                $rolNombre = $rol->limpiarCadena($_POST["rol"]);
                echo json_encode($rol->registerRol(["rol" => $rolNombre]));
                break;
            
            case "getRolById":
                $idRol = $rol->limpiarCadena($_POST["idrol"]);
                echo json_encode($rol->getRolById(["idrol" => $idRol]));
                break;
            
            case "updateRol":
                $idRol = $rol->limpiarCadena($_POST["idrol"]);
                $rolNombre = $rol->limpiarCadena($_POST["rol"]);
                echo json_encode($rol->update(["rol" => $rolNombre]));
                break;
        }
        break;
    
    case "GET":
        if ($_GET["operation"] == "listRoles") {
            echo json_encode($rol->getAll());
        }
        break;
    
    case "DELETE":
        parse_str(file_get_contents("php://input"), $_DELETE);
        if (isset($_DELETE["idrol"])) {
            $idRol = $rol->limpiarCadena($_DELETE["idrol"]);
            echo json_encode($rol->delete(["idrol" => $idRol]));
        }
        break;
}
