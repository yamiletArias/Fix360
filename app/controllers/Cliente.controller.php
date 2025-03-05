<?php

require_once "../models/ClientesModel.php";
header('Content-Type: application/json');

$cliente = new Clientes();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if (!isset($_POST["operation"])) {
            echo json_encode(["status" => false, "message" => "Operación no especificada"]);
            exit;
        }

        switch ($_POST["operation"]) {
            case "register":
                $result = $cliente->add(
                    isset($_POST["idempresa"]) ? Conexion::limpiarCadena($_POST["idempresa"]) : null,
                    isset($_POST["idpersona"]) ? Conexion::limpiarCadena($_POST["idpersona"]) : null,
                    Conexion::limpiarCadena($_POST["idcontactabilidad"])
                );
                echo json_encode($result);
                break;

            case "update":
                $result = $cliente->update(
                    Conexion::limpiarCadena($_POST["idcliente"]),
                    isset($_POST["idempresa"]) ? Conexion::limpiarCadena($_POST["idempresa"]) : null,
                    isset($_POST["idpersona"]) ? Conexion::limpiarCadena($_POST["idpersona"]) : null,
                    Conexion::limpiarCadena($_POST["idcontactabilidad"])
                );
                echo json_encode($result);
                break;

            case "delete":
                echo json_encode($cliente->delete(Conexion::limpiarCadena($_POST["idcliente"])));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET["idcliente"])) {
            echo json_encode($cliente->find(Conexion::limpiarCadena($_GET["idcliente"])));
        } else {
            echo json_encode($cliente->getAll());
        }
        break;
    
    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
