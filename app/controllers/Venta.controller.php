<?php
//Venta.controller.php /si
if (isset($_SERVER['REQUEST_METHOD'])) {

    header('Content-Type: application/json; charset=utf-8');
    date_default_timezone_set("America/Lima");

    require_once '../models/Venta.php';
    require_once __DIR__ . '/../models/sesion.php';
    require_once "../helpers/helper.php";
    require_once '../models/Cotizacion.php';
    $venta = new Venta();
    $m = new Cotizacion();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $tipo = Helper::limpiarCadena($_GET['type'] ?? "");

            // 1) Monedas
            if ($tipo == 'moneda') {
                echo json_encode($venta->getMonedasVentas());
                exit;
            }

            // 2) Productos o Clientes
            if (isset($_GET['q']) && !empty($_GET['q'])) {
                $termino = $_GET['q'];
                if ($tipo == 'producto') {
                    echo json_encode($venta->buscarProducto($termino));
                    exit;
                } else {
                    echo json_encode($venta->buscarCliente($termino));
                    exit;
                }
            }

            // 3) Listado por periodo (dia/semana/mes) usando vs_ventas
            if (isset($_GET['modo'], $_GET['fecha'])) {
                $modo = in_array($_GET['modo'], ['dia', 'semana', 'mes'], true)
                    ? $_GET['modo']
                    : 'dia';
                $fecha = $_GET['fecha'] ?: date('Y-m-d');
                $ventas = $venta->listarPorPeriodoVentas($modo, $fecha);
                echo json_encode(['status' => 'success', 'data' => $ventas]);
                exit;
            }

            // 4) Ventas eliminadas
            if (isset($_GET['action']) && $_GET['action'] === 'ventas_eliminadas') {
                $eliminadas = $venta->getVentasEliminadas();
                echo json_encode(['status' => 'success', 'data' => $eliminadas]);
                exit;
            }

            // 5) Justificación de eliminación
            if (
                isset($_GET['action'], $_GET['idventa'])
                && $_GET['action'] === 'justificacion'
            ) {
                $id = (int) $_GET['idventa'];
                try {
                    $just = $venta->getJustificacion($id);
                    if ($just !== null) {
                        echo json_encode(['status' => 'success', 'justificacion' => $just]);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'No existe justificación']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
                exit;
            }

            // Cabecera de cotización
            if (
                isset($_GET['action'], $_GET['idcotizacion'])
                && $_GET['action'] === 'getCabecera'
            ) {
                $m = new Cotizacion();
                $cab = $m->getCabeceraById((int) $_GET['idcotizacion']);
                echo json_encode($cab);
                exit;
            }

            // Detalle de cotización
            if (
                isset($_GET['action'], $_GET['idcotizacion'])
                && $_GET['action'] === 'getDetalle'
            ) {
                $m = new Cotizacion();
                $det = $m->getDetalleById((int) $_GET['idcotizacion']);
                echo json_encode($det);
                exit;
            }

            if (
                isset($_GET['action'], $_GET['idcotizacion'])
                && $_GET['action'] === 'getClienteCotizacion'
            ) {
                header('Content-Type: application/json');
                require_once __DIR__ . '/../models/Cotizacion.php';
                $m = new Cotizacion();
                $cab = $m->getCabeceraById((int) $_GET['idcotizacion']);
                echo json_encode([
                    'idcliente' => $cab['idcliente'] ?? null,
                    'cliente' => $cab['cliente'] ?? null
                ]);
                exit;
            }

            // 6) Listar todas las ventas si no se especifica nada
            echo json_encode(['status' => 'success', 'data' => $venta->getAll()]);
            exit;

        // …
case 'POST':
    // …
    $data = json_decode(file_get_contents('php://input'), true);

    // Recojo todos los campos nuevos
    $conOrden       = !empty($data['servicios']);      // si hay servicios, creamos orden
    $idpropietario  = $data['idpropietario'] ?? 0;
    $observaciones  = trim($data['observaciones'] ?? '');
    $ingresogrua    = !empty($data['ingresogrua']) ? 1 : 0;
    // Solo envío fechaingreso si la quiero distinta; si no, lo omito y el SP la iguala a fechahora
    $fechaingreso   = $data['fechaingreso'] ?? null;

    $params = [
        'conOrden'       => $conOrden,
        'idcolaborador'  => $_SESSION['login']['idcolaborador'],
        'idpropietario'  => $idpropietario,
        'idcliente'      => (int)$data['idcliente'],
        'idvehiculo'     => (int)$data['idvehiculo'],
        'kilometraje'    => $data['kilometraje'] ?? 0,
        'observaciones'  => $observaciones,
        'ingresogrua'    => $ingresogrua,
        'fechaingreso'   => $fechaingreso,          // o null
        'tipocom'        => $data['tipocom'],
        'fechahora'      => $data['fechahora'],
        'numserie'       => $data['numserie'],
        'numcom'         => $data['numcom'],
        'moneda'         => $data['moneda'],
        'productos'      => $data['productos'],
        'servicios'      => $data['servicios'] ?? [],  // array de servicios {idservicio,idmecanico,precio}
    ];

    try {
        $res = (new Venta())->registerVentasConOrden($params);
        echo json_encode([
            'status'  => 'success',
            'idventa' => $res['idventa'],
            'idorden' => $res['idorden']  // null si no hubo orden
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;


    }
}
?>