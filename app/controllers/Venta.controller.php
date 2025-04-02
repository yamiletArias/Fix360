<?php
// Requiere la conexión a la base de datos y la clase Venta
require_once '../models/Venta.php';

if (isset($_SERVER['REQUEST_METHOD'])) {

    header('Content-Type: application/json; charset=utf-8'); 

    $venta = new Venta();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Obtener todas las ventas
            echo json_encode($venta->getAll());
            break;

            case 'POST':
                $data = json_decode(file_get_contents("php://input"), true);
            
                if (isset($data['nomcliente'], $data['tipocom'], $data['numserie'], $data['numcom'], $data['fechahora'], $data['moneda'], $data['productos'])) {
            
                    // campo idproducto sea un número y no una cadena con el nombre
                    foreach ($data['productos'] as &$producto) {
                        $producto['idproducto'] = (int)$producto['idproducto'];
                    }
            
                    try {
                        $ventaId = $venta->add($data);
                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Venta registrada correctamente',
                            'ventaId' => $ventaId
                        ]);
                    } catch (Exception $e) {
                        echo json_encode([
                            'status' => 'error',
                            'message' => $e->getMessage()
                        ]);
                    }
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Faltan datos requeridos para registrar la venta.'
                    ]);
                }
                exit(); 
    }
}
