<?php

namespace Controllers;

use Model\Cliente;
use Model\DetalleOrden;
use Model\Direccion;
use Model\EstatusOrden;
use Model\Orden;
use Model\Producto;

class OrdenController
{
    public static function index()
    {

        session_start();
        isAuth();

        $restauranteId = $_SESSION['restauranteId'];

        // Traer las ordenes del restaurante
        $ordenes = Orden::belongsTo('restauranteId', $restauranteId);

        foreach ($ordenes as $orden) {
            $cliente = Cliente::find($orden->clienteId);
            $orden->nombreCliente = $cliente->nombre . " " . $cliente->apellido;

            $estatus = EstatusOrden::find($orden->estatusId);
            $orden->nombreEstatus = $estatus ? $estatus->nombre : 'Sin estatus';

            if (!empty($orden->direccionId)) {
                $direccion = Direccion::find($orden->direccionId);
                $orden->direccionCompleta = $direccion ? $direccion->direccion : 'Sin direccion';
                $orden->direccionReferencia = $direccion ? $direccion->referencia : 'Sin referencia';
            } else {
                $orden->direccionCompleta = 'No aplica';
            }
        }

        // header('Content-Type: application/json');
        echo json_encode(['ordenes' => $ordenes]);
    }

    public static function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $ordenId = $_POST['id'];
            $estatus = EstatusOrden::where('id', $_POST['estatusId']);
            $orden = Orden::find($ordenId);

            session_start();

            $restauranteId = $_SESSION['restauranteId'];

            if (!$orden) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Orden No Existe'
                ];
                echo json_encode(['respuesta' => $respuesta]);
                return;
            }

            if ($orden->restauranteId !== $restauranteId) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'No puedes modificar esta orden'
                ];
                echo json_encode(['respuesta' => $respuesta]);
                return;
            }

            //$orden = new Orden($_POST);
            $orden->estatusId = $estatus->id;

            $resultado = $orden->guardar();

            if ($resultado) {
                $respuesta = [
                    'tipo' => 'exito',
                    'id' => $orden->id,
                    'estatusId' => $estatus->id,
                    'mensaje' => 'Actualizado Correctamente'
                ];
                echo json_encode(['respuesta' => $respuesta]);
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

        $producto = [];

        foreach($detalles as $detalle){
            $producto = Producto::find($detalle->productoId);

            $productos[] = [
                'idProducto' => $producto->id,
                'nombreProducto' => $producto->nombre,
                'precioProducto' => $producto->precio,
                'cantidadProducto' => $detalle->cantidad
            ];
        }

        echo json_encode(['productos' => $productos]);
    }
}
