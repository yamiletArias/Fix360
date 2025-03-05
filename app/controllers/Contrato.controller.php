<?php

require_once "../models/ContratosModel.php";
header('Content-Type: application/json');

$contrato = new Contratos();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if (!isset($_POST["operation"])) {
            echo json_encode(["status" => false, "message" => "Operación no especificada"]);
            exit;
        }

        switch ($_POST["operation"]) {
            case "register":
                $result = $contrato->add(
                    Conexion::limpiarCadena($_POST["idrol"]),
                    Conexion::limpiarCadena($_POST["idpersona"]),
                    Conexion::limpiarCadena($_POST["fechainicio"]),
                    Conexion::limpiarCadena($_POST["fechafin"])
                );
                echo json_encode($result);
                break;

            case "update":
                $result = $contrato->update(
                    Conexion::limpiarCadena($_POST["idcontrato"]),
                    Conexion::limpiarCadena($_POST["idrol"]),
                    Conexion::limpiarCadena($_POST["idpersona"]),
                    Conexion::limpiarCadena($_POST["fechainicio"]),
                    Conexion::limpiarCadena($_POST["fechafin"])
                );
                echo json_encode($result);
                break;

            case "delete":
                echo json_encode($contrato->delete(Conexion::limpiarCadena($_POST["idcontrato"])));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET["idcontrato"])) {
            echo json_encode($contrato->find(Conexion::limpiarCadena($_GET["idcontrato"])));
        } else {
            echo json_encode($contrato->getAll());
        }
        break;
    
    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
