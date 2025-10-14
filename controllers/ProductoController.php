<?php

namespace Controllers;

use Model\Categoria;
use Model\Producto;

class ProductoController
{
    public static function index()
    {
        $categoriaId = $_GET['id'];

        if (!$categoriaId) header('Location: /dashboard');

        $categoria = Categoria::where('id', $categoriaId);

        session_start();

        if (!$categoria || $categoria->restauranteId !== $_SESSION['restauranteId']) header('Location: /404');

        $productos = Producto::belongsTo('categoriaId', $categoria->id);

        echo json_encode(['productos' => $productos]);
    }

    public static function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            session_start();

            $categoriaId = $_POST['categoriaId'];

            $categoria = Categoria::where('id', $categoriaId);

            if (!$categoria || $categoria->restauranteId !== $_SESSION['restauranteId']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al agregar el producto'
                ];

                echo json_encode($respuesta);
                return;
            }

            //Todo bien 

            $producto = new Producto($_POST);
            $resultado = $producto->guardar();

            $respuesta = [
                'tipo' => 'exito',
                'id' => $resultado['id'],
                'mensaje' => 'Producto creado correctamente',
                'categoriaId' => $categoria->id,
                'fecha_creacion' => $producto->fecha_creacion,
                'fecha_actualizacion' => $producto->fecha_actualizacion
            ];

            echo json_encode($respuesta);
        }
    }

    public static function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Validar existencia de la categoria 

            $categoria = Categoria::where('id', $_POST['categoriaId']);

            session_start();

            if (!$categoria || $categoria->restauranteId !== $_SESSION['restauranteId']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al actualizar el producto'
                ];

                echo json_encode($respuesta);

                return;
            }

            $producto = new Producto($_POST);
            $producto->categoriaId = $categoria->id;
            $producto->fecha_actualizacion = date('Y-m-d H:i:s');

            $resultado = $producto->guardar();

            if ($resultado) {
                $respuesta = [
                    'tipo' => 'exito',
                    'id' => $producto->id,
                    'categoriaId' => $categoria->id,
                    'fecha_creacion' => $producto->fecha_creacion,
                    'fecha_actualizacion' => $producto->fecha_actualizacion,
                    'mensaje' => 'Actualizado Correctamente'
                ];

                echo json_encode(['respuesta' => $respuesta]);
            }
        }
    }

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $categoria = Categoria::where('id', $_POST['categoriaId']);

            session_start();

            if (!$categoria || $categoria->restauranteId !== $_SESSION['restauranteId']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al actualizar el producto'
                ];

                echo json_encode($respuesta);
                return;
            }

            $producto = new Producto($_POST);
            $resultado = $producto->eliminar();

            if ($resultado) [
                'resultado' => $resultado,
                'tipo' => 'exito',
                'mensaje' => 'Eliminado Correctamente'
            ];

            echo json_encode(['resultado' => $resultado]);
        }
    }
}
