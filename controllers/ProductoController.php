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

    /*    public static function crear()
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

*/

    public static function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();

            $categoriaId = $_POST['categoriaId'] ?? null;
            $categoria = Categoria::where('id', $categoriaId);

            if (!$categoria || $categoria->restauranteId !== $_SESSION['restauranteId']) {
                echo json_encode([
                    'tipo' => 'error',
                    'mensaje' => 'Categoría no válida'
                ]);
                return;
            }

            // Subir imágenes si existen
            $imagen1Nombre = null;
            $imagen2Nombre = null;

            if (!empty($_FILES['imagen1']) && $_FILES['imagen1']['error'] === UPLOAD_ERR_OK) {
                $imagen1Nombre = subirArchivoASupabase($_FILES['imagen1']);
            }

            if (!empty($_FILES['imagen2']) && $_FILES['imagen2']['error'] === UPLOAD_ERR_OK) {
                $imagen2Nombre = subirArchivoASupabase($_FILES['imagen2']);
            }

            // Crear producto con los datos
            $producto = new Producto([
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'categoriaId' => $categoriaId,
                'imagen1' => $imagen1Nombre,
                'imagen2' => $imagen2Nombre,
                'disponible' => 1, // o el valor por defecto que uses
                // ... otros campos que necesites
            ]);

            $resultado = $producto->guardar();

            if ($resultado) {
                echo json_encode([
                    'tipo' => 'exito',
                    'id' => $resultado['id'],
                    'mensaje' => 'Producto creado correctamente',
                    'categoriaId' => $categoria->id,
                    'fecha_creacion' => $producto->fecha_creacion,
                    'fecha_actualizacion' => $producto->fecha_actualizacion,
                    'imagen1' => $imagen1Nombre,
                    'imagen2' => $imagen2Nombre
                ]);
            } else {
                echo json_encode([
                    'tipo' => 'error',
                    'mensaje' => 'Error al guardar el producto'
                ]);
            }
        }
    }

    /*    public static function actualizar()
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
*/

    public static function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria = Categoria::where('id', $_POST['categoriaId'] ?? null);
            session_start();

            if (!$categoria || $categoria->restauranteId !== $_SESSION['restauranteId']) {
                echo json_encode(['respuesta' => [
                    'tipo' => 'error',
                    'mensaje' => 'Categoría no válida'
                ]]);
                return;
            }

            $productoActual = Producto::find($_POST['id'] ?? null);
            if (!$productoActual) {
                echo json_encode(['respuesta' => [
                    'tipo' => 'error',
                    'mensaje' => 'Producto no encontrado'
                ]]);
                return;
            }

            // === Manejo de imágenes ===
            $imagen1Nombre = $productoActual->imagen1;
            $imagen2Nombre = $productoActual->imagen2;

            // Imagen 1
            if (!empty($_FILES['imagen1']['name'])) {
                if (!empty($productoActual->imagen1)) {
                    eliminarArchivoDeSupabase($productoActual->imagen1);
                }
                $imagen1Nombre = subirArchivoASupabase($_FILES['imagen1']);
            }

            // Imagen 2
            if (!empty($_FILES['imagen2']['name'])) {
                if (!empty($productoActual->imagen2)) {
                    eliminarArchivoDeSupabase($productoActual->imagen2);
                }
                $imagen2Nombre = subirArchivoASupabase($_FILES['imagen2']);
            }

            // === Construir datos limpios para el modelo ===
            $datosProducto = [
                'id' => $_POST['id'],
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'disponible' => $_POST['disponible'] ?? $productoActual->disponible,
                'categoriaId' => $categoria->id,
                'imagen1' => $imagen1Nombre,
                'imagen2' => $imagen2Nombre,
                'fecha_creacion' => $productoActual->fecha_creacion,
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ];

            $producto = new Producto($datosProducto);
            $resultado = $producto->guardar();

            if ($resultado) {
                echo json_encode(['respuesta' => [
                    'tipo' => 'exito',
                    'id' => $producto->id,
                    'categoriaId' => $categoria->id,
                    'fecha_creacion' => $producto->fecha_creacion,
                    'fecha_actualizacion' => $producto->fecha_actualizacion,
                    'imagen1' => $imagen1Nombre,
                    'imagen2' => $imagen2Nombre,
                    'mensaje' => 'Producto actualizado correctamente'
                ]]);
            } else {
                echo json_encode(['respuesta' => [
                    'tipo' => 'error',
                    'mensaje' => 'Error al actualizar el producto'
                ]]);
            }
        }
    }

    /*    public static function eliminar()
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
*/

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $categoriaId = $_POST['categoriaId'] ?? null;

            if (!$id || !$categoriaId) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Datos incompletos'
                ]);
                return;
            }

            $categoria = Categoria::where('id', $categoriaId);
            session_start();

            if (!$categoria || $categoria->restauranteId !== $_SESSION['restauranteId']) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Acceso denegado'
                ]);
                return;
            }

            // Obtener el producto para tener las imágenes
            $producto = Producto::find($id);
            if (!$producto || $producto->categoriaId != $categoriaId) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Producto no encontrado'
                ]);
                return;
            }

            // ✅ Eliminar imágenes de Supabase (si existen)
            if (!empty($producto->imagen1)) {
                eliminarArchivoDeSupabase($producto->imagen1);
            }
            if (!empty($producto->imagen2)) {
                eliminarArchivoDeSupabase($producto->imagen2);
            }

            // Eliminar de la base de datos
            $resultado = $producto->eliminar(); // Asegúrate de que tu ActiveRecord tenga un método `eliminar()`

            if ($resultado) {
                echo json_encode([
                    'resultado' => true,
                    'mensaje' => 'Producto eliminado correctamente'
                ]);
            } else {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Error al eliminar el producto'
                ]);
            }
        }
    }
}
