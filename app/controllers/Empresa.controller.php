<?php

require_once "../models/Empresas.php";
require_once "../models/Conexion.php";

header("Content-Type: application/json");

$empresa = new Empresa();
$response = ["status" => false, "message" => "Solicitud no válida"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = isset($_POST["action"]) ? Conexion::limpiarCadena($_POST["action"]) : "";

    switch ($action) {
        case "listar":
            $response = $empresa->getAll();
            break;

        case "registrar":
            $params = [
                "razonsocial" => Conexion::limpiarCadena($_POST["razonsocial"] ?? ""),
                "telefono"    => Conexion::limpiarCadena($_POST["telefono"] ?? ""),
                "correo"      => Conexion::limpiarCadena($_POST["correo"] ?? ""),
                "ruc"         => Conexion::limpiarCadena($_POST["ruc"] ?? "")
            ];
            $response = $empresa->add($params);
            break;

        case "buscar":
            $ruc = Conexion::limpiarCadena($_POST["ruc"] ?? "");
            $response = $empresa->find($ruc);
            break;

        case "actualizar":
            $params = [
                "idempresa"   => Conexion::limpiarCadena($_POST["idempresa"] ?? ""),
                "razonsocial" => Conexion::limpiarCadena($_POST["razonsocial"] ?? ""),
                "telefono"    => Conexion::limpiarCadena($_POST["telefono"] ?? ""),
                "correo"      => Conexion::limpiarCadena($_POST["correo"] ?? ""),
                "ruc"         => Conexion::limpiarCadena($_POST["ruc"] ?? "")
            ];
            $response = $empresa->update($params);
            break;

        case "eliminar":
            $idempresa = Conexion::limpiarCadena($_POST["idempresa"] ?? "");
            $response = $empresa->delete($idempresa);
            break;
    }
}

echo json_encode($response);
