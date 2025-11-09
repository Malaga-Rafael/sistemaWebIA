<?php

namespace Controllers;

use Model\Categoria;
use Model\Orden;
use Model\Restaurante;
use Model\Usuario;
use MVC\Router;

class DashboardController
{

    public static function index(Router $router)
    {
        //session_start();
        isAuth();

        // === Evitar caché ===
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        $restauranteId = $_SESSION['restauranteId'];

        $router->render('dashboard/index', [
            'titulo' => 'ORDENES',
            'restauranteId' => $restauranteId
        ]);
    }


    public static function existencia(Router $router)
    {

        //session_start();
        isAuth();

        $restauranteId = $_SESSION['restauranteId'];

        $categorias = Categoria::belongsTo('restauranteId', $restauranteId);

        $router->render('dashboard/existencia', [
            'titulo' => 'PRODUCTOS',
            'categorias' => $categorias
        ]);
    }

    public static function crear_categoria(Router $router)
    {
        //session_start();
        isAuth();

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $categoria = new Categoria($_POST);

            // Validar datos básicos
            $alertas = $categoria->validarCategoria();

            // Subir imagen si existe
            $imagenUrl = null;
            if (!empty($_FILES['imagen1']) && $_FILES['imagen1']['error'] === UPLOAD_ERR_OK) {
                $imagenUrl = subirArchivoASupabase($_FILES['imagen1'], 'Categorias');
                if (!$imagenUrl) {
                    $alertas[] = 'Error al subir la imagen. Inténtalo de nuevo.';
                }
            }

            if (empty($alertas)) {
                $categoria->restauranteId = $_SESSION['restauranteId'];
                $categoria->imagen = $imagenUrl; // Guardar la URL en el modelo

                $resultado = $categoria->guardar();

                if ($resultado) {
                    header('Location: /categoria?id=' . $resultado['id']);
                    exit;
                } else {
                    $alertas[] = 'Error al guardar la categoría.';
                }
            }
        }

        $router->render('dashboard/crear-categoria', [
            'alertas' => $alertas,
            'titulo' => 'AGREGAR CATEGORIA'
        ]);
    }

    public static function categoria(Router $router)
    {

        //session_start();
        isAuth();

        $id = $_GET['id'];

        if (!$id) header('Location: /dashboard');

        $categoria = Categoria::where('id', $id);


        if ($categoria->restauranteId !== $_SESSION['restauranteId']) {
            header('Location: /dashboard');
        }

        $router->render('dashboard/categoria', [
            'titulo' => $categoria->nombre
        ]);
    }

    public static function rol(Router $router)
    {
        //session_start();
        isAuth();

        $restauranteId = $_SESSION['restauranteId'];

        $router->render('dashboard/rol', [
            'titulo' => 'ROLES',
            'restauranteId' => $restauranteId
        ]);
    }

    public static function historial(Router $router) {
        //session_start();
        isAuth();

        $restauranteId = $_SESSION['restauranteId'];

        $ordenes = Orden::ordenesHistorial($restauranteId);

        //debuguear($ordenes);

        $router->render('dashboard/historial', [
            'titulo' => 'HISTORIAL',
            'ordenes' => $ordenes
        ]);
    }

    public static function perfil(Router $router)
    {
        //session_start();
        isAuth();
        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);
        $restaurante = Restaurante::find($_SESSION['restauranteId']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Asignar al usuario
            $usuario->nombre = $_POST['nombre'];
            $usuario->email = $_POST['email'];
            $usuario->telefono = $_POST['telefono'];

            //Asignar al restaurante
            $restaurante->nombre = $_POST['nombre-restaurante'];
            $restaurante->direccion = $_POST['direccion-restaurante'];
            $restaurante->email = $_POST['email-restaurante'];
            $restaurante->telefono = $_POST['telefono-restaurante'];
            $restaurante->hora_apertura = $_POST['apertura-restaurante'];
            $restaurante->hora_cierre = $_POST['cierre-restaurante'];
            $restaurante->numero_cuenta = $_POST['cuenta-restaurante'];
            $restaurante->clabe = $_POST['clabe-restaurante'];

            $alertas = $usuario->validar_perfil();
            $alertas = $restaurante->validarInformacion();

            if (empty($alertas)) {

                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    Usuario::setAlerta('error', 'Email no válido, ya pertence a otra cuenta');
                    $alertas = $usuario->getAlertas();
                } else {
                    $usuario->guardar();
                    $restaurante->guardar();

                    Usuario::setAlerta('exito', 'Guardado Correctamente');
                    $alertas = $usuario->getAlertas();

                    $_SESSION['nombre'] = $usuario->nombre;
                }


            }
        }

        $router->render('dashboard/perfil', [
            'alertas' => $alertas,
            'titulo' => 'PERFIL',
            'usuario' => $usuario,
            'restaurante' => $restaurante
        ]);
    }

    public static function cambiar_password(Router $router)
    {
        //session_start();
        isAuth();

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $usuario = Usuario::find($_SESSION['id']);

            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();

            if (empty($alertas)) {
                $resultado = $usuario->comprobar_password();

                if ($resultado) {
                    $usuario->password = $usuario->password_nuevo;

                    //Eliminacion de propiedades innecesarias
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    //Hashear el password
                    $usuario->hashPassword();

                    //Actualizar
                    $resultado = $usuario->guardar();

                    if ($resultado) {
                        Usuario::setAlerta('exito', 'Password Guardado Correctamente');
                        $alertas = $usuario->getAlertas();
                    }
                } else {
                    Usuario::setAlerta('error', 'Password Incorrecto');
                    $alertas = $usuario->getAlertas();
                }
            }
        }

        $router->render('dashboard/cambiar-password', [
            'alertas' => $alertas,
            'titulo' => 'CAMBIAR PASSWORD'

        ]);
    }
}
