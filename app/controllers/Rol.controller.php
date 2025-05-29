<?php

require_once "../models/Rol.php";
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
                $result = $rol->add(Helper::limpiarCadena($_POST["rol"]));
                echo json_encode($result);
                break;

            case "update":
                $result = $rol->update(
                    Helper::limpiarCadena($_POST["idrol"]),
                    Helper::limpiarCadena($_POST["rol"])
                );
                echo json_encode($result);
                break;

            case "delete":
                echo json_encode($rol->delete(Helper::limpiarCadena($_POST["idrol"])));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET["idrol"])) {
            echo json_encode($rol->find(Helper::limpiarCadena($_GET["idrol"])));
        } else {
            echo json_encode($rol->getAll());
        }
        break;
    
    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
