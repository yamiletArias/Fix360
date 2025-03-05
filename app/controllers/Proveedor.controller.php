<?php

require_once "../models/ProveedoresModel.php";
header('Content-Type: application/json');

$proveedor = new Proveedores();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if (!isset($_POST["operation"])) {
            echo json_encode(["status" => false, "message" => "Operación no especificada"]);
            exit;
        }

        switch ($_POST["operation"]) {
            case "register":
                $result = $proveedor->add(
                    Conexion::limpiarCadena($_POST["idempresa"])
                );
                echo json_encode($result);
                break;

            case "update":
                $result = $proveedor->update(
                    Conexion::limpiarCadena($_POST["idproveedor"]),
                    Conexion::limpiarCadena($_POST["idempresa"])
                );
                echo json_encode($result);
                break;

            case "delete":
                $idproveedor = Conexion::limpiarCadena($_POST["idproveedor"]);
                echo json_encode($proveedor->delete($idproveedor));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET["idproveedor"])) {
            $idproveedor = Conexion::limpiarCadena($_GET["idproveedor"]);
            echo json_encode($proveedor->find($idproveedor));
        } else {
            echo json_encode($proveedor->getAll());
        }
        break;
    
    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
