<?php

require_once "../models/Proveedor.php";
require_once "../models/Empresa.php";
require_once "../helpers/helper.php";
header('Content-Type: application/json');
$empresa = new Empresa();

$proveedor = new Proveedores();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if (!isset($_POST["operation"])) {
            echo json_encode(["status" => false, "message" => "Operación no especificada"]);
            exit;
        }

        switch ($_POST["operation"]) {
            case "registerEmpresa":
                // 1) Registrar empresa
                $empresaData = [
                    "ruc" => Helper::limpiarCadena($_POST["ruc"]),
                    "nomcomercial" => Helper::limpiarCadena($_POST["nomcomercial"]),
                    "razonsocial" => Helper::limpiarCadena($_POST["razonsocial"]),
                    "telefono" => Helper::limpiarCadena($_POST["telempresa"]),
                    "correo" => Helper::limpiarCadena($_POST["correoemp"])
                ];

                $resultEmpresa = $empresa->add($empresaData);
                if (!$resultEmpresa["status"] || !$resultEmpresa["idempresa"]) {
                    // devuelve detalle de error
                    echo json_encode($resultEmpresa);
                    exit;
                }

                // 2) Registrar proveedor con ese idempresa
                $idempresa = $resultEmpresa["idempresa"];
                $result = $proveedor->add($idempresa);

                // 3) Añadir datos para el frontend
                $result["idempresa"] = $idempresa;
                $result["nomcomercial"] = $empresaData["nomcomercial"];

                echo json_encode($result);
                break;
            /* case "register":
                $result = $proveedor->add(
                    Helper::limpiarCadena($_POST["idempresa"])
                );
                echo json_encode($result);
                break; */

            case "update":
                $result = $proveedor->update(
                    Helper::limpiarCadena($_POST["idproveedor"]),
                    Helper::limpiarCadena($_POST["idempresa"])
                );
                echo json_encode($result);
                break;

            case "delete":
                $idproveedor = Helper::limpiarCadena($_POST["idproveedor"]);
                echo json_encode($proveedor->delete($idproveedor));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET["idproveedor"])) {
            $idproveedor = Helper::limpiarCadena($_GET["idproveedor"]);
            echo json_encode($proveedor->find($idproveedor));
        } else {
            echo json_encode($proveedor->getAll());
        }
        break;

    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
