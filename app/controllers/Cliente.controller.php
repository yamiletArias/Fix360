<?php
session_start();
require_once "../models/Cliente.php";
header('Content-Type: application/json');

$cliente = new Cliente();

switch ($_SERVER["REQUEST_METHOD"]) {
    case 'POST':
        switch ($_POST["operation"]) {
<<<<<<< HEAD
            case "register":
                $tipo = $_POST["tipo"];  // Recibimos el tipo, persona o empresa
                $idcontactabilidad = Conexion::limpiarCadena($_POST["idcontactabilidad"]);

                if ($tipo === "persona") {
                    // Registra una persona
                    $nombres = Conexion::limpiarCadena($_POST["nombres"]);
                    $apellidos = Conexion::limpiarCadena($_POST["apellidos"]);
                    $tipodoc = Conexion::limpiarCadena($_POST["tipodoc"]);
                    $numdoc = Conexion::limpiarCadena($_POST["numdoc"]);
                    $correo = Conexion::limpiarCadena($_POST["correo"]);
                    $telprincipal = Conexion::limpiarCadena($_POST["telprincipal"]);
                    $telalternativo = Conexion::limpiarCadena($_POST["telalternativo"]);
                    $direccion = Conexion::limpiarCadena($_POST["direccion"]);

                    // Aquí debes insertar la persona en la base de datos
                    $result = $cliente->addPersona($nombres, $apellidos, $tipodoc, $numdoc, $correo, $telprincipal, $telalternativo, $direccion, $idcontactabilidad);

                } else if ($tipo === "empresa") {
                    // Registra una empresa
                    $ruc = Conexion::limpiarCadena($_POST["ruc"]);
                    $nomcomercial = Conexion::limpiarCadena($_POST["nomcomercial"]);
                    $razonsocial = Conexion::limpiarCadena($_POST["razonsocial"]);
                    $telempresa = Conexion::limpiarCadena($_POST["telempresa"]);
                    $correoemp = Conexion::limpiarCadena($_POST["correoemp"]);

                    // Aquí debes insertar la empresa en la base de datos
                    $result = $cliente->addEmpresa($ruc, $nomcomercial, $razonsocial, $telempresa, $correoemp, $idcontactabilidad);
                }

                echo json_encode($result);
                break;

            // Otros casos como 'update', 'delete', etc.

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
=======
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
>>>>>>> 788210d8d451980873c4f55946267f5e1de3c09f
}
