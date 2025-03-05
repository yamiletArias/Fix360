<?php

require_once "../models/RolesModel.php";
header('Content-Type: application/json');

$rol = new Roles();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if (!isset($_POST["operation"])) {
            echo json_encode(["status" => false, "message" => "Operación no especificada"]);
            exit;
        }

        switch ($_POST["operation"]) {
            case "register":
                $result = $rol->add(Conexion::limpiarCadena($_POST["rol"]));
                echo json_encode($result);
                break;

            case "update":
                $result = $rol->update(
                    Conexion::limpiarCadena($_POST["idrol"]),
                    Conexion::limpiarCadena($_POST["rol"])
                );
                echo json_encode($result);
                break;

            case "delete":
                echo json_encode($rol->delete(Conexion::limpiarCadena($_POST["idrol"])));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET["idrol"])) {
            echo json_encode($rol->find(Conexion::limpiarCadena($_GET["idrol"])));
        } else {
            echo json_encode($rol->getAll());
        }
        break;
    
    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
