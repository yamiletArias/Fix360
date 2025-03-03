<?php

require_once "../models/MarcasModel.php";
header('Content-Type: application/json');

$marca = new Marcas();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if (!isset($_POST["operation"])) {
            echo json_encode(["status" => false, "message" => "Operación no especificada"]);
            exit;
        }

        switch ($_POST["operation"]) {
            case "register":
                $result = $marca->add(
                    Conexion::limpiarCadena($_POST["nombre"]),
                    Conexion::limpiarCadena($_POST["tipo"])
                );
                echo json_encode($result);
                break;

            case "update":
                $result = $marca->update(
                    Conexion::limpiarCadena($_POST["idmarca"]),
                    Conexion::limpiarCadena($_POST["nombre"]),
                    Conexion::limpiarCadena($_POST["tipo"])
                );
                echo json_encode($result);
                break;

            case "delete":
                echo json_encode($marca->delete(Conexion::limpiarCadena($_POST["idmarca"])));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET["idmarca"])) {
            echo json_encode($marca->find(Conexion::limpiarCadena($_GET["idmarca"])));
        } else {
            echo json_encode($marca->getAll());
        }
        break;
    
    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
