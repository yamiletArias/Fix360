<?php

require_once "../models/Proveedores.php";
require_once "../models/Conexion.php";

header("Content-Type: application/json");

$proveedor = new Proveedor();
$response = ["status" => false, "message" => "Acción no válida"];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    switch ($_POST["action"]) {
        case "list":
            $response = ["status" => true, "data" => $proveedor->getAll()];
            break;

        case "add":
            if (isset($_POST["idempresa"])) {
                $idempresa = Conexion::limpiarCadena($_POST["idempresa"]);
                $response = $proveedor->add($idempresa);
            } else {
                $response = ["status" => false, "message" => "ID de empresa requerido"];
            }
            break;

        case "findById":
            if (isset($_POST["idproveedor"])) {
                $idproveedor = Conexion::limpiarCadena($_POST["idproveedor"]);
                $response = ["status" => true, "data" => $proveedor->findById($idproveedor)];
            } else {
                $response = ["status" => false, "message" => "ID de proveedor requerido"];
            }
            break;

        case "findByEmpresaId":
            if (isset($_POST["idempresa"])) {
                $idempresa = Conexion::limpiarCadena($_POST["idempresa"]);
                $response = ["status" => true, "data" => $proveedor->findByEmpresaId($idempresa)];
            } else {
                $response = ["status" => false, "message" => "ID de empresa requerido"];
            }
            break;

        case "update":
            if (isset($_POST["idproveedor"], $_POST["idempresa"])) {
                $idproveedor = Conexion::limpiarCadena($_POST["idproveedor"]);
                $idempresa = Conexion::limpiarCadena($_POST["idempresa"]);
                $response = $proveedor->update($idproveedor, $idempresa);
            } else {
                $response = ["status" => false, "message" => "Datos incompletos"];
            }
            break;

        case "delete":
            if (isset($_POST["idproveedor"])) {
                $idproveedor = Conexion::limpiarCadena($_POST["idproveedor"]);
                $response = $proveedor->delete($idproveedor);
            } else {
                $response = ["status" => false, "message" => "ID de proveedor requerido"];
            }
            break;
    }
}

echo json_encode($response);
