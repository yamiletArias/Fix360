<?php

require_once "../models/Cliente.php";

class ClientesController {
    private $model;

    public function __CONSTRUCT() {
        $this->model = new Cliente();
    }

    /**
     * Listar todos los clientes
     */
    public function getAll() {
        $response = $this->model->getAll();
        echo json_encode($response);
    }

    /**
     * Registrar un nuevo cliente
     */
    public function add() {
        $idempresa = isset($_POST['idempresa']) ? $_POST['idempresa'] : null;
        $idpersona = isset($_POST['idpersona']) ? $_POST['idpersona'] : null;
        $response = $this->model->add($idempresa, $idpersona);
        echo json_encode($response);
    }

    /**
     * Obtener un cliente por ID
     */
    public function findById() {
        if (!isset($_GET['idcliente'])) {
            echo json_encode(["status" => false, "message" => "ID de cliente requerido"]);
            return;
        }
        $idcliente = $_GET['idcliente'];
        $response = $this->model->findById($idcliente);
        echo json_encode($response);
    }

    /**
     * Obtener un cliente por ID de empresa o persona
     */
    public function findByEmpresaOPersona() {
        $idempresa = isset($_GET['idempresa']) ? $_GET['idempresa'] : null;
        $idpersona = isset($_GET['idpersona']) ? $_GET['idpersona'] : null;
        $response = $this->model->findByEmpresaOPersona($idempresa, $idpersona);
        echo json_encode($response);
    }

    /**
     * Actualizar un cliente
     */
    public function update() {
        if (!isset($_POST['idcliente'])) {
            echo json_encode(["status" => false, "message" => "ID de cliente requerido"]);
            return;
        }
        $idcliente = $_POST['idcliente'];
        $idempresa = isset($_POST['idempresa']) ? $_POST['idempresa'] : null;
        $idpersona = isset($_POST['idpersona']) ? $_POST['idpersona'] : null;
        $response = $this->model->update($idcliente, $idempresa, $idpersona);
        echo json_encode($response);
    }

    /**
     * Eliminar un cliente
     */
    public function delete() {
        if (!isset($_POST['idcliente'])) {
            echo json_encode(["status" => false, "message" => "ID de cliente requerido"]);
            return;
        }
        $idcliente = $_POST['idcliente'];
        $response = $this->model->delete($idcliente);
        echo json_encode($response);
    }
}

// Manejo de solicitudes HTTP
$controller = new ClientesController();
$action = isset($_GET['action']) ? $_GET['action'] : ''; 

switch ($action) {
    case 'getAll':
        $controller->getAll();
        break;
    case 'add':
        $controller->add();
        break;
    case 'findById':
        $controller->findById();
        break;
    case 'findByEmpresaOPersona':
        $controller->findByEmpresaOPersona();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    default:
        echo json_encode(["status" => false, "message" => "Acción no válida"]);
        break;
}
