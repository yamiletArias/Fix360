<?php
header("Content-Type: application/json");
require_once "../models/Cliente.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Leer datos JSON enviados desde el frontend
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "No se recibieron datos"]);
    exit;
}

$operation = $data["operation"] ?? "";

if ($operation === "registerClient") {
    $cliente = new Cliente();

    $params = [
        "tipo"             => $data["tipo"] ?? "",
        "nombres"          => $data["nombres"] ?? "",
        "apellidos"        => $data["apellidos"] ?? "",
        "tipodoc"          => $data["tipodoc"] ?? "",
        "numdoc"           => $data["numdoc"] ?? "",
        "direccion"        => $data["direccion"] ?? "",
        "correo"           => $data["correo"] ?? "",
        "telprincipal"     => $data["telprincipal"] ?? "",
        "telalternativo"   => $data["telalternativo"] ?? "",
        "nomcomercial"     => $data["nomcomercial"] ?? "",
        "razonsocial"      => $data["razonsocial"] ?? "",
        "telefono"         => $data["telefono"] ?? "",
        "ruc"              => $data["ruc"] ?? "",
        "idcontactabilidad" => $data["idcontactabilidad"] ?? 0
    ];

    // Validación básica
    if (empty($params["tipo"]) || empty($params["idcontactabilidad"])) {
        echo json_encode(["error" => "Faltan datos obligatorios"]);
        exit;
    }

    $result = $cliente->registerCliente($params);
    echo json_encode($result);
    exit;
}

// Si la operación no es válida, enviar un solo error y salir
echo json_encode(["error" => "Operación no válida"]);
exit;
?>
