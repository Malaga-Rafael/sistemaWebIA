<?php

namespace Controllers;

use Model\Cliente;
use Model\DetalleOrden;
use Model\Direccion;
use Model\EstatusOrden;
use Model\Orden;
use Model\Pagos;
use Model\Producto;

class OrdenController
{
    public static function index()
    {

        session_start();
        isAuth();

        $restauranteId = $_SESSION['restauranteId'];

        // Traer las ordenes del restaurante
        //$ordenes = Orden::belongsTo('restauranteId', $restauranteId);

        $ordenes = Orden::ordenesConDetalles($restauranteId);

        // header('Content-Type: application/json');
        echo json_encode(['ordenes' => $ordenes]);
    }

    public static function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ordenId = $_POST['id'];
            $estatusId = $_POST['estatusId'];

            //Validar existencia de orden y estatus
            $orden = Orden::find($ordenId);
            if (!$orden) {
                echo json_encode(['respuesta' => ['tipo' => 'error', 'mensaje' => 'Orden no existe']]);
                return;
            }

            $estatus = EstatusOrden::find($estatusId);
            if (!$estatus) {
                echo json_encode(['respuesta' => ['tipo' => 'error', 'mensaje' => 'Estatus inválido']]);
                return;
            }

            session_start();
            $restauranteId = $_SESSION['restauranteId'] ?? null;

            if (!$restauranteId || $orden->restauranteId != $restauranteId) {
                echo json_encode(['respuesta' => ['tipo' => 'error', 'mensaje' => 'No puedes modificar esta orden']]);
                return;
            }

            //Asignar las nuevas actualizaciones
            $orden->estatusId = $estatusId;

            $resultado = $orden->guardar();

            if ($resultado) {
                echo json_encode([
                    'respuesta' => [
                        'tipo' => 'exito',
                        'mensaje' => 'Actualizado correctamente',
                        'id' => $orden->id,
                        'estatusId' => $orden->estatusId
                    ]
                ]);
            }
        }
    }

    public static function detalle()
    {
        session_start();
        isAuth();

        $ordenId = $_GET['id'];

        if (!$ordenId) header('Location: /dashboard');

        $orden = Orden::where('id', $ordenId);

        if (!$orden) {
            $respuesta = [
                'mensaje' => 'No hay productos para esta orden'
            ];

            echo json_encode($respuesta);
            return;
        }

        $detalles = DetalleOrden::belongsTo('ordenId', $orden->id);

        $pago = Pagos::where('ordenId', $orden->id);

        $producto = [];
        foreach ($detalles as $detalle) {
            $producto = Producto::find($detalle->productoId);

            $productos[] = [
                'idProducto' => $producto->id,
                'nombreProducto' => $producto->nombre,
                'precioProducto' => $producto->precio,
                'cantidadProducto' => $detalle->cantidad
            ];

            $respuesta = [
                'productos' => $productos,
                'pago' => $pago
            ];
        }

        echo json_encode(['respuesta' => $respuesta]);
    }
}
