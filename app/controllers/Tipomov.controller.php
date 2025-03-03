<?php

require_once "../models/Tipomovimiento.php";
header('Content-Type: application/json');

$tipomovimiento = new Tipomovimiento();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if (!isset($_POST["operation"])) {
            echo json_encode(["status" => false, "message" => "Operación no especificada"]);
            exit;
        }

        switch ($_POST["operation"]) {
            case "register":
                $result = $tipomovimiento->add([
                    "flujo" => Conexion::limpiarCadena($_POST["flujo"]),
                    "tipomov" => Conexion::limpiarCadena($_POST["tipomov"])
                ]);
                echo json_encode($result);
                break;

            case "update":
                $result = $tipomovimiento->update([
                    "idtipomov" => Conexion::limpiarCadena($_POST["idtipomov"]),
                    "flujo" => Conexion::limpiarCadena($_POST["flujo"]),
                    "tipomov" => Conexion::limpiarCadena($_POST["tipomov"])
                ]);
                echo json_encode($result);
                break;

            case "delete":
                $idtipomov = Conexion::limpiarCadena($_POST["idtipomov"]);
                echo json_encode($tipomovimiento->delete($idtipomov));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        echo json_encode($tipomovimiento->getAll());
        break;
    
    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
