<?php
session_start();
require_once "../models/Cliente.php";
header('Content-Type: application/json');

$cliente = new Cliente();

switch ($_SERVER["REQUEST_METHOD"]) {
    case 'POST':
        switch ($_POST["operation"]) {
            case 'registerCliente':
                $result = $cliente->registerCliente([
                    "tipo"              => $_POST["tipo"],
                    "nombres"           => $_POST["nombres"] ?? null,
                    "apellidos"         => $_POST["apellidos"] ?? null,
                    "tipodoc"           => $_POST["tipodoc"] ?? null,
                    "numdoc"            => $_POST["numdoc"] ?? null,
                    "direccion"         => $_POST["direccion"] ?? null,
                    "correo"            => $_POST["correo"]  ?? null,
                    "telprincipal"      => $_POST["telprincipal"] ?? null,
                    "telalternativo"    => $_POST["telalternativo"] ?? null,
                    "nomcomercial"      => $_POST["nomcomercial"] ?? null,
                    "razonsocial"       => $_POST["razonsocial"] ?? null,
                    "telefono"          => $_POST["telefono"] ?? null,
                    "ruc"               => $_POST["ruc"] ?? null,
                    "idcontactabilidad" => $_POST["idcontactabilidad"]
                ]);

                if ($result > 0) {
                    echo json_encode(["status" => true, "idcliente" => $result]);
                } else {
                    echo json_encode(["status" => false, "message" => "Error al registrar al cliente"]);
                }
                break;
        }
        break;
}
