<?php

require_once "../models/ContactabilidadModel.php";
header('Content-Type: application/json');

$contactabilidad = new Contactabilidad();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if (!isset($_POST["operation"])) {
            echo json_encode(["status" => false, "message" => "Operación no especificada"]);
            exit;
        }

        switch ($_POST["operation"]) {
            case "register":
                $result = $contactabilidad->add(
                    Conexion::limpiarCadena($_POST["contactabilidad"])
                );
                echo json_encode($result);
                break;

            case "update":
                $result = $contactabilidad->update(
                    Conexion::limpiarCadena($_POST["idcontactabilidad"]),
                    Conexion::limpiarCadena($_POST["contactabilidad"])
                );
                echo json_encode($result);
                break;

            case "delete":
                $idcontactabilidad = Conexion::limpiarCadena($_POST["idcontactabilidad"]);
                echo json_encode($contactabilidad->delete($idcontactabilidad));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET["idcontactabilidad"])) {
            $idcontactabilidad = Conexion::limpiarCadena($_GET["idcontactabilidad"]);
            echo json_encode($contactabilidad->find($idcontactabilidad));
        } else {
            echo json_encode($contactabilidad->getAll());
        }
        break;
    
    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
