<?php

namespace Controllers;

use Model\Pagos;

class PagoController {
    public static function actualizar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $pagoId = $_POST['id'];
            $pagoEstatus = $_POST['estatus'];

            //Validar la existencia de la orden
            $pago = Pagos::find($pagoId);

            if (!$pago) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'No se encontro el pago para esta orden'
                ];

                echo json_encode($respuesta);
                return;
            }

            $pago->estatus = $pagoEstatus;

            $resultado = $pago->guardar();

            if ($resultado) {
                $respuesta = [
                    'tipo' => 'exito',
                    'mensaje' => 'Actualizado correctamente',
                    'id' => $pago->id,
                    'estatus' => $pago->estatus
                ];

                echo json_encode($respuesta);
            }
        }
    }
}